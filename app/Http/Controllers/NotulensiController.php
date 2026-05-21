<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notulensi;
use Illuminate\Support\Facades\Auth;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Docs;
use Carbon\Carbon;

class NotulensiController extends Controller
{
    /**
     * Helper untuk koneksi Google API
     */
    private function getGoogleClient()
    {
        $client = new Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        
        $refreshToken = env('GOOGLE_REFRESH_TOKEN');
        $client->refreshToken($refreshToken);
        
        $client->addScope([Drive::DRIVE, Docs::DOCUMENTS]);
        return $client;
    }

    public function index()
    {
        $user = Auth::user();
        $isKestari = ($user->role == 'superadmin' || $user->unit == 'Biro Kesekretariatan');
        $notulensis = $isKestari 
            ? Notulensi::latest()->get() 
            : Notulensi::where('unit_owner', $user->unit)->latest()->get();

        return view('notulensi.index', compact('notulensis'));
    }

    public function create()
    {
        return view('notulensi.create');
    }

    public function generateTemplate(Request $request)
    {
        $request->validate([
            'judul_syuro' => 'required|string|max:255',
            'pimpinan_rapat' => 'required|string|max:255',
            'kategori' => 'required',
            'waktu_mulai' => 'required',
            'tempat' => 'required|string',
        ]);

        try {
            $client = $this->getGoogleClient();
            $driveService = new Drive($client);
            $docsService = new Docs($client);

            $folderMapping = [
                'Rutin/Koordinasi' => '1MHUq8rAsjUXPV3rOO-gjvPSbhIixYGVz',
                'Proker KM'        => '1lHZjTnTENJfvK7GEIItaXQsvpo30z-xa',
                'Proker Non KM'    => '1OP_iZ-oQuuWtWJnElWfdDwp31QwqbfwV',
            ];
            
            $targetFolderId = $folderMapping[$request->kategori] ?? '1SY3zLiFJso8RoNTcmgEMAHzuST8iWrAz';
            $templateId = '1ePK_MaUOh_PEpQyTqA-TgtJuddH7tgSOpnuGUxaygnM';

            // --- PERBAIKAN LOGIKA KODE UNIT ---
            $user = Auth::user();
            // Ambil nama unit asli dari database dan bersihkan spasi
            $namaUnitDB = trim(Auth::user()->unit); 

            // Pastikan Key di sini SAMA PERSIS dengan tulisan di database Anda
            $unitMap = [
                'Biro Kesekretariatan'                 => 'KES',
                'Biro Keuangan'                        => 'KEU',
                'Departemen Syiar Pusat'               => 'SYR',
                'Departemen Kaderisasi Pusat'          => 'KAD',
                'Departemen Medkominfo'                => 'MDK',
                'LDF Al-Fath Fakultas Informatika'     => 'FIF',
                'LDF Al-Fath Fakultas Teknik Elektro'  => 'FTE',
                'LDF Al-Fath Fakultas Industri Kreatif' => 'FIK',
                'LDF Al-Fath Fakultas Komunikasi dan Ilmu Sosial' => 'FKS',
                'LDF Al-Fath Fakultas Rekayasa Industri' => 'FRI',
                'LDF Al-Fath Fakultas Ilmu Terapan'    => 'FIT',
                'LDF Al-Fath Fakultas Ekonomi dan Bisnis' => 'FEB',
            ];

            // Cari di map, gunakan penulisan yang lebih aman
            if (isset($unitMap[$namaUnitDB])) {
                $unitCode = $unitMap[$namaUnitDB];
            } else {
                // Jika tidak ketemu di map, ambil 3 huruf depan (Inilah penyebab "BIR")
                $unitCode = strtoupper(substr($namaUnitDB, 0, 3));
            }
            // ----------------------------------

            $fileName = "NOTULENSI - " . strtoupper($request->judul_syuro) . " - " . date('d/m/Y') . " - " . $unitCode;

            $copyMetadata = new Drive\DriveFile([
                'name' => $fileName,
                'parents' => [$targetFolderId]
            ]);
            $newFile = $driveService->files->copy($templateId, $copyMetadata);
            $newFileId = $newFile->id;

            Carbon::setLocale('id');
            $tgl = Carbon::parse($request->waktu_mulai);

            $replacements = [
                '[JUDUL SYURO]'           => strtoupper($request->judul_syuro),
                '[Hari, Tgl Bulan Tahun]'  => $tgl->isoFormat('dddd, D MMMM Y'),
                '[XX.XX s.d. XX.XX WIB]'  => $tgl->format('H:i') . ' s.d. Selesai WIB',
                '[Tempat/Platfom]'        => strtoupper($request->tempat),
                '[Nama Pimpinan]'         => strtoupper($request->pimpinan_rapat),
                '[Link Drive PDF]'        => '[Link Drive PDF]', 
            ];

            $docsRequests = [];
            foreach ($replacements as $key => $value) {
                $docsRequests[] = new Docs\Request([
                    'replaceAllText' => [
                        'containsText' => ['text' => $key, 'matchCase' => true],
                        'replaceText' => (string)$value,
                    ],
                ]);
            }
            
            $docsService->documents->batchUpdate($newFileId, new Docs\BatchUpdateDocumentRequest([
                'requests' => $docsRequests
            ]));

            Notulensi::create([
                'unit_owner' => $user->unit,
                'judul_syuro' => strtoupper($request->judul_syuro),
                'pimpinan_rapat' => strtoupper($request->pimpinan_rapat),
                'kategori' => strtoupper($request->kategori),
                'waktu_mulai' => $request->waktu_mulai,
                'tempat' => strtoupper($request->tempat),
                'link_google_docs' => "https://docs.google.com/document/d/$newFileId/edit",
                'google_drive_file_id' => $newFileId,
            ]);

            return redirect()->route('notulensi.index')->with('success', 'Alhamdulillah! Notulensi Berhasil Dibuat.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memproses Notulensi: ' . $e->getMessage());
        }
    }

