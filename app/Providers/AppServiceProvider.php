<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
       
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // 1. SET LOCALE CARBON KE INDONESIA
        config(['app.locale' => 'id']);
        Carbon::setLocale('id');

        // 2. SET TIMEZONE WIB
        date_default_timezone_set('Asia/Jakarta');

        // 3. GLOBAL UNITS DATA (Scalable - Otomatis mendeteksi admin baru dari unit manapun)
        View::composer('*', function ($view) {
            // Ambil daftar unit unik dari tabel Users
            try {
                $dbUnitsRaw = \App\Models\User::whereNotNull('unit')
                            ->where('unit', '!=', '')
                            ->distinct()
                            ->pluck('unit')
                            ->toArray();

                // Fungsi Perapi Otomatis (Cerdas & Presisi)
                $formatUnit = function($text) {
                    $text = strtolower(trim($text));
                    $text = ucwords($text);
                    
                    // Daftar kata yang WAJIB HURUF BESAR SEMUA (Singkatan)
                    $acronyms = ['Ldf', 'Fte', 'Fif', 'Fri', 'Feb', 'Fkb', 'Fik', 'Fit', 'Fks', 'Mdk', 'Syr', 'Kdr', 'Keu', 'Kes', 'Mq', 'Dkm', 'Prisma', 'Lazissu', 'Bm'];
                    $replacements = ['LDF', 'FTE', 'FIF', 'FRI', 'FEB', 'FKB', 'FIK', 'FIT', 'FKS', 'MDK', 'SYR', 'KDR', 'KEU', 'KES', 'MQ', 'DKM', 'PRISMA', 'LAZISSU', 'BM'];
                    
                    // Gunakan Regex (Case Insensitive /i) agar lebih galak mencari singkatan
                    foreach ($acronyms as $i => $search) {
                        $text = preg_replace('/\b' . $search . '\b/i', $replacements[$i], $text);
                    }

                    // Perbaikan Khusus
                    $text = str_replace(['Al-fath', "'ulum"], ['Al-Fath', "'Ulum"], $text);
                    
                    return $text;
                };

                $dbUnits = array_map($formatUnit, $dbUnitsRaw);
                
                // Daftar Lembaga Luar / Tetangga (Hanya untuk Absen, bukan untuk Nomor Surat)
                $externalUnitsRaw = ["DKM Syamsul 'Ulum", "Badan Mentoring (BM)", "MQ", "PRISMA", "LAZISSU"];
                $externalUnits = array_map($formatUnit, $externalUnitsRaw);

                // Gabungkan untuk Absen
                $allUnits = array_unique(array_merge($dbUnits, $externalUnits));
                sort($allUnits);
                
                // Pisahkan untuk Nomor Surat (Hanya yang dari DB / Al-Fath)
                $pusatUnits = [];
                $ldfUnits = [];
                foreach($dbUnits as $u) {
                    if(str_contains(strtoupper($u), 'LDF') || str_contains(strtoupper($u), 'FAKULTAS')) {
                        $ldfUnits[] = $u;
                    } else {
                        $pusatUnits[] = $u;
                    }
                }
                
                // Bagikan ke semua Blade
                $view->with(compact('allUnits', 'pusatUnits', 'ldfUnits'));

                // Fallback jika kosong
                if(empty($allUnits)) {
                    $allUnits = ['Biro Kesekretariatan', 'LDF Al-Fath'];
                }
            } catch (\Exception $e) {
                $allUnits = ['Biro Kesekretariatan'];
                $view->with(['allUnits' => $allUnits, 'pusatUnits' => [], 'ldfUnits' => []]);
            }

            // 4. THEME & LOGO (Khusus User Login)
            if (Auth::check()) {
                $user = Auth::user();
                $themes = [
                    'red'         => ['bg' => 'bg-red-700',      'light' => 'bg-red-50',      'text' => 'text-red-700',      'border' => 'border-red-500',   'hover' => 'hover:bg-red-800',     'ring' => 'focus:ring-red-500'],
                    'yellow'      => ['bg' => 'bg-yellow-500',   'light' => 'bg-yellow-50',   'text' => 'text-yellow-700',   'border' => 'border-yellow-500', 'hover' => 'hover:bg-yellow-600',  'ring' => 'focus:ring-yellow-500'], 
                    'blue_dark'   => ['bg' => 'bg-blue-900',     'light' => 'bg-blue-50',     'text' => 'text-blue-900',     'border' => 'border-blue-900',   'hover' => 'hover:bg-blue-800',    'ring' => 'focus:ring-blue-500'],   
                    'orange'      => ['bg' => 'bg-orange-500',   'light' => 'bg-orange-50',   'text' => 'text-orange-600',   'border' => 'border-orange-500', 'hover' => 'hover:bg-orange-600',  'ring' => 'focus:ring-orange-500'], 
                    'purple'      => ['bg' => 'bg-purple-700',   'light' => 'bg-purple-50',   'text' => 'text-purple-700',   'border' => 'border-purple-700', 'hover' => 'hover:bg-purple-800',  'ring' => 'focus:ring-purple-500'], 
                    'green_dark'  => ['bg' => 'bg-emerald-800',  'light' => 'bg-emerald-50',  'text' => 'text-emerald-800',  'border' => 'border-emerald-800','hover' => 'hover:bg-emerald-900', 'ring' => 'focus:ring-emerald-500'],
                    'green_light' => ['bg' => 'bg-lime-600',     'light' => 'bg-lime-50',     'text' => 'text-lime-700',     'border' => 'border-lime-600',   'hover' => 'hover:bg-lime-700',    'ring' => 'focus:ring-lime-500'],   
                    'blue_light'  => ['bg' => 'bg-cyan-600',     'light' => 'bg-cyan-50',     'text' => 'text-cyan-700',     'border' => 'border-cyan-600',   'hover' => 'hover:bg-cyan-700',    'ring' => 'focus:ring-cyan-500'],   
                ];

                $userColor = $user->color_code ?? 'red';
                $theme = $themes[$userColor] ?? $themes['red'];
                $unitName = $user->unit;
                $isKestari = ($user->role == 'superadmin' || $unitName == 'Biro Kesekretariatan');

                // Auto-Detect Logo berdasarkan Kata Kunci di Nama Unit
                $logoFile = 'LogoPusat.png'; // Default
                $searchKeywords = [
                    'Informatika' => 'LogoFIF.png',
                    'Elektro'     => 'LogoFTE.png',
                    'Kreatif'     => 'LogoFIK.png',
                    'Sosial'      => 'LogoFKS.png',
                    'Industri'    => 'LogoFRI.png',
                    'Terapan'     => 'LogoFIT.png',
                    'Bisnis'      => 'LogoFEB.png',
                ];

                foreach($searchKeywords as $keyword => $file) {
                    if(strpos($unitName, $keyword) !== false) {
                        $logoFile = $file;
                        break;
                    }
                }

                $view->with(compact('theme', 'unitName', 'isKestari', 'logoFile', 'user'));
            }
        });
    }
}