<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Services\GoogleSheetService;

class AccountController extends Controller
{
    /**
     * Tampilan Halaman Pengaturan & Akun
     */
    public function index()
    {
        $users = User::all();
        $currentUser = Auth::user();
        
        // Auto-sync CP dari Google Sheets ketika masuk ke halaman pengaturan
        try {
            $this->syncCp();
        } catch (\Exception $e) {
            // Silently ignore agar tidak merusak halaman jika API error / offline
        }
        
        // Ambil status saklar langsung dari DB buat halaman pengaturan
        $setting = \Illuminate\Support\Facades\DB::table('settings')->where('key', 'auth_source')->first();
        $authSource = $setting ? $setting->value : 'database';
        
        return view('dashboard.settings', compact('users', 'currentUser', 'authSource'));
    }

    /**
     * Ganti Password User Login
     */
    public function changePassword(Request $request)
    {
        // Cek Saklar
        $setting = \Illuminate\Support\Facades\DB::table('settings')->where('key', 'auth_source')->first();
        if (($setting ? $setting->value : 'database') === 'hardcode') {
            return back()->with('error', 'Fitur ganti password terkunci karena sistem sedang menggunakan mode Hardcode.');
        }

        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * Register User Baru (Hanya Superadmin)
     */
    public function register(Request $request)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        // Cek Saklar
        $setting = \Illuminate\Support\Facades\DB::table('settings')->where('key', 'auth_source')->first();
        if (($setting ? $setting->value : 'database') === 'hardcode') {
            return back()->with('error', 'Fitur pendaftaran user terkunci karena sistem sedang menggunakan mode Hardcode.');
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,superadmin',
            'unit'     => 'required|string',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'unit'     => $request->unit,
            'color_code' => '#ef4444', // Default red
        ]);

