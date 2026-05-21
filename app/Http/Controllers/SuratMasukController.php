<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratMasuk;
use Illuminate\Support\Facades\Auth;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;

class SuratMasukController extends Controller
{
    // ID Spreadsheet sesuai screenshot Mas
    private $spreadsheetId;

    public function __construct() {
        $this->spreadsheetId = env('GSHEET_SURAT_MASUK_ID');
    }

    public function index()
    {
        $service = app(\App\Services\GoogleSheetService::class)->getService();
        $response = $service->spreadsheets_values->get($this->spreadsheetId, "'SuratMasuk'!A:L");
        $rows = $response->getValues() ?? [];

        // Hapus header, abaikan baris kosong, dan map data
        $suratMasuk = collect($rows)->forget(0)->filter(fn($r) => !empty($r[0]))->map(function($row) {
            return (object) [
                'no_surat'         => $row[0] ?? '-',
                'pengirim'         => $row[1] ?? '-',
                'jenis_kontak'     => $row[2] ?? '-',
                'detail_kontak'    => $row[3] ?? '-',
                'perihal'          => $row[4] ?? '-',
                'nama_kegiatan'    => $row[5] ?? null,
                'ditujukan_kepada' => $row[6] ?? '-',
                'tgl_terima'       => \Carbon\Carbon::parse($row[7] ?? now()),
                'penerima_fisik'   => $row[8] ?? '-',
                'link_drive'       => $row[9] ?? '#',
                'uploader'         => $row[10] ?? '-',
                'is_checked'       => ($row[11] ?? '0') == '1'
            ];
        })->reverse();

        return view('dashboard.surat_masuk', compact('suratMasuk'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_surat'       => 'required|min:5',
            'pengirim'       => 'required|string|max:100',
            'perihal'        => 'required|min:10',
            'tgl_terima'     => 'required|date',
            'penerima_fisik' => 'required',
            'link_drive'     => ['required', 'url', 'regex:/drive\.google\.com/'],
            'jenis_kontak'   => 'required|in:WHATSAPP,EMAIL,KEDUANYA,TIDAK ADA',
        ]);

        $detailKontak = '-';
        if ($request->jenis_kontak == 'EMAIL') $detailKontak = $request->input_email;
        elseif ($request->jenis_kontak == 'WHATSAPP') $detailKontak = $request->input_wa;
        elseif ($request->jenis_kontak == 'KEDUANYA') $detailKontak = $request->input_email . ' | ' . $request->input_wa;

        $data = [
            'no_surat'         => strtoupper($request->no_surat),
            'pengirim'         => strtoupper($request->pengirim),
            'jenis_kontak'     => strtoupper($request->jenis_kontak),
            'detail_kontak'    => $detailKontak,
            'perihal'          => $request->perihal,
            'nama_kegiatan'    => strtoupper($request->nama_kegiatan ?? '-'),
            'ditujukan_kepada' => strtoupper($request->ditujukan_kepada ?? '-'),
            'tgl_terima'       => $request->tgl_terima,
            'penerima_fisik'   => strtoupper($request->penerima_fisik),
            'link_drive'       => $request->link_drive,
            'uploader'         => Auth::user()->name . ' (' . Auth::user()->unit . ')'
        ];

        try {
            // 1. SIMPAN KE LOKAL DULU (Kebenaran Utama)[cite: 15]
            $surat = SuratMasuk::create($data);

            // 2. JIKA BERHASIL, BARU KIRIM KE CLOUD (Backup)[cite: 15]
            $this->syncToSheets((object)$data, $detailKontak);

            return back()->with('success', 'Arsip Berhasil Disimpan & Sinkron ke Cloud!');
        } catch (\Exception $e) {
            // Jika Cloud gagal, data di lokal sudah aman
            return back()->withInput()->with('error', 'Data tersimpan di Web, tapi gagal sinkron ke Cloud: ' . $e->getMessage());
        }
    }

