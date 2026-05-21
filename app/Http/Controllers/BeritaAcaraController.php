<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Docs;
use Google\Service\Sheets;
use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class BeritaAcaraController extends Controller
{
    /**
     * Fungsi Helper untuk inisialisasi Google Client.
     * Menggunakan data dari .env agar lebih aman dan rapi.
     */
    private function getGoogleClient()
    {
        $client = new Client();
        // Disarankan simpan di .env: GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_REFRESH_TOKEN
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

    // app/Http/Controllers/BeritaAcaraController.php

    public function create()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        try {
            // Panggil query langsung dari Model Sushi
            // Sushi akan otomatis mengurus pembuatan tabel di memori
            $riwayatCloud = \App\Models\BeritaAcara::get()->sortByDesc('id');
        } catch (\Exception $e) {
            // Jika ada masalah koneksi ke Google Sheets, kirim array kosong
            $riwayatCloud = [];
        }

        return view('berita-acara.create', compact('user', 'riwayatCloud'));
    }

    public function downloadTemplate()
    {
        $client = $this->getGoogleClient();
        $service = new Drive($client);
        $templateId = Setting::where('key', 'template_id')->value('value') ?? '1ss0yFQ-oFw-rAFskLt6LYShXfNhxKTmT41rnP6QYsWE';

        try {
            $response = $service->files->export($templateId, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', ['alt' => 'media']);
            $content = $response->getBody()->getContents();

            return response()->streamDownload(function() use ($content) {
                echo $content;
            }, 'Template_Berita_Acara_Master.docx');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal download template: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'email_pembuat' => 'required|email',
            'asal_departemen' => 'required',
            'nama_kegiatan' => 'required',
            'nama_ketua' => 'required',
            'nim_ketua' => 'required',
            'tanggal_kegiatan' => 'required|date',
        ]);

        try {
            $client = $this->getGoogleClient();
            $driveService = new Drive($client);
            $docsService = new Docs($client);
            $sheetsService = new Sheets($client);

            // 1. MAPPING FOLDER & DATA
            $folderMapping = [
                'Departemen Syiar Pusat' => '1nqCuLaEnZaaK1gK6iAfxSLxxw0sOZTTg',
                'Departemen MedKomInfo'  => '1ADkamu36keT_4ZM4jeHA8M9G2c9zATS7',
                'Departemen Kaderisasi Pusat' => '1faKg2OEz5sUaLs0YS6gIWnAwWX0gRFIB',
                'Biro Keuangan'    => '1NTrTBbGQUyL0RMbvoZ5-oMw1kU5U2lAU',
                'Biro Kesekretariatan' => '1vgkcxj8axq8YT9bk7B-YZTTkp8rvl0Wh',
                'LDF Al-Fath Fakultas Teknik Elektro' => '1GC5o2NZ5zuXfpLnL9hVxulmkaS04Z92u',
                'LDF Al-Fath Fakultas Informatika'    => '15laaaiJZU3NQGSIFVML15bJkqrNHunok',
                'LDF Al-Fath Fakultas Rekayasa Industri' => '1eeDKVLLJ8TRitJ3jPsZkmx4qEN9qPrYR',
                'LDF Al-Fath Fakultas Ekonomi dan Bisnis'    => '1ac3HudHOVVylBIWlYYAtfnkCfEoV0OOH',
                'LDF Al-Fath Fakultas Komunikasi dan Ilmu Sosial' => '1_OJ9NvIcOxd7zYoygl01kLWVFXo1-1KC',
                'LDF Al-Fath Fakultas Industri Kreatif'  => '1TU9wKlvlrB8ingM8UJqvGRLadJmUg-1e',
                'LDF Al-Fath Fakultas Ilmu Terapan'      => '1oOtZHVEw1L313SpJF5crzg62uVILzZfw',
            ];

            $singkatanMapping = [
                'Biro Keuangan' => 'Keang', 'Biro Kesekretariatan' => 'Kestari', 'Departemen Syiar Pusat' => 'Syiar Pusat',
                'Departemen Kaderisasi Pusat' => 'KDRP', 'Departemen MedKomInfo' => 'Medkom', 'LDF Al-Fath Fakultas Teknik Elektro' => 'FTE',
                'LDF Al-Fath Fakultas Informatika' => 'FIF', 'LDF Al-Fath Fakultas Rekayasa Industri' => 'FRI',
                'LDF Al-Fath Fakultas Ekonomi dan Bisnis' => 'FEB', 'LDF Al-Fath Fakultas Komunikasi dan Ilmu Sosial' => 'FKS',
                'LDF Al-Fath Fakultas Industri Kreatif' => 'FIK', 'LDF Al-Fath Fakultas Ilmu Terapan' => 'FIT',
            ];

            $inisial = $singkatanMapping[$request->asal_departemen] ?? 'LDK';
            $folderId = $folderMapping[$request->asal_departemen] ?? '1NmzZTkQFgM6XTowlctmY3Vxo0IUxOTXE';
            $templateId = Setting::where('key', 'template_id')->value('value') ?? '1ss0yFQ-oFw-rAFskLt6LYShXfNhxKTmT41rnP6QYsWE';
            $namaFile = "BA - " . $inisial . " - " . $request->nama_kegiatan . " - " . date('d-m-Y');

            // 2. COPY TEMPLATE
            $copyMetadata = new Drive\DriveFile([
                'name' => $fileName ?? $namaFile,
                'parents' => [$folderId]
            ]);
            $newFile = $driveService->files->copy($templateId, $copyMetadata, ['fields' => 'id']);
            $newDocumentId = $newFile->id;

            // 3. LOGIKA JABATAN & TANGGAL
            $jabatanKoordinator = str_contains($request->asal_departemen, 'Fakultas') 
                ? "Koordinator " . str_replace('Fakultas ', '', $request->asal_departemen)
                : "Ketua " . $request->asal_departemen;

            Carbon::setLocale('id'); 
            $tgl = Carbon::parse($request->tanggal_kegiatan);

            // 4. REPLACE CONTENT
            $replacements = [
                '[hari]' => $tgl->isoFormat('dddd'), 
                '[tanggal]' => $tgl->isoFormat('D'),
                '[Bulan]' => $tgl->isoFormat('MMMM'), 
                '[tahun]' => $tgl->isoFormat('Y'),
                '[tempat pelaksanaan]' => strtoupper($request->tempat), 
                '[Asal Kepanitiaan]' => $request->asal_departemen,
                '[Nama Kegiatan]' => strtoupper($request->nama_kegiatan), 
                '[Jumlah Peserta]' => $request->jumlah_peserta,
                'Cantumkan realisasi rundown (berupa link rundown)' => $request->rangkaian_kegiatan,
                'Cantumkan dokumentasi acara (berupa link drive)' => $request->link_dokumentasi,
                '[XX.XX]' => $request->jam_selesai, 
                '[Tempat]' => strtoupper($request->kota),
                '[DD Mmmm YYYY]' => $tgl->isoFormat('D MMMM Y'), 
                '[NAMA_KETUA]' => strtoupper($request->nama_ketua),
                'NIM. XXXXXXX' => 'NIM. ' . strtoupper($request->nim_ketua), 
                '[NAMA_SEKRETARIS]' => strtoupper($request->nama_sekretaris),
                'NIM. [XXXXXXX]' => 'NIM. ' . strtoupper($request->nim_sekretaris), 
                '[NAMA_KOORDINATOR]' => isset($request->nama_koordinator) ? strtoupper($request->nama_koordinator) : '.........................',
                '(NIM. XXXXXXX)' => isset($request->nim_koordinator) ? 'NIM. ' . strtoupper($request->nim_koordinator) : '.........................',
                '[JABATAN_KOORDINATOR]' => strtoupper($jabatanKoordinator),
            ];

            $docsRequests = [];
            foreach ($replacements as $key => $value) {
                $docsRequests[] = new Docs\Request([
                    'replaceAllText' => [
                        'containsText' => ['text' => $key, 'matchCase' => true],
                        'replaceText' => (string)($value ?? '-'),
                    ],
                ]);
            }
            $docsService->documents->batchUpdate($newDocumentId, new Docs\BatchUpdateDocumentRequest(['requests' => $docsRequests]));

            // 5. APPEND KE SHEETS
            try {
                // Panggil Service menggunakan app() agar efisien (Singleton)
                $gsheetService = app(\App\Services\GoogleSheetService::class);
                $spreadsheetId = env('GSHEET_BERITA_ACARA_ID'); // Ambil dari .env[cite: 2, 28]
                
                // Siapkan data baris (Array 1 Dimensi)
                $rowValues = [
                    \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB',
                    $request->email_pembuat,
                    $request->asal_departemen,
                    strtoupper($request->nama_kegiatan),
                    strtoupper($request->nama_ketua),
                    "https://docs.google.com/document/d/" . $newDocumentId . "/edit",
                    Auth::user()->unit,
                ];

                // Gunakan fungsi appendSheet dari service agar logika penulisan seragam[cite: 24]
                $gsheetService->appendSheet($spreadsheetId, 'BeritaAcara_db!A2', $rowValues);

            } catch (\Exception $e) {
                // Log error jika sinkronisasi cloud gagal, tapi proses dokumen tetap lanjut
                \Illuminate\Support\Facades\Log::error("Gagal Sinkron Berita Acara: " . $e->getMessage());
            } 

            return redirect()->route('berita-acara.create')->with('success', 'Alhamdulillah! Berita Acara berhasil dibuat.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses Berita Acara: ' . $e->getMessage());
        }
    }
}