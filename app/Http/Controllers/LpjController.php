<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proposal;
use App\Models\Lpj;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;

class LpjController extends Controller
{
    // ID Spreadsheet Database LPJ
    private $spreadsheetId;

    public function __construct() {
        $this->spreadsheetId = env('GSHEET_LPJ_ID');
    }

    /**
     * Menampilkan daftar arsip LPJ.
     */
    public function index()
    {
        $user = Auth::user();
        $service = app(\App\Services\GoogleSheetService::class)->getService();
        $response = $service->spreadsheets_values->get($this->spreadsheetId, 'Lpj_db!A2:M');
        $rows = $response->getValues() ?? [];

        // Map data dari Sheets (Abaikan baris kosong)
        $dataLpj = collect($rows)->filter(fn($r) => !empty($r[0]))->map(function($row) {
            return (object) [
                'id_lpj'              => $row[0] ?? '-',
                'tgl_input'           => $row[1] ?? '-',
                'nama_proker'         => $row[2] ?? '-',
                'pemohon'             => $row[3] ?? '-',
                'realisasi_peserta'   => (int)($row[4] ?? 0),
                'ketercapaian_tujuan' => $row[5] ?? '-',
                'realisasi_sasaran'   => $row[6] ?? '-',
                'anggaran_sponsor'    => (int)($row[7] ?? 0),
                'realisasi_anggaran'  => (int)($row[8] ?? 0),
                'link_lpj_pdf'        => $row[9] ?? '#',
                'link_dokumentasi'    => $row[10] ?? '#',
                'link_evaluasi'       => $row[11] ?? '#',
                'is_checked'          => strtoupper($row[12] ?? 'FALSE') == 'TRUE'
            ];
        })->filter(function($l) use ($user) {
            $isKestari = ($user->role == 'superadmin' || $user->unit == 'Biro Kesekretariatan');
            return $isKestari ? true : $l->pemohon == $user->unit;
        })->reverse();

        $proposals = Proposal::whereRaw('is_checked = true')->get();

        return view('lpj.lpj_index', compact('dataLpj', 'proposals'));
    }

