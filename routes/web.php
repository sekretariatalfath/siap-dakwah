<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\BeritaAcaraController;
use App\Http\Controllers\Admin\TemplateController; 
use App\Http\Controllers\NotulensiController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\EvaluasiController;
use App\Http\Controllers\LpjController;
use App\Http\Controllers\PresensiPublicController;
use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

// ==========================================
// 1. AREA PUBLIC
// ==========================================
Route::get('/', function () { 
    $service = new \App\Services\GoogleSheetService();
    $spreadsheetId = env('GSHEET_RESOURCE_ID');
    $rows = $service->readSheet($spreadsheetId, "Templates!A2:D") ?: [];
    $featuredTemplates = collect($rows)->reverse()->take(3)->map(fn($r) => (object)['judul' => $r[1] ?? 'Template', 'link' => $r[3] ?? '#']);
    return view('welcome', compact('featuredTemplates')); 
})->name('home');
Route::get('/sop', function () { 
    $service = new \App\Services\GoogleSheetService();
    $spreadsheetId = env('GSHEET_RESOURCE_ID');
    $rows = $service->readSheet($spreadsheetId, "Pedoman!A2:E") ?: [];
    
    $sop = collect($rows)->filter(function($r) {
        $akses = $r[4] ?? 'Internal';
        return $akses == 'Publik' || Auth::check();
    })->map(fn($r) => (object)[
        'judul'     => $r[1] ?? 'Judul SOP',
        'deskripsi' => $r[2] ?? 'Panduan administrasi.',
        'link'      => $r[3] ?? '#'
    ]);

    return view('public.sop', compact('sop')); 
})->name('public.sop');

Route::get('/template', function () {
    $service = new \App\Services\GoogleSheetService();
    $spreadsheetId = env('GSHEET_RESOURCE_ID');
    $rows = $service->readSheet($spreadsheetId, "Templates!A2:E") ?: [];
    
    $templates = collect($rows)->filter(function($r) {
        $akses = $r[4] ?? 'Internal';
        return $akses == 'Publik' || Auth::check();
    })->map(fn($r) => (object)[
        'judul'     => $r[1] ?? 'Template',
        'deskripsi' => $r[2] ?? 'Unduh dokumen resmi.',
        'link'      => $r[3] ?? '#'
    ]);
    return view('public.template', compact('templates'));
})->name('public.template');

Route::get('/informasi', function () {
    $service = new \App\Services\GoogleSheetService();
    $spreadsheetId = env('GSHEET_RESOURCE_ID');
    $rows = $service->readSheet($spreadsheetId, "Informasi!A2:E") ?: [];
    
    $informasi = collect($rows)->filter(function($r) {
        $akses = $r[4] ?? 'Internal';
        return $akses == 'Publik' || Auth::check();
    })->map(fn($r) => (object)[
        'judul'     => $r[1] ?? 'Informasi',
        'deskripsi' => $r[2] ?? 'Informasi penting Al-Fath.',
        'link'      => $r[3] ?? '#'
    ]);
    return view('public.informasi', compact('informasi'));
})->name('public.informasi');

// AREA PUBLIC (Tanpa Login)
Route::get('/p/{idSesi}', [PresensiPublicController::class, 'showForm'])->name('presensi.public');
Route::post('/p/{idSesi}', [PresensiPublicController::class, 'submitHadir'])
    ->middleware('throttle:5,1') // <--- Tambahkan ini
    ->name('presensi.submit');

// AREA DASHBOARD (Wajib Login)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::prefix('dashboard/presensi')->name('presensi.')->group(function() {
        Route::get('/', [DashboardController::class, 'presensiIndex'])->name('index');
        Route::post('/generate', [DashboardController::class, 'storeSesi'])->name('generate');
        Route::post('/toggle/{idSesi}', [DashboardController::class, 'toggleSesi'])->name('toggle');
        Route::get('/detail/{idSesi}', [DashboardController::class, 'presensiDetail'])->name('detail');
        Route::post('/update-row', [DashboardController::class, 'updatePresensiRow'])->name('update-row');
        Route::post('/store-manual', [DashboardController::class, 'storePresensiManual'])->name('store-row');
        Route::get('/export-pdf/{idSesi}', [DashboardController::class, 'exportPdf'])->name('export-pdf');
    });
});

