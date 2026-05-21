<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluasi;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Docs;
use Google\Service\Sheets;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;

class EvaluasiController extends Controller
{
    private $spreadsheetId;

    public function __construct() {
        $this->spreadsheetId = env('GSHEET_EVALUASI_ID');
    }
    private $templateDocId = '1sRpYW-olapO4jdk_nOsWjAZKxPNVlxRfENt1mVJa5ms'; 
    private $folderId      = '10DlRRPxZDYSNkjzClbjMP_SGaKPeU_5h'; 

    /**
     * JURUS SUSHI: Inisialisasi Google Client menggunakan OAuth2 (Refresh Token)
     * Cara ini langsung memakai kuota 5TB milik akun Mas/UKM.
     */
    private function getGoogleClient()
    {
        $client = new Client();
        
        // Data diambil dari .env (Sama dengan Berita Acara)
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        
        $refreshToken = env('GOOGLE_REFRESH_TOKEN');
        
        $client->refreshToken($refreshToken);
        $client->addScope([
            Drive::DRIVE,
            Docs::DOCUMENTS,
            Sheets::SPREADSHEETS
        ]);

        return $client;
    }

    public function index()
    {
        // Cukup gunakan query biasa. Sushi akan otomatis refresh datanya.
        $query = \App\Models\Evaluasi::query();

        // Logika Personalisasi Unit
        $isKestari = (Auth::user()->role == 'superadmin' || Auth::user()->unit == 'Biro Kesekretariatan');
        if (!$isKestari) {
            $unitCode = $this->getUnitCode(Auth::user()->unit);
            $query->where('pemohon', $unitCode);
        }

        // Ambil data dan urutkan secara manual lewat Collection agar lebih aman
        $dataEvaluasi = $query->get()->sortByDesc('id');
        
        return view('evaluasi.evaluasi_index', compact('dataEvaluasi'));
    }
    
    public function create()
    {
        $proposals = Proposal::whereRaw('is_checked = true')->get();
        
        // Daftar unit sesuai mapping getUnitCode kamu
        $units = [
            'Biro Kesekretariatan',
            'Biro Keuangan',
            'Departemen Syiar Pusat',
            'Departemen Kaderisasi Pusat',
            'Departemen MedKomInfo',
            'LDF Al-Fath Fakultas Ilmu Terapan',
            'LDF Al-Fath Fakultas Informatika',
            'LDF Al-Fath Fakultas Ekonomi dan Bisnis',
            'LDF Al-Fath Fakultas Komunikasi dan Ilmu Sosial',
            'LDF Al-Fath Fakultas Industri Kreatif',
            'LDF Al-Fath Fakultas Rekayasa Industri',
            'LDF Al-Fath Fakultas Teknik Elektro',
        ];

        return view('evaluasi.evaluasi_generate', compact('proposals', 'units'));
    }

