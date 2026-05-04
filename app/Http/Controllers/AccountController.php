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
        return view('dashboard.settings', compact('users', 'currentUser'));
    }

    /**
     * Ganti Password User Login
     */
    public function changePassword(Request $request)
    {
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
}