    /**
     * Menyimpan data realisasi LPJ baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_proker'         => 'required',
            'realisasi_peserta'   => 'required|numeric',
            'ketercapaian_tujuan' => 'required',
            'realisasi_sasaran'   => 'required',
            'realisasi_anggaran'  => 'required|numeric',
            'link_lpj_pdf'        => 'required|url',
            'link_evaluasi'       => 'required|url',
            'link_dokumentasi'    => 'required|url',
        ]);

        $user = Auth::user();
        $lpjId = 'LPJ-' . strtoupper(Str::random(5));

        // 1. Simpan ke Database Lokal (SQLite)
        $lpj = Lpj::create([
            'id_lpj'              => $lpjId,
            'tgl_input'           => now()->format('d/m/Y H:i'),
            'nama_proker'         => $request->nama_proker,
            'pemohon'             => $user->unit,
            'realisasi_peserta'   => (int)$request->realisasi_peserta,
            'ketercapaian_tujuan' => $request->ketercapaian_tujuan,
            'realisasi_sasaran'   => $request->realisasi_sasaran,
            'anggaran_sponsor'    => (int)$request->anggaran_sponsor ?? 0,
            'realisasi_anggaran'  => (int)$request->realisasi_anggaran,
            'link_lpj_pdf'        => $request->link_lpj_pdf,
            'link_dokumentasi'    => $request->link_dokumentasi,
            'link_evaluasi'       => $request->link_evaluasi
        ]);

        // 2. Sync ke Google Sheets (Cloud)
        try {
            $this->syncToSheets($lpj);

            return back()->with('success', 'LPJ Berhasil Diarsipkan & Sinkron ke Cloud!');
        } catch (\Exception $e) {
            // Notifikasi jika Cloud gagal tapi Lokal berhasil[cite: 15]
            return back()->withInput()->with('error', 'Arsip lokal berhasil, namun gagal sinkron ke Spreadsheet: ' . $e->getMessage());
        }
    }

    /**
     * Verifikasi LPJ (Khusus Superadmin).
     */
    public function verify($id_lpj)
    {
        if (Auth::user()->role !== 'superadmin') return back();

        try {
            $service = app(\App\Services\GoogleSheetService::class)->getService();
            $rows = $service->spreadsheets_values->get($this->spreadsheetId, 'Lpj_db!A:M')->getValues();

            $targetRow = null;
            if ($rows) {
                foreach ($rows as $index => $row) {
                    if (($row[0] ?? '') == $id_lpj) {
                        $targetRow = $index + 1;
                        break;
                    }
                }
            }

            if (!$targetRow) throw new \Exception("Data tidak ditemukan!");

            $body = new ValueRange(['values' => [['TRUE']]]);
            $service->spreadsheets_values->update($this->spreadsheetId, "Lpj_db!M$targetRow", $body, ['valueInputOption' => 'RAW']);

            // Update status lokal
            Lpj::where('id_lpj', $id_lpj)->update(['is_checked' => \Illuminate\Support\Facades\DB::raw('true')]);

            return back()->with('success', 'LPJ Berhasil Diverifikasi!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus arsip LPJ (Lokal & Cloud).
     */
    public function destroy($id_lpj)
    {
        if (Auth::user()->role !== 'superadmin') {
            return back()->with('error', 'Akses ditolak!');
        }

        try {
            // 1. Hapus dari Google Sheets
            $this->deleteFromSheets($id_lpj);

            // 2. Hapus dari SQLite
            Lpj::where('id_lpj', $id_lpj)->delete();

            return back()->with('success', "Arsip LPJ Berhasil Dihapus!");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // PRIVATE HELPERS (GOOGLE SHEETS LOGIC)
    // =========================================================================

    /**
     * Kirim data baru ke baris terakhir Google Sheets.
     */
    private function syncToSheets($lpj)
    {
        $service = app(\App\Services\GoogleSheetService::class)->getService();
        
        $values = [[
            $lpj->id_lpj,              // A: ID_LPJ
            $lpj->tgl_input,           // B: TANGGAL INPUT
            $lpj->nama_proker,         // C: NAMA PROKER
            $lpj->pemohon,             // D: PEMOHON
            $lpj->realisasi_peserta,   // E: REALISASI PESERTA
            $lpj->ketercapaian_tujuan, // F: KETERCAPAIAN TUJUAN
            $lpj->realisasi_sasaran,   // G: REALISASI SASARAN
            $lpj->anggaran_sponsor,    // H: ANGGARAN SPONSOR
            $lpj->realisasi_anggaran,  // I: REALISASI ANGGARAN
            $lpj->link_lpj_pdf,        // J: LINK LPJ PDF
            $lpj->link_dokumentasi,    // K: LINK DOKUMENTASI
            $lpj->link_evaluasi,       // L: LINK EVALUASI
            'FALSE'                    // M: STATUS
        ]];

        $body = new ValueRange(['values' => $values]);
        $service->spreadsheets_values->append(
            $this->spreadsheetId, 
            'Lpj_db!A2:M', 
            $body, 
            ['valueInputOption' => 'RAW']
        );
    }

    /**
     * Update status verifikasi di Google Sheets (Kolom M).
     */
    private function updateStatusInSheets($lpj)
    {
        $service = app(\App\Services\GoogleSheetService::class)->getService();
        $rows = $service->spreadsheets_values->get($this->spreadsheetId, 'Lpj_db!A:A')->getValues();

        if ($rows) {
            foreach ($rows as $index => $row) {
                if (($row[0] ?? '') == $lpj->id_lpj) {
                    $rowNum = $index + 1;
                    $body = new ValueRange(['values' => [['TRUE']]]);
                    $service->spreadsheets_values->update(
                        $this->spreadsheetId, 
                        "Lpj_db!M{$rowNum}", 
                        $body, 
                        ['valueInputOption' => 'RAW']
                    );
                    break;
                }
            }
        }
    }

    /**
     * Menghapus baris berdasarkan ID_LPJ di Google Sheets.
     */
    private function deleteFromSheets($lpjId)
    {
        $service = app(\App\Services\GoogleSheetService::class)->getService();
        $rows = $service->spreadsheets_values->get($this->spreadsheetId, 'Lpj_db!A:A')->getValues();

        if ($rows) {
            foreach ($rows as $index => $row) {
                if (($row[0] ?? '') == $lpjId) {
                    $rowNum = $index; 
                    
                    $batchUpdate = new BatchUpdateSpreadsheetRequest([
                        'requests' => [
                            'deleteDimension' => [
                                'range' => [
                                    'sheetId'    => env('GID_LPJ', 0), // GID tab Lpj_db
                                    'dimension'  => 'ROWS',
                                    'startIndex' => $rowNum,
                                    'endIndex'   => $rowNum + 1
                                ]
                            ]
                        ]
                    ]);
                    $service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdate);
                    break;
                }
            }
        }
    }

}