    public function generate(Request $request)
    {
        // Inisialisasi Client & Services ala Berita Acara
        $client = $this->getGoogleClient();
        $driveService = new Drive($client);
        $docsService = new Docs($client);
        $sheetsService = new Sheets($client);

        try {
            // 1. Logika Penamaan & Periode
            $unitFullName = $request->dept_fakultas;
            $kodeUnit = $this->getUnitCode($unitFullName);
            $tglFile = date('dmY', strtotime($request->tgl_evaluasi));
            $prokerClean = strtoupper(preg_replace("/[^A-Z0-9]/", "", $request->nama_proker));
            $newFileName = "{$tglFile}_{$prokerClean}_{$kodeUnit}";
            
            $tglMulai = date('d M Y', strtotime($request->tgl_mulai));
            $tglSelesai = date('d M Y', strtotime($request->tgl_selesai));
            $periode = ($tglMulai == $tglSelesai) ? $tglMulai : "$tglMulai - $tglSelesai";

            // 2. Copy Template (Tanpa Transfer Ownership karena sudah pakai kuota Mas)
            $copyMetadata = new Drive\DriveFile([
                'name' => $newFileName,
                'parents' => [$this->folderId] 
            ]);
            
            // Tambahkan supportsAllDrives untuk fleksibilitas
            $driveFile = $driveService->files->copy($this->templateDocId, $copyMetadata, [
                'supportsAllDrives' => true,
                'fields' => 'id'
            ]);
            $newFileId = $driveFile->id;

            // 3. Update Isi Dokumen (Batch Update)
            $replacements = [
                '[Nama Kegiatan]' => strtoupper($request->nama_proker),
                '[Nama Departemen / Fakultas Penyelenggara]' => strtoupper($unitFullName),
                '[KM/Non-KM]' => $request->kategori,
                '[tgl Mulai Acara] - [tgl Akhir Acara]' => $periode,
                '[Lokasi Kegiatan]' => strtoupper($request->tempat_kegiatan ?? '-'),
                '[Tempat Evaluasi]' => strtoupper($request->tempat_evaluasi),
                '[Nama Lengkap Pimpinan Evaluasi]' => strtoupper($request->pimpinan_evaluasi),
                '[Nama Lengkap Ketua Pelaksana]' => strtoupper($request->ketuplak),
                '[Nama Lengkap Sekretaris Proker]' => strtoupper($request->sekre_proker),
            ];

            $docsRequests = [];
            foreach ($replacements as $key => $value) {
                $docsRequests[] = new Docs\Request([
                    'replaceAllText' => [
                        'containsText' => ['text' => $key, 'matchCase' => false],
                        'replaceText' => (string)($value ?? '-'),
                    ],
                ]);
            }

            $docsService->documents->batchUpdate($newFileId, new Docs\BatchUpdateDocumentRequest([
                'requests' => $docsRequests
            ]));

            // 4. Data untuk Database & Link
            $docLink = "https://docs.google.com/document/d/" . $newFileId . "/edit";
            $idEval = 'EVL-' . strtoupper(Str::random(5));

            $evalData = [
                'id_eval'     => $idEval,
                'tgl_rapat'   => date('d/m/Y', strtotime($request->tgl_evaluasi)),
                'kategori'    => $request->kategori,
                'nama_proker' => strtoupper($request->nama_proker),
                'link_doc'    => $docLink,
                'pemohon'     => $kodeUnit,
                'status'      => false 
            ];

            try {
                // SYNC KE CLOUD (Spreadsheet adalah Database Utama via Sushi)
                $rowValues = [[
                    $idEval, 
                    $evalData['tgl_rapat'], 
                    $request->kategori, 
                    $evalData['nama_proker'], 
                    $docLink, 
                    $kodeUnit, 
                    'FALSE',
                    strtoupper($request->tempat_kegiatan)
                ]];
                $valueRange = new Sheets\ValueRange(['values' => $rowValues]);
                $sheetsService->spreadsheets_values->append($this->spreadsheetId, 'Evaluasi_db!A2', $valueRange, [
                    'valueInputOption' => 'RAW'
                ]);

                return redirect()->route('evaluasi.index')->with('success', "Alhamdulillah! Evaluasi berhasil dibuat dan tersinkron ke Spreadsheet.");

            } catch (\Exception $e) {
                return redirect()->route('evaluasi.index')->with('error', "Google Docs berhasil dibuat, tapi gagal sinkron ke Sheets: " . $e->getMessage());
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses Evaluasi: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'superadmin') return back();
        try {
            $eval = Evaluasi::findOrFail($id);
            $this->deleteFromSheets($eval->id_eval, 'Evaluasi_db');
            $eval->delete();
            return back()->with('success', 'Arsip Evaluasi Berhasil Dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Hapus Cloud');
        }
    }

    // --- PRIVATE HELPERS ---

    private function getUnitCode($namaUnit)
    {
        $map = [
            'Inti' => 'INTI',
            'Biro Kesekretariatan' => 'KES',
            'Biro Keuangan' => 'KEU',
            'Departemen Syiar Pusat' => 'SYR',
            'Departemen Kaderisasi Pusat' => 'KDR',
            'Departemen MedKomInfo' => 'MDK',
            'LDF Al-Fath Fakultas Ilmu Terapan' => 'FIT',
            'LDF Al-Fath Fakultas Informatika' => 'FIF',
            'LDF Al-Fath Fakultas Ekonomi dan Bisnis' => 'FEB',
            'LDF Al-Fath Fakultas Komunikasi dan Ilmu Sosial' => 'FKS',
            'LDF Al-Fath Fakultas Industri Kreatif' => 'FIK',
            'LDF Al-Fath Fakultas Rekayasa Industri' => 'FRI',
            'LDF Al-Fath Fakultas Teknik Elektro' => 'FTE',
        ];
        return $map[$namaUnit] ?? 'UNIT';
    }

    private function deleteFromSheets($idValue, $sheetName)
    {
        $client = $this->getGoogleClient();
        $service = new Sheets($client);
        
        $rows = $service->spreadsheets_values->get($this->spreadsheetId, "$sheetName!A:A")->getValues();
        if ($rows) {
            foreach ($rows as $index => $row) {
                if (($row[0] ?? '') == $idValue) {
                    $batchUpdate = new BatchUpdateSpreadsheetRequest([
                        'requests' => [['deleteDimension' => ['range' => ['sheetId' => env('GID_EVALUASI', 0), 'dimension' => 'ROWS', 'startIndex' => $index, 'endIndex' => $index + 1]]]]
                    ]);
                    $service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdate);
                    break;
                }
            }
        }
    }
}