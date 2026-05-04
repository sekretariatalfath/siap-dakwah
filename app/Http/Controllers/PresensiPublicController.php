<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SesiPresensi;
use Illuminate\Support\Str;

class PresensiPublicController extends Controller
{
    public function showForm($idSesi)
    {
        $sesi = SesiPresensi::find($idSesi);

        if (!$sesi || $sesi->is_active == '0') {
            return response()->view('presensi.closed', compact('sesi'), 403);
        }

        return view('presensi.public_form', compact('sesi'));
    }

    public function submitHadir(Request $request, $idSesi)
    {
        $request->validate([
            'email' => 'required|email',
            'nim' => 'required|numeric',
            'nama' => 'required|string|max:255',
            'amanah' => 'required',
            'asal_wajihah' => 'required',
            'status_kehadiran' => 'required|in:Hadir,Izin,Sakit,Alpa',
        ]);

        $sesi = SesiPresensi::find($idSesi);
        if (!$sesi || $sesi->is_active == '0') {
            return view('presensi.closed', compact('sesi'));
        }

        // Generate Submission ID jika baru, atau pakai ID lama jika edit
        $submissionId = $request->input('submission_id') ?? 'SUB-' . strtoupper(Str::random(10));

        $data = [
            $idSesi,                            // A
            trim(strtolower($request->email)),  // B
            strtoupper($request->nama),         // C
            $request->amanah,                   // D
            $request->asal_wajihah,             // E
            date('Y-m-d H:i:s'),                // F
            $request->nim,                      // G
            $request->status_kehadiran,         // H
            $submissionId                       // I (Paku Pengunci)
        ];

        try {
            // 3. PANGGIL SERVICE (Gunakan app() sesuai catatan di gambar Mas)[cite: 8, 24]
            $service = app(\App\Services\GoogleSheetService::class);
            
            // Ambil ID dari env[cite: 24, 28]
            $spreadsheetId = env('GSHEET_PRESENSI_ID'); 
            
            // Gunakan updateOrAppend agar jika kader edit absen, datanya tidak double
            $service->updateOrAppend($spreadsheetId, $data);
            
            return view('presensi.success', compact('idSesi', 'submissionId'));
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}