// ==========================================
// 2. AREA AUTH (LOGIN)
// ==========================================
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'processLogin']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// 3. DASHBOARD (MIDDLEWARE AUTH)
// ==========================================
Route::middleware(['auth'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // --- FITUR PRESENSI KADER ---
    Route::prefix('dashboard/presensi')->name('presensi.')->group(function() {
        Route::get('/', [DashboardController::class, 'presensiIndex'])->name('index');
        Route::post('/generate', [DashboardController::class, 'storeSesi'])->name('generate');
        Route::post('/toggle/{idSesi}', [DashboardController::class, 'toggleSesi'])->name('toggle');
        Route::get('/detail/{idSesi}', [DashboardController::class, 'presensiDetail'])->name('detail');
        Route::post('/update-row', [DashboardController::class, 'updatePresensiRow'])->name('update-row');
        Route::post('/store-manual', [DashboardController::class, 'storePresensiManual'])->name('store-row');
        Route::get('/export-pdf/{idSesi}', [DashboardController::class, 'exportPdf'])->name('export-pdf');
    });

    // --- FITUR MANAJEMEN PROPOSAL ---
    Route::prefix('dashboard/proposal')->name('proposal.')->group(function () {
        Route::get('/', [ProposalController::class, 'index'])->name('index');
        Route::post('/store', [ProposalController::class, 'store'])->name('store');
        Route::post('/toggle/{proposal_id}', [ProposalController::class, 'toggleCheck'])->name('toggle');
        Route::delete('/{proposal_id}', [ProposalController::class, 'destroy'])->name('destroy');
    });

    // --- FITUR NOTULENSI ---
    Route::prefix('notulensi')->name('notulensi.')->group(function () {
        Route::get('/', [NotulensiController::class, 'index'])->name('index'); 
        Route::get('/buat', [NotulensiController::class, 'create'])->name('create');
        Route::post('/generate', [NotulensiController::class, 'generateTemplate'])->name('generate');
        Route::post('/update-link/{id}', [NotulensiController::class, 'updateLink'])->name('update-link');
        Route::delete('/delete/{id}', [NotulensiController::class, 'destroy'])->name('destroy');
    });

    // --- FITUR SURAT ---
    Route::get('/surat/buat', [SuratController::class, 'create'])->name('surat.create');
    Route::post('/surat', [SuratController::class, 'store'])->name('surat.store');
    Route::get('/surat/arsip', [SuratController::class, 'arsipList'])->name('surat.arsip');
    Route::post('/surat/arsip/update', [SuratController::class, 'updateArsip'])->name('surat.arsip.update');

    // --- FITUR SURAT MASUK ---
    Route::get('/surat-masuk', [SuratMasukController::class, 'index'])->name('surat-masuk.index');
    Route::post('/surat-masuk', [SuratMasukController::class, 'store'])->name('surat-masuk.store');
    Route::delete('/surat-masuk/{no_surat}', [SuratMasukController::class, 'destroy'])->name('surat-masuk.destroy');
    Route::post('/surat-masuk/{no_surat}/toggle-check', [SuratMasukController::class, 'toggleCheck'])->name('surat-masuk.toggle');

    // --- FITUR BERITA ACARA ---
    Route::get('/berita-acara/buat', [BeritaAcaraController::class, 'create'])->name('berita-acara.create');
    Route::post('/berita-acara/simpan', [BeritaAcaraController::class, 'store'])->name('berita-acara.store');
    Route::get('/berita-acara/download-template', [BeritaAcaraController::class, 'downloadTemplate'])->name('berita-acara.download-template');

    // --- FITUR EVALUASI & LPJ ---
    Route::prefix('dashboard/lpj-evaluasi')->group(function () {
        Route::get('/generate-evaluasi', [EvaluasiController::class, 'create'])->name('evaluasi.generate.form');
        Route::post('/generate-evaluasi', [EvaluasiController::class, 'generate'])->name('evaluasi.generate.submit');
        Route::get('/arsip-evaluasi', [EvaluasiController::class, 'index'])->name('evaluasi.index');
        Route::delete('/destroy-evaluasi/{id}', [EvaluasiController::class, 'destroy'])->name('evaluasi.destroy');
        
        // Fitur Emergency Clean Up (Pembersih Gudang Robot)
        // Ganti isi rute purge-robot Mas dengan ini buat debugging
        Route::get('/purge-robot', function() {
            $client = new \Google\Client();
            $client->setAuthConfig(storage_path('app/credentials.json'));
            $client->addScope(\Google\Service\Drive::DRIVE);
            $driveService = new \Google\Service\Drive($client);

            try {
                // 1. Ambil info kuota untuk memastikan bot benar-benar baru/kosong
                $about = $driveService->about->get(['fields' => 'storageQuota']);
                $usageBefore = $about->getStorageQuota()->getUsage();

                // 2. Hapus semua file yang dimiliki bot di seluruh penjuru Drive
                $files = $driveService->files->listFiles(['q' => "'me' in owners"])->getFiles();
                foreach ($files as $file) { 
                    try { 
                        $driveService->files->delete($file->id); 
                    } catch (\Exception $e) {}
                }

                // 3. PAKSA KOSONGKAN SAMPAH (Ini kunci biar 0 MB)
                $driveService->files->emptyTrash();

                $aboutAfter = $driveService->about->get(['fields' => 'storageQuota']);
                $usageAfter = $aboutAfter->getStorageQuota()->getUsage();

                return "Bot UKM Bersih! Pemakaian: " . ($usageAfter / (1024 * 1024)) . " MB. Silakan gas generate!";
            } catch (\Exception $e) {
                return "Gagal bersih-bersih: " . $e->getMessage();
            }
        })->middleware('auth')->name('evaluasi.purge');

        // LPJ
        Route::get('/arsip-lpj', [LpjController::class, 'index'])->name('lpj.index');
        Route::post('/store-lpj', [LpjController::class, 'store'])->name('lpj.store');
        Route::post('/verify-lpj/{id_lpj}', [LpjController::class, 'verify'])->name('lpj.verify');
        Route::delete('/destroy-lpj/{id_lpj}', [LpjController::class, 'destroy'])->name('lpj.destroy');
    });

    Route::resource('admin/templates', TemplateController::class);
});

