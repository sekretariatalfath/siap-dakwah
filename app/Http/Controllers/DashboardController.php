<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Surat; 
use App\Models\BeritaAcara; 
use App\Models\SesiPresensi;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
    /**
     * Dashboard Utama: Menampilkan statistik performa organisasi.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $filterUnit = $request->input('unit_filter');
        
        // Logika Filter (Kestari vs Unit)
        $isKestari = ($user->role == 'superadmin' || $user->unit == 'Biro Kesekretariatan');
        
        // Buat kunci cache yang unik berdasarkan filter/unit biar gak ketuker
        $cacheKey = 'dashboard_stats_' . ($isKestari ? ($filterUnit ?: 'all') : $user->unit);
        $cacheKey = str_replace(' ', '_', strtolower($cacheKey)); // Bersihkan spasi

        // Simpan dalam cache selama 300 detik (5 menit)
        $stats = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($isKestari, $user, $filterUnit) {
            // 1. SURAT MASUK
            $totalMasuk = \App\Models\SuratMasuk::count();
            $perluVerifikasi = \App\Models\SuratMasuk::whereRaw('is_checked = false')->count();

            // 2. SURAT KELUAR
            $semuaKeluarQuery = \App\Models\Surat::query();
            
            if (!$isKestari) {
                $unitLower = trim(strtolower($user->unit));
                $semuaKeluarQuery->whereRaw('LOWER(TRIM(asal_pengisi)) = ?', [$unitLower]);
            } elseif ($filterUnit) {
                $filterLower = trim(strtolower($filterUnit));
                $semuaKeluarQuery->whereRaw('LOWER(TRIM(asal_pengisi)) = ?', [$filterLower]);
            }

            $totalKeluar = (clone $semuaKeluarQuery)->count();
            $belumArsip = (clone $semuaKeluarQuery)->where(function($q) {
                $q->whereNull('link_drive')->orWhere('link_drive', '')->orWhere('link_drive', '-');
            })->count();
            
            $detailJenis = (clone $semuaKeluarQuery)->select('jenis', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                ->groupBy('jenis')
                ->pluck('count', 'jenis');

            // 3. BERITA ACARA
            $semuaBAQuery = \App\Models\BeritaAcara::query();
            
            if (!$isKestari) {
                $unitLower = trim(strtolower($user->unit));
                $semuaBAQuery->whereRaw('LOWER(TRIM(asal_unit_akun)) = ?', [$unitLower]);
            } elseif ($filterUnit) {
                $filterLower = trim(strtolower($filterUnit));
                $semuaBAQuery->whereRaw('LOWER(TRIM(asal_unit_akun)) = ?', [$filterLower]);
            }
            
            $totalBA = $semuaBAQuery->count();

            return [
                'totalMasuk' => $totalMasuk,
                'perluVerifikasi' => $perluVerifikasi,
                'totalKeluar' => $totalKeluar,
                'belumArsip' => $belumArsip,
                'detailJenis' => $detailJenis,
                'totalBA' => $totalBA,
            ];
        });

        // Ambil Link Kalender dari Database (Dummy default jika kosong)
        $calendarLink = \Illuminate\Support\Facades\Cache::rememberForever('calendar_link', function () {
            $setting = \Illuminate\Support\Facades\DB::table('settings')->where('key', 'calendar_link')->first();
            return $setting ? $setting->value : 'https://docs.google.com/spreadsheets/d/e/2PACX-1vT1.../pubhtml';
        });

        return view('dashboard.main', [
            'user' => $user,
            'isKestari' => $isKestari,
            'totalMasuk' => $stats['totalMasuk'],
            'perluVerifikasi' => $stats['perluVerifikasi'],
            'totalBA' => $stats['totalBA'],
            'totalKeluar' => $stats['totalKeluar'],
            'belumArsip' => $stats['belumArsip'],
            'detailJenis' => $stats['detailJenis'],
            'calendarLink' => $calendarLink,
            'filterUnit' => $filterUnit
        ]);
    }

    // Tambahkan di DashboardController

    // app/Http/Controllers/DashboardController.php

    private $resourceSpreadsheetId;

        public function __construct()
        {
            // Ambil ID dari file .env agar lebih rapi
            $this->resourceSpreadsheetId = env('GSHEET_RESOURCE_ID');
        }

    public function resourceIndex($category) {
        $service = new \App\Services\GoogleSheetService();
        
        $sheetMap = [
            'template'  => 'Templates',
            'pedoman'   => 'Pedoman',
            'informasi' => 'Informasi'
        ];
        $sheetName = $sheetMap[$category] ?? 'Templates';

        // Ambil data dari tab yang sesuai (Kolom A-E)
        $rows = $service->readSheet($this->resourceSpreadsheetId, "{$sheetName}!A2:E") ?: [];
        
        $resources = collect($rows)->map(function($row, $index) {
            return (object) [
                'row_index' => $index + 2,
                'judul'     => $row[1] ?? '', // Kolom B
                'deskripsi' => $row[2] ?? '', // Kolom C
                'link'      => $row[3] ?? '#', // Kolom D
                'akses'     => $row[4] ?? 'Internal' // Kolom E
            ];
        });

        return view('dashboard.resource_table', compact('resources', 'category', 'sheetName'));
    }

    public function resourceStore(Request $request) {
        $service = new \App\Services\GoogleSheetService();
        
        $sheetMap = [
            'template'  => 'Templates',
            'pedoman'   => 'Pedoman',
            'informasi' => 'Informasi'
        ];
        $sheetName = $sheetMap[$request->category] ?? 'Templates';

        // Kolom: Kategori (A), Judul (B), Deskripsi (C), Link (D), Akses (E)
        $data = [
            strtoupper($request->category), 
            $request->judul, 
            $request->deskripsi, 
            $request->link,
            $request->akses ?? 'Internal'
        ];

        try {
            $service->appendSheet($this->resourceSpreadsheetId, "{$sheetName}!A2", $data);
            return back()->with('success', 'Data berhasil disimpan ke tab ' . $sheetName);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal simpan! Pastikan nama tab sudah benar.');
        }
    }

    public function resourceDelete(Request $request, $rowIndex) {
        // 1. Panggil service menggunakan app() sesuai anjuran
        $service = app(\App\Services\GoogleSheetService::class);
        
        // 2. Pemetaan Nama Tab (untuk notifikasi success)
        $sheetNames = [
            'template'  => 'Templates',
            'pedoman'   => 'Pedoman',
            'informasi' => 'Informasi'
        ];
        $sheetName = $sheetNames[$request->category] ?? 'Templates';

        // 3. Pemetaan GID Spesifik dari .env agar tidak "Salah Tembak"
        $gidMap = [
            'template'  => env('GID_TEMPLATES', 0),
            'pedoman'   => env('GID_PEDOMAN', 0),
            'informasi' => env('GID_INFORMASI', 0)
        ];
        $targetGid = $gidMap[$request->category] ?? 0;

        try {
            // 4. Jalankan BatchUpdate dengan targetGid yang akurat
            $batchUpdate = new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest([
                'requests' => [['deleteDimension' => ['range' => [
                    'sheetId'    => $targetGid, // Mengunci tab yang benar
                    'dimension'  => 'ROWS',
                    'startIndex' => $rowIndex - 1, // API Google Sheets mulai dari 0
                    'endIndex'   => $rowIndex
                ]]]]
            ]);
            
            $service->getService()->spreadsheets->batchUpdate($this->resourceSpreadsheetId, $batchUpdate);
            
            return back()->with('success', "Baris di tab {$sheetName} berhasil dihapus secara permanen!");
        } catch (\Exception $e) {
            // Log error jika sinkronisasi gagal
            \Illuminate\Support\Facades\Log::error("Gagal Hapus Resource: " . $e->getMessage());
            return back()->with('error', 'Gagal hapus di Cloud: ' . $e->getMessage());
        }
    }

    public function updateCalendarLink(Request $request) {
        $request->validate(['calendar_link' => 'required|url']);
        \Illuminate\Support\Facades\DB::table('settings')->updateOrInsert(
            ['key' => 'calendar_link'],
            ['value' => $request->calendar_link, 'updated_at' => now()]
        );
        \Illuminate\Support\Facades\Cache::forget('calendar_link');
        return back()->with('success', 'Link Kalender diperbarui!');
    }
    /**
     * Manajemen Presensi (Admin): Daftar semua link presensi.
     */
    public function presensiIndex(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        
        // Ambil data melalui model Sushi dengan query builder
        $query = SesiPresensi::query();

        // Filter unit jika bukan superadmin atau Kestari
        $isKestari = ($user->role == 'superadmin' || $user->unit == 'Biro Kesekretariatan');
        if (!$isKestari) {
            $query->where('unit_host', $user->unit);
        }

        // Fitur Cari
        if ($search) {
            $query->where('nama_kegiatan', 'like', '%' . $search . '%');
        }

        // Pagination menggunakan Eloquent
        $semuaSesi = $query->paginate(5)->withQueryString();

        return view('presensi.index', compact('semuaSesi', 'search'));
    }

    /**
     * Membuat Sesi Presensi Baru (Kirim ke Google Sheets).
     */
    public function storeSesi(Request $request)
    {
        $request->validate(['nama_kegiatan' => 'required|max:100', 'kategori' => 'required', 'unit_host' => 'required']);
        
        $idSesi = 'PRE-' . strtoupper(Str::random(6));
        $data = [$idSesi, strtoupper($request->nama_kegiatan), $request->kategori, $request->unit_host, date('d-m-Y H:i'), '1'];

        try {
            $service = new \App\Services\GoogleSheetService(); // PAKAI app()
            $service->appendSheet(env('GSHEET_PRESENSI_ID'), 'Sesi_Presensi!A2', $data);
            return back()->with('success', 'Sesi Berhasil Dibuat: ' . $idSesi);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Membuka atau Menutup akses presensi secara real-time.
     */
    public function toggleSesi($idSesi)
    {
        $service = new \App\Services\GoogleSheetService(); // PAKAI app()
        $spreadsheetId = env('GSHEET_PRESENSI_ID');
        $rows = $service->readSheet($spreadsheetId, 'Sesi_Presensi!A2:F');
        
        if ($rows) {
            foreach ($rows as $index => $row) {
                if (($row[0] ?? '') == $idSesi) {
                    $newStatus = ($row[5] ?? '0') == '1' ? '0' : '1';
                    $service->updateCell($spreadsheetId, "Sesi_Presensi!F" . ($index + 2), $newStatus);
                    break;
                }
            }
        }
        return back()->with('success', 'Status akses diperbarui!');
    }

    /**
     * Menampilkan daftar kader yang hadir pada sesi tertentu.
     */
    public function presensiDetail(Request $request, $idSesi)
    {
        $sesiInfo = SesiPresensi::find($idSesi);
        $service = new \App\Services\GoogleSheetService();
        $search = $request->input('search');
        $rows = $service->readSheet(env('GSHEET_PRESENSI_ID'), 'Presensi_Detail!A2:H') ?: [];
        
        $query = collect($rows)->map(fn($row, $index) => [
            'row_index' => $index + 2, 
            'id_sesi_row' => $row[0] ?? '',
            'email' => $row[1] ?? '', 
            'nama' => $row[2] ?? '',
            'amanah' => $row[3] ?? '', 
            'wajihah' => $row[4] ?? '', 
            'waktu' => $row[5] ?? '',
            'nim' => $row[6] ?? '', 
            'status' => $row[7] ?? 'Hadir'
        ])->filter(fn($h) => $h['id_sesi_row'] == $idSesi);

        if ($search) {
            $query = $query->filter(fn($h) => str_contains(strtolower($h['nama']), strtolower($search)) || str_contains($h['nim'], $search));
        }

        // Pagination
        $currentPage = $request->input('page', 1);
        $perPage = 10;
        $daftarHadir = new LengthAwarePaginator($query->slice(($currentPage - 1) * $perPage, $perPage)->values(), $query->count(), $perPage, $currentPage, [
            'path' => $request->url(), 'query' => $request->query(),
        ]);

        return view('presensi.detail', compact('daftarHadir', 'idSesi', 'sesiInfo', 'search'));
    }

    /**
     * Memperbarui data kehadiran kader secara manual (Koreksi Admin).
     */
    public function updatePresensiRow(Request $request)
    {
        $service = new \App\Services\GoogleSheetService();
        $spreadsheetId = env('GSHEET_PRESENSI_ID');
        $idx = $request->row_index;

        $service->updateCell($spreadsheetId, "Presensi_Detail!C{$idx}", strtoupper($request->nama));
        $service->updateCell($spreadsheetId, "Presensi_Detail!D{$idx}", $request->amanah);
        $service->updateCell($spreadsheetId, "Presensi_Detail!E{$idx}", $request->wajihah);
        $service->updateCell($spreadsheetId, "Presensi_Detail!G{$idx}", $request->nim);
        $service->updateCell($spreadsheetId, "Presensi_Detail!H{$idx}", $request->status);

        return back()->with('success', 'Data kader berhasil dikoreksi.');
    }

    /**
     * Admin menambahkan kader ke daftar hadir secara manual.
     */
    public function storePresensiManual(Request $request)
    {
        $data = [
            $request->id_sesi, 'admin@internal.com', strtoupper($request->nama),
            strtoupper($request->amanah), $request->wajihah, date('Y-m-d H:i:s'),
            $request->nim, $request->status
        ];

        try {
            $service = new \App\Services\GoogleSheetService();
            $service->appendSheet(env('GSHEET_PRESENSI_ID'), 'Presensi_Detail!A2', $data);
            return back()->with('success', 'Kader berhasil ditambahkan manual.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Export hasil kehadiran ke PDF dengan Kop Surat Al-Fath.
     */
    public function exportPdf(Request $request, $idSesi) 
    {
        $sesiInfo = SesiPresensi::find($idSesi);
        $service = new \App\Services\GoogleSheetService();
        $rows = $service->readSheet(env('GSHEET_PRESENSI_ID'), 'Presensi_Detail!A2:H') ?: [];
        
        $daftarHadir = collect($rows)->filter(fn($r) => ($r[0] ?? '') == $idSesi)->map(fn($r) => [
            'nama' => strtoupper($r[2] ?? ''), 'nim' => $r[6] ?? '', 'status' => $r[7] ?? '', 'amanah' => $r[3] ?? '', 'wajihah' => $r[4] ?? ''
        ]);

        $pdf = Pdf::loadView('exports.presensi_pdf', [
            'daftarHadir' => $daftarHadir,
            'sesiInfo' => $sesiInfo,
            'idSesi' => $idSesi,
            'namaTtd' => strtoupper($request->nama_ttd),
            'nimTtd' => $request->nim_ttd,
            'namaUnitLengkap' => Auth::user()->unit
        ]);
        
        return $pdf->stream("Rekap-Presensi-{$idSesi}.pdf");
    }
}