        return back()->with('success', 'User ' . $request->name . ' berhasil didaftarkan.');
    }

    /**
     * Reset Password User (Hanya Superadmin)
     */
    public function resetUserPassword(Request $request, $id)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        $request->validate(['new_password' => 'required|min:6']);
        
        $user = User::findOrFail($id);
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password user ' . $user->name . ' telah di-reset.');
    }

    /**
     * Hapus User (Hanya Superadmin)
     */
    public function deleteUser($id)
    {
        if (Auth::user()->role !== 'superadmin' || Auth::id() == $id) {
            abort(403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }

    /**
     * FITUR BERSIHKAN SPS (Resource Only)
     */
    public function purgeSps(Request $request)
    {
        if (Auth::user()->role !== 'superadmin' && Auth::user()->unit !== 'Kestari') {
            abort(403);
        }

        $service = new GoogleSheetService();
        $resourceId = env('GSHEET_RESOURCE_ID');

        try {
            $service->clearSheet($resourceId, 'Templates!A2:E');
            $service->clearSheet($resourceId, 'Pedoman!A2:E');
            $service->clearSheet($resourceId, 'Informasi!A2:E');

            return back()->with('success', 'Data Resource (Templates, SOP, Informasi) berhasil dibersihkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * MASTER RESET: Bersihkan SEMUA data transaksi (Local & Cloud)
     */
    public function hardReset(Request $request)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        try {
            $service = new GoogleSheetService();

            // 1. CLEAR CLOUD (Google Sheets)
            $service->clearSheet(env('GSHEET_SURAT_KELUAR_ID'), 'Surat_db!A2:R');
            $service->clearSheet(env('GSHEET_SURAT_MASUK_ID'), 'SuratMasuk!A2:L');
            $service->clearSheet(env('GSHEET_PROPOSAL_ID'), 'Proposal_db!A2:P');
            $service->clearSheet(env('GSHEET_LPJ_ID'), 'Lpj_db!A2:M');
            $service->clearSheet(env('GSHEET_EVALUASI_ID'), 'Evaluasi_db!A2:G');
            $service->clearSheet(env('GSHEET_BERITA_ACARA_ID'), 'BeritaAcara_db!A2:G');
            $service->clearSheet(env('GSHEET_PRESENSI_ID'), 'Sesi_Presensi!A2:F');
            $service->clearSheet(env('GSHEET_PRESENSI_ID'), 'Presensi_Detail!A2:I');
            $service->clearSheet(env('GSHEET_RESOURCE_ID'), 'Templates!A2:E');
            $service->clearSheet(env('GSHEET_RESOURCE_ID'), 'Pedoman!A2:E');
            $service->clearSheet(env('GSHEET_RESOURCE_ID'), 'Informasi!A2:E');

            // 2. CLEAR LOCAL (Truncate Tables)
            \App\Models\Surat::truncate();
            \App\Models\SuratMasuk::truncate();
            \App\Models\Proposal::truncate();
            \App\Models\Notulensi::truncate();
            \App\Models\BeritaAcara::truncate(); // Meskipun Sushi, tetap panggil jika ada data lokal
            \App\Models\Lpj::truncate();
            \App\Models\Evaluasi::truncate();
            \App\Models\SesiPresensi::truncate();

            return back()->with('success', 'SYSTEM RESET BERHASIL. Seluruh data transaksi lokal & cloud telah dibersihkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Master Reset: ' . $e->getMessage());
        }
    }

    /**
     * TOGGLE AUTH SOURCE (Hanya Superadmin)
     */
    public function toggleAuthSource(Request $request)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403);
        }

        $request->validate([
            'source' => 'required|in:database,hardcode',
        ]);

        \Illuminate\Support\Facades\DB::table('settings')
            ->where('key', 'auth_source')
            ->update([
                'value' => $request->source,
                'updated_at' => now(),
            ]);

        // Hapus cache biar langsung ngefek
        \Illuminate\Support\Facades\Cache::forget('auth_source');

        return back()->with('success', 'Mode otentikasi berhasil diubah ke ' . $request->source);
    }

    /**
     * SYNC CONTACT PERSON (CP) DARI GOOGLE SHEETS
     */
    public function syncCp(Request $request = null)
    {
        if ($request && Auth::user()->role !== 'superadmin' && Auth::user()->unit !== 'Biro Kesekretariatan') {
            abort(403);
        }

        try {
            $service = new GoogleSheetService();
            $spreadsheetId = env('GSHEET_CP_ID');

            $response = $service->getService()->spreadsheets_values->get($spreadsheetId, 'CP_siapdakwah_db!A2:C');
            $rows = $response->getValues() ?? [];

            $cpPusat = [];
            $cpFakultas = [];

            foreach ($rows as $index => $row) {
                $kategori = strtoupper(trim($row[0] ?? ''));
                $nama = trim($row[1] ?? '');
                $wa = trim($row[2] ?? '');
                $rowIndex = $index + 2; // Karena A2 mulai dari baris 2

                if ($nama && $wa) {
                    $wa = preg_replace('/[^0-9]/', '', $wa);

                    $cpData = [
                        'row_index' => $rowIndex,
                        'nama' => $nama,
                        'wa' => $wa
                    ];

                    if ($kategori === 'PUSAT') {
                        $cpPusat[] = $cpData;
                    } elseif ($kategori === 'FAKULTAS') {
                        $cpFakultas[] = $cpData;
                    }
                }
            }

            $contactPersons = [
                'PUSAT' => $cpPusat,
                'FAKULTAS' => $cpFakultas
            ];

            \Illuminate\Support\Facades\Cache::put('contact_persons_footer', $contactPersons, now()->addDays(30));

            if ($request) {
                return back()->with('success', 'Kontak Person berhasil disinkronisasi dari Google Sheets!');
            }
        } catch (\Exception $e) {
            if ($request) {
                return back()->with('error', 'Gagal sinkronisasi CP: ' . $e->getMessage());
            }
        }
    }

    /**
     * STORE CP BARU
     */
    public function storeCp(Request $request)
    {
        if (Auth::user()->role !== 'superadmin' && Auth::user()->unit !== 'Biro Kesekretariatan') {
            abort(403);
        }

        $request->validate([
            'kategori' => 'required|in:PUSAT,FAKULTAS',
            'nama' => 'required|string',
            'wa' => 'required|string'
        ]);

        try {
            $service = new GoogleSheetService();
            $spreadsheetId = env('GSHEET_CP_ID');

            // Append to Google Sheets
            $values = [$request->kategori, $request->nama, $request->wa];
            $service->appendSheet($spreadsheetId, 'CP_siapdakwah_db!A2:C', $values);

            // Resync cache
            $this->syncCp();

            return back()->with('success', 'Kontak Person berhasil ditambahkan dan disimpan ke SPS!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan CP: ' . $e->getMessage());
        }
    }

    /**
     * HAPUS CP
     */
    public function destroyCp($rowIndex)
    {
        if (Auth::user()->role !== 'superadmin' && Auth::user()->unit !== 'Biro Kesekretariatan') {
            abort(403);
        }

        try {
            $service = new GoogleSheetService();
            $spreadsheetId = env('GSHEET_CP_ID');

            // Hapus isi baris tersebut dengan mengosongkan cell-nya (A-C)
            // Menggunakan updateCell untuk mereplace dengan string kosong
            $service->updateCell($spreadsheetId, "CP_siapdakwah_db!A{$rowIndex}:C{$rowIndex}", ['', '', '']);

            // Resync cache (baris kosong otomatis di-skip oleh syncCp)
            $this->syncCp();

            return back()->with('success', 'Kontak Person berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus CP: ' . $e->getMessage());
        }
    }
}
