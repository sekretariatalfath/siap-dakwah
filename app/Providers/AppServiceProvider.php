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
        // 1. SET LOCALE CARBON KE INDONESIA
        config(['app.locale' => 'id']);
        Carbon::setLocale('id');

        // 2. SET TIMEZONE WIB
        date_default_timezone_set('Asia/Jakarta');

        // 3. GLOBAL DASHBOARD THEME (Personalisasi Unit)
        View::composer('*', function ($view) {
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

                $logoMap = [
                    'Biro Kesekretariatan'                            => 'LogoPusat.png',
                    'Biro Keuangan'                                   => 'LogoPusat.png',
                    'Departemen Syiar Pusat'                          => 'LogoPusat.png',
                    'Departemen Kaderisasi Pusat'                     => 'LogoPusat.png',
                    'Departemen Medkominfo'                           => 'LogoPusat.png',
                    'LDF Al-Fath Fakultas Informatika'                => 'LogoFIF.png',
                    'LDF Al-Fath Fakultas Teknik Elektro'             => 'LogoFTE.png',
                    'LDF Al-Fath Fakultas Industri Kreatif'           => 'LogoFIK.png',
                    'LDF Al-Fath Fakultas Komunikasi dan Ilmu Sosial' => 'LogoFKS.png',
                    'LDF Al-Fath Fakultas Rekayasa Industri'          => 'LogoFRI.png',
                    'LDF Al-Fath Fakultas Ilmu Terapan'               => 'LogoFIT.png',
                    'LDF Al-Fath Fakultas Ekonomi dan Bisnis'         => 'LogoFEB.png',
                ];
                $logoFile = $logoMap[$unitName] ?? 'LogoPusat.png';

                $allUnits = [
                    'Biro Kesekretariatan', 'Biro Keuangan', 'Departemen Syiar Pusat', 
                    'Departemen Kaderisasi Pusat', 'Departemen Medkominfo',
                    'LDF Al-Fath Fakultas Informatika', 'LDF Al-Fath Fakultas Teknik Elektro',
                    'LDF Al-Fath Fakultas Industri Kreatif', 'LDF Al-Fath Fakultas Komunikasi dan Ilmu Sosial',
                    'LDF Al-Fath Fakultas Rekayasa Industri', 'LDF Al-Fath Fakultas Ilmu Terapan',
                    'LDF Al-Fath Fakultas Ekonomi dan Bisnis'
                ];

                $view->with(compact('theme', 'unitName', 'isKestari', 'logoFile', 'user', 'allUnits'));
            }
        });
    }
}