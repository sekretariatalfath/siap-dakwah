<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Illuminate\Support\Facades\Auth;
use App\Models\Surat;

class SuratController extends Controller
{
    private $spreadsheetId;

    public function __construct() {
        $this->spreadsheetId = env('GSHEET_SURAT_KELUAR_ID');
    } 

    /**
     * TAMPILAN BUAT SURAT: Riwayat diambil langsung dari SPS
     */
    public function create() {
        $service = app(\App\Services\GoogleSheetService::class)->getService();
        $response = $service->spreadsheets_values->get($this->spreadsheetId, 'Surat_db!A:R');
        $rows = $response->getValues() ?? [];

        // Hapus header, ambil 10 data terakhir, lalu balik urutannya (terbaru di atas)
        $riwayat = collect($rows)->forget(0)->reverse()->take(10)->map(function($row) {
            return (object) [
                'no_surat_full' => $row[0] ?? '-',
                'perihal'       => $row[13] ?? '-',
                'jenis'         => $row[1] ?? '-',
            ];
        });

        $bulanRomawi = $this->intToRoman(date('n'));
        $spreadsheetId = $this->spreadsheetId;
        
        return view('dashboard.surat_create', compact('riwayat', 'bulanRomawi', 'spreadsheetId'));
    }

    /**
     * SIMPAN SURAT: Logika urutan Global & Tipe tetap sinkron ke SPS
     */
    public function store(Request $request) {
        $request->validate([
            'jenis' => 'required',
            'lingkup' => 'required',
            'jumlah' => 'required|numeric|min:1',
            'perihal' => 'required|string',
            'penyelenggara' => 'required|string',
        ]);

        $service = app(\App\Services\GoogleSheetService::class)->getService();
        $jenis = $request->jenis;
        $tahun = date('Y');
        $bulanRomawi = $this->intToRoman(date('n'));

        try {
            $response = $service->spreadsheets_values->get($this->spreadsheetId, 'Surat_db!A:R');
            $rows = $response->getValues() ?? [];

            $maxGlobal = 0;
            $maxTypeUrutan = 0;

            foreach ($rows as $index => $row) {
                if ($index === 0) continue; 
                $maxGlobal = max($maxGlobal, (int)($row[10] ?? 0));
                if (($row[1] ?? '') === $jenis && ($row[12] ?? '') == $tahun) {
                    preg_match('/-(\d+)/', $row[0] ?? '', $matches);
                    $maxTypeUrutan = max($maxTypeUrutan, isset($matches[1]) ? (int)$matches[1] : 0);
                }
            }

            $nextGlobal = $maxGlobal + 1;
            $nextType = $maxTypeUrutan + 1;
            $batchId = time();
            $dataToSheets = [];      
            $generatedNumbers = [];

            for ($i = 0; $i < (int)$request->jumlah; $i++) {
                $currentGlobal = $nextGlobal + $i;
                $currentType = $nextType + $i;
                $nomor3Digit = str_pad($currentType, 3, '0', STR_PAD_LEFT);
                $middlePart = $this->generateMiddlePart($request);
                
                $finalNomor = ($middlePart == "") 
                    ? "{$jenis}-{$nomor3Digit}/ALFATH-UNITEL/{$bulanRomawi}/{$tahun}" 
                    : "{$jenis}-{$nomor3Digit}/{$middlePart}/ALFATH-UNITEL/{$bulanRomawi}/{$tahun}";

                $detail = ($request->lingkup == 'PUSAT' ? ($request->kategori_pusat ?? 'UMUM') : ($request->kategori_fakultas ?? 'UMUM'));
                $nama_acara = $request->nama_acara ?? '-';
                $fakultas = $request->kode_fakultas ?? '-';
                $bidang = $request->kode_bidang ?? '-';
                $nama_pengisi = $request->nama_pengisi ?? Auth::user()->name;
                
                // --- PERBAIKAN: SIMPAN KE SQLITE ---
                Surat::create([
                    'no_surat_full' => $finalNomor,
                    'jenis' => $jenis,
                    'lingkup' => $request->lingkup,
                    'detail' => $detail,
                    'nama_acara' => $nama_acara,
                    'fakultas' => $fakultas,
                    'bidang_fakultas' => $bidang,
                    'asal_pengisi' => Auth::user()->unit,
                    'nama_pengisi' => $nama_pengisi,
                    'tanggal' => date('Y-m-d'),
                    'nomor_urut' => $currentType,
                    'bulan_romawi' => $bulanRomawi,
                    'tahun' => $tahun,
                    'perihal' => $request->perihal,
                    'penyelenggara' => strtoupper($request->penyelenggara),
                    'catatan' => $request->catatan ?? '',
                    'link_drive' => '-',
                    'batch_id' => $batchId
                ]);

                $generatedNumbers[] = $finalNomor;
                $dataToSheets[] = [
                    $finalNomor, $jenis, $request->lingkup, 
                    $detail,
                    $nama_acara, $fakultas, $bidang,
                    Auth::user()->unit, $nama_pengisi, date('d/m/Y H:i'),
                    $currentGlobal, $bulanRomawi, $tahun, $request->perihal, strtoupper($request->penyelenggara),
                    $request->catatan ?? '', '-', $batchId
                ];
            }

            $body = new ValueRange(['values' => $dataToSheets]);
            $service->spreadsheets_values->append($this->spreadsheetId, 'Surat_db!A2', $body, ['valueInputOption' => 'USER_ENTERED']);
            
            return back()->with('success', true)->with('last_number', end($generatedNumbers))->with('generated_list', $generatedNumbers);
        } catch (\Exception $e) {
            return back()->with('error', "Gagal: " . $e->getMessage());
        }
    }