    public function updateLink(Request $request, $id)
    {
        $request->validate(['link_daftar_hadir' => 'required|url']);
        $notulensi = Notulensi::findOrFail($id);
        
        try {
            $client = $this->getGoogleClient();
            $docsService = new Docs($client);
            
            $requests = [
                new Docs\Request([
                    'replaceAllText' => [
                        'containsText' => ['text' => '[Link Drive PDF]', 'matchCase' => true],
                        'replaceText' => $request->link_daftar_hadir,
                    ],
                ])
            ];
            
            $docsService->documents->batchUpdate($notulensi->google_drive_file_id, new Docs\BatchUpdateDocumentRequest([
                'requests' => $requests
            ]));

            $notulensi->update(['link_daftar_hadir' => $request->link_daftar_hadir]);

            return back()->with('success', 'Link presensi otomatis ter-update di Google Docs!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal sinkron ke Google Docs: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $notulensi = Notulensi::findOrFail($id);

        try {
            // 1. Koneksi ke Google Drive Service
            $client = $this->getGoogleClient();
            $driveService = new \Google\Service\Drive($client);

            // 2. Hapus file di Google Drive (masuk ke sampah/trash)
            if ($notulensi->google_drive_file_id) {
                $driveService->files->delete($notulensi->google_drive_file_id);
            }

            // 3. Hapus data di Database MySQL
            $notulensi->delete();

            return back()->with('success', 'Arsip Notulensi dan file di Google Drive berhasil dihapus!');
            
        } catch (\Exception $e) {
            // Jika gagal hapus di Drive (misal file sudah dihapus manual), tetap hapus di DB
            $notulensi->delete();
            return back()->with('error', 'Data dihapus, tapi file di Drive tidak ditemukan/gagal dihapus: ' . $e->getMessage());
        }
    }
}