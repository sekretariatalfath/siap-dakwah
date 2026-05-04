<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // --- BIRO PUSAT (LDP) ---
            [
                'name' => ' Admin Biro Kesekretariatan',
                'email' => 'kestari.pusat@alfathunitel.org',
                'password' => 'kstr13@123',
                'role' => 'superadmin',
                'unit' => 'Biro Kesekretariatan',
                'color_code' => 'red',
            ],
            [
                'name' => 'Admin Biro Keuangan',
                'email' => 'keuangan.pusat@alfathunitel.org',
                'password' => 'kugn13@123',
                'role' => 'admin',
                'unit' => 'Biro Keuangan',
                'color_code' => 'red',
            ],
            [
                'name' => 'Admin Departemen Syiar Pusat',
                'email' => 'syiar.pusat@alfathunitel.org',
                'password' => 'syrp13@123',
                'role' => 'admin',
                'unit' => 'Departemen Syiar Pusat',
                'color_code' => 'red',
            ],
            [
                'name' => 'Admin Departemen Kaderisasi Pusat',
                'email' => 'kaderisasi.pusat@alfathunitel.org',
                'password' => 'kdrp13@123',
                'role' => 'admin',
                'unit' => 'Departemen Kaderisasi Pusat',
                'color_code' => 'red',
            ],
            [
                'name' => 'Admin Departemen MedKomInfo',
                'email' => 'medkom.pusat@alfathunitel.org',
                'password' => 'mdkm13@123',
                'role' => 'admin',
                'unit' => 'Departemen MedKomInfo',
                'color_code' => 'red',
            ],

            // --- LDF (FAKULTAS) ---
            [
                'name' => 'Admin LDF FIF',
                'email' => 'fif.fakultas@alfathunitel.org',
                'password' => 'fktfif13@123',
                'role' => 'admin',
                'unit' => 'LDF Al-Fath Fakultas Informatika',
                'color_code' => 'yellow',
            ],
            [
                'name' => 'Admin LDF FTE',
                'email' => 'fte.fakultas@alfathunitel.org',
                'password' => 'fktfte13@123',
                'role' => 'admin',
                'unit' => 'LDF Al-Fath Fakultas Teknik Elektro',
                'color_code' => 'blue_dark',
            ],
            [
                'name' => 'Admin LDF FIK',
                'email' => 'fik.fakultas@alfathunitel.org',
                'password' => 'fktfik13@123',
                'role' => 'admin',
                'unit' => 'LDF Al-Fath Fakultas Industri Kreatif',
                'color_code' => 'orange',
            ],
            [
                'name' => 'Admin LDF FIT',
                'email' => 'fit.fakultas@alfathunitel.org',
                'password' => 'fktfit13@123',
                'role' => 'admin',
                'unit' => 'LDF Al-Fath Fakultas Ilmu Terapan',
                'color_code' => 'green_light',
            ],
            [
                'name' => 'Admin LDF FKS',
                'email' => 'fks.fakultas@alfathunitel.org',
                'password' => 'fktfks13@123',
                'role' => 'admin',
                'unit' => 'LDF Al-Fath Fakultas Komunikasi dan Ilmu Sosial',
                'color_code' => 'purple',
            ],
            [
                'name' => 'Admin LDF FRI',
                'email' => 'fri.fakultas@alfathunitel.org',
                'password' => 'fktfri13@123',
                'role' => 'admin',
                'unit' => 'LDF Al-Fath Fakultas Rekayasa Industri',
                'color_code' => 'green_dark',
            ],
            [
                'name' => 'Admin LDF FEB',
                'email' => 'feb.fakultas@alfathunitel.org',
                'password' => 'fktfeb13@123',
                'role' => 'admin',
                'unit' => 'LDF Al-Fath Fakultas Ekonomi dan Bisnis',
                'color_code' => 'blue_light',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}