    /**
     * ARSIP SURAT: Filter & Grouping dilakukan langsung dari data SPS
     */
    public function arsipList() {
        $user = Auth::user();
        $service = app(\App\Services\GoogleSheetService::class)->getService();
        $response = $service->spreadsheets_values->get($this->spreadsheetId, 'Surat_db!A:R');
        $rows = $response->getValues() ?? [];

        // Mapping rows ke Collection agar bisa di-filter dan di-group
        $groups = collect($rows)->forget(0)->map(function($row) {
            return (object) [
                'no_surat_full' => $row[0] ?? '',
                'asal_pengisi'  => $row[7] ?? '',
                'perihal'       => $row[13] ?? '',
                'batch_id'      => $row[17] ?? '',
                'link_drive'    => $row[16] ?? '-',
                'tanggal'       => $row[9] ?? '-',
            ];
        })->filter(function($s) use ($user) {
            // Filter unit (Admin Unit cuma liat unitnya, Superadmin dan Kestari liat semua)
            $isKestari = ($user->role == 'superadmin' || $user->unit == 'Biro Kesekretariatan');
            return $isKestari ? true : trim($s->asal_pengisi) == trim($user->unit);
        })->groupBy('batch_id')->map(function ($batch) {
            return $batch->groupBy('perihal');
        })->reverse(); // Urutan terbaru di atas

        return view('dashboard.surat_arsip', compact('groups'));
    }

    /**
     * UPDATE ARSIP: Mencari baris yang tepat di SPS untuk update Link Drive
     */
    public function updateArsip(Request $request) {
        try {
            $batchId = (string) trim($request->batch_id);
            $perihal = (string) trim($request->perihal);
            $link = trim($request->link_drive);

            $service = app(\App\Services\GoogleSheetService::class)->getService();
            $response = $service->spreadsheets_values->get($this->spreadsheetId, 'Surat_db!A:R');
            $rows = $response->getValues() ?? [];
            
            $dataUpdate = [];
            foreach ($rows as $index => $row) {
                // Baris cocok jika Batch ID (Index 17) & Perihal (Index 13) sama
                if (($row[17] ?? '') == $batchId && ($row[13] ?? '') == $perihal) {
                    $rowNum = $index + 1;
                    $dataUpdate[] = [
                        'range' => "Surat_db!Q{$rowNum}",
                        'values' => [[$link]]
                    ];
                }
            }

            if (count($dataUpdate) > 0) {
                $batchRequest = new \Google\Service\Sheets\BatchUpdateValuesRequest([
                    'valueInputOption' => 'USER_ENTERED',
                    'data' => $dataUpdate
                ]);
                $service->spreadsheets_values->batchUpdate($this->spreadsheetId, $batchRequest);
                
                // --- PERBAIKAN: UPDATE SQLITE JUGA ---
                Surat::where('batch_id', $batchId)
                     ->where('perihal', $perihal)
                     ->update(['link_drive' => $link]);

                return redirect()->route('surat.arsip')->with('success', 'Arsip berhasil diperbarui secara Lokal dan Cloud.');
            }

            return back()->with('error', 'Data tidak ditemukan di Cloud.');
        } catch (\Exception $e) {
            return back()->with('error', 'API Error: ' . $e->getMessage());
        }
    }

    private function generateMiddlePart($request) {
        $kategori = ($request->lingkup == 'PUSAT') ? $request->kategori_pusat : $request->kategori_fakultas;
        if ($request->lingkup == 'PUSAT') {
            if ($kategori == 'MSP') return "MSP";
            if ($kategori == 'PROKER_NON_KM') return $request->kode_departemen;
            if ($kategori == 'PROKER_KM') return preg_replace("/[^A-Z0-9]/", "", strtoupper($request->nama_acara));
            return "";
        } else {
            $fak = $request->kode_fakultas;
            if ($kategori == 'UMUM') return $fak;
            if ($kategori == 'PROKER_NON_KM') return $fak . "/" . ($request->kode_bidang ?? '00');
            if ($kategori == 'PROKER_KM') return preg_replace("/[^A-Z0-9]/", "", strtoupper($request->nama_acara)) . "/" . $fak;
            return $fak;
        }
    }

    private function intToRoman($number) {
        $map = array('XII' => 12, 'XI' => 11, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        foreach ($map as $roman => $int) {
            while ($number >= $int) { $returnValue .= $roman; $number -= $int; }
        }
        return $returnValue;
    }
}