Route::get('/resource/{category}', [DashboardController::class, 'resourceIndex'])->name('resource.index');

Route::middleware(['auth'])->group(function () {
    Route::post('/resource/store', [DashboardController::class, 'resourceStore'])->name('resource.store');
    Route::delete('/resource/delete/{rowIndex}', [DashboardController::class, 'resourceDelete'])->name('resource.delete');
    Route::post('/dashboard/calendar-update', [DashboardController::class, 'updateCalendarLink'])->name('dashboard.calendar.update');

    // --- MANAJEMEN AKUN & PENGATURAN ---
    Route::get('/dashboard/settings', [AccountController::class, 'index'])->name('settings.index');
    Route::post('/dashboard/settings/password', [AccountController::class, 'changePassword'])->name('settings.password');
    Route::post('/dashboard/settings/register', [AccountController::class, 'register'])->name('settings.register');
    Route::post('/dashboard/settings/reset-user/{id}', [AccountController::class, 'resetUserPassword'])->name('settings.reset-user');
    Route::delete('/dashboard/settings/user/{id}', [AccountController::class, 'deleteUser'])->name('settings.delete-user');
    Route::post('/dashboard/settings/purge-sps', [AccountController::class, 'purgeSps'])->name('settings.purge-sps');
    Route::post('/dashboard/settings/hard-reset', [AccountController::class, 'hardReset'])->name('settings.hard-reset');
    Route::post('/dashboard/settings/toggle-auth', [AccountController::class, 'toggleAuthSource'])->name('settings.toggle-auth');
    Route::post('/dashboard/settings/cp/store', [AccountController::class, 'storeCp'])->name('settings.store-cp');
    Route::delete('/dashboard/settings/cp/{rowIndex}', [AccountController::class, 'destroyCp'])->name('settings.destroy-cp');
});

Route::get('/gas-pol-storage', function () {
    Artisan::call('storage:link');
    return "Selamat! Folder Storage sudah terhubung.";
});

Route::get('/clear-cache', function () {
    try {
        Artisan::call('optimize:clear');
        return "Cache berhasil dihapus. Silahkan cek ulang tampilan web.";
    } catch (\Exception $e) {
        return "Gagal membersihkan cache: " . $e->getMessage();
    }
});