    public function toggleCheck($no_surat)
    {
        try {
            $service = app(\App\Services\GoogleSheetService::class)->getService();
            $rows = $service->spreadsheets_values->get($this->spreadsheetId, "'SuratMasuk'!A:L")->getValues();
            
            $targetRow = null;
            $currentStatus = '0';

            if ($rows) {
                foreach ($rows as $index => $row) {
                    if (isset($row[0]) && trim($row[0]) == trim($no_surat)) {
                        $targetRow = $index + 1;
                        $currentStatus = $row[11] ?? '0';
                        break;
                    }
                }
            }

            if (!$targetRow) throw new \Exception("Data tidak ditemukan di Spreadsheet!");

            $newStatus = ($currentStatus == '1') ? '0' : '1';
            $body = new ValueRange(['values' => [[$newStatus]]]);
            $service->spreadsheets_values->update(
                $this->spreadsheetId, 
                "'SuratMasuk'!L{$targetRow}", 
                $body, 
                ['valueInputOption' => 'RAW']
            );

            // Update Lokal jika ada (Opsional, agar sinkron jika local dipanggil)
            SuratMasuk::where('no_surat', $no_surat)->update(['is_checked' => \Illuminate\Support\Facades\DB::raw($newStatus == '1' ? 'true' : 'false')]);
            \Illuminate\Support\Facades\Cache::flush();

            return back()->with('success', 'Status Verifikasi Berhasil Diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function destroy($no_surat)
    {
        if (Auth::user()->role !== 'superadmin') {
            return back()->with('error', 'Hanya Super Admin yang bisa menghapus.');
        }

        try {
            // 1. Hapus di Spreadsheet
            $this->deleteFromSheets($no_surat, 'SuratMasuk');

            // 2. Hapus di SQLite
            SuratMasuk::where('no_surat', $no_surat)->delete();

            return back()->with('success', 'Data dihapus dari Web & Cloud!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Hapus Cloud: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function syncToSheets($surat, $detailKontak)
    {
        $service = app(\App\Services\GoogleSheetService::class)->getService();
        $dataToInsert = [[
            $surat->no_surat,
            $surat->pengirim,
            $surat->jenis_kontak,
            $detailKontak,
            $surat->perihal,
            $surat->nama_kegiatan,
            $surat->ditujukan_kepada,
            is_string($surat->tgl_terima) ? $surat->tgl_terima : $surat->tgl_terima->format('Y-m-d'),
            $surat->penerima_fisik,
            $surat->link_drive,
            $surat->uploader,
            '0' // Status default (Kolom L)
        ]];

        $body = new ValueRange(['values' => $dataToInsert]);
        $service->spreadsheets_values->append(
            $this->spreadsheetId, 
            'SuratMasuk!A1', 
            $body, 
            ['valueInputOption' => 'USER_ENTERED'] // Agar format tanggal/angka benar
        );
    }

    private function deleteFromSheets($noSurat, $sheetName)
{
    $service = app(\App\Services\GoogleSheetService::class)->getService();
    // Gunakan kutip ' ' untuk nama sheet
    $rows = $service->spreadsheets_values->get($this->spreadsheetId, "'$sheetName'!A:A")->getValues();
    
    if ($rows) {
        foreach ($rows as $index => $row) {
            if (isset($row[0]) && trim($row[0]) == trim($noSurat)) {
                $batchUpdate = new BatchUpdateSpreadsheetRequest([
                    'requests' => [['deleteDimension' => ['range' => [
                        // PENTING: Buka Google Sheets Mas, lihat di URL bagian gid=... 
                        // Jika gid=0 maka tetap 0, jika gid=12345 maka ganti 12345
                        'sheetId'    => env('GID_SURAT_MASUK', 0), 
                        'dimension'  => 'ROWS',
                        'startIndex' => $index,
                        'endIndex'   => $index + 1
                    ]]]]
                ]);
                $service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdate);
                break;
            }
        }
    }
}
}