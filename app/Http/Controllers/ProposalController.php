<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest; // Tambahkan import ini

class ProposalController extends Controller
{
    private $spreadsheetId;

    public function __construct() {
        $this->spreadsheetId = env('GSHEET_PROPOSAL_ID');
    }

    public function index()
    {
        $user = Auth::user();
        $service = app(\App\Services\GoogleSheetService::class)->getService();
        $response = $service->spreadsheets_values->get($this->spreadsheetId, 'Proposal_db!A:P');
        $rows = $response->getValues() ?? [];

        // Map data dari Sheets (Abaikan baris kosong)
        $dataProposal = collect($rows)->forget(0)->filter(fn($r) => !empty($r[0]))->map(function($row) {
            return (object) [
                'proposal_id'    => $row[0] ?? '-',
                'tgl_input'      => $row[1] ?? '-',
                'kategori'       => $row[2] ?? '-',
                'pemohon'        => $row[3] ?? '-',
                'nama_proker'    => $row[4] ?? '-',
                'bentuk_kegiatan'=> $row[5] ?? '-',
                'tempat'         => $row[6] ?? '-',
                'anggaran'       => (int)($row[7] ?? 0),
                'target_peserta' => (int)($row[8] ?? 0),
                'jumlah_panitia' => (int)($row[9] ?? 0),
                'link_pdf'       => $row[10] ?? '#',
                'cp_nama'        => $row[11] ?? '-',
                'cp_wa'          => $row[12] ?? '-',
                'cp_email'       => $row[13] ?? '-',
                'cp_line'        => $row[14] ?? '-',
                'is_checked'     => strtoupper($row[15] ?? 'FALSE') == 'TRUE'
            ];
        })->filter(function($p) use ($user) {
            $isKestari = ($user->role == 'superadmin' || $user->unit == 'Biro Kesekretariatan');
            return $isKestari ? true : $p->pemohon == $user->unit;
        })->reverse();

        return view('dashboard.proposal_index', compact('dataProposal'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $propId = 'PROP-' . strtoupper(Str::random(5));
        
        $data = [
            'proposal_id'    => $propId,
            'kategori'       => $request->kategori,
            'pemohon'        => $user->unit,
            'nama_proker'    => strtoupper($request->nama_proker),
            'bentuk_kegiatan'=> $request->bentuk_kegiatan,
            'tempat'         => strtoupper($request->tempat_kegiatan),
            'anggaran'       => (int)$request->anggaran,
            'target_peserta' => (int)$request->target_peserta,
            'jumlah_panitia' => (int)$request->jumlah_panitia,
            'link_pdf'       => $request->link_pdf,
            'cp_nama'        => strtoupper($request->cp_nim_nama),
            'cp_wa'          => $request->cp_wa,
            'cp_email'       => $request->cp_email,
            'cp_line'        => $request->cp_line ?? '-'
        ];

        try {
            // 1. SIMPAN LOKAL DULU (Prioritas Utama)[cite: 13, 15]
            $proposal = Proposal::create($data);

            // 2. BARU SINKRON KE CLOUD (Mirroring)[cite: 13, 15]
            $this->syncToSheets((object)$data);
            
            return back()->with('success', 'Proposal Berhasil Diajukan & Tersinkron!');
        } catch (\Exception $e) {
            // Jika Cloud gagal, data lokal sudah tersimpan
            return back()->withInput()->with('error', 'Data tersimpan lokal, tapi gagal sinkron ke Cloud: ' . $e->getMessage());
        }
    }

    public function destroy($proposal_id)
    {
        if (Auth::user()->role !== 'superadmin') return back();
        
        try {
            // 1. HAPUS DI CLOUD DULU
            $this->deleteFromSheets($proposal_id, 'Proposal_db');
            
            // 2. HAPUS LOKAL
            Proposal::where('proposal_id', $proposal_id)->delete();

            return back()->with('success', 'Proposal Berhasil Dihapus dari Web & Spreadsheet!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Hapus Cloud: ' . $e->getMessage());
        }
    }

    public function toggleCheck($proposal_id)
    {
        if (Auth::user()->role !== 'superadmin') return back();

        try {
            $service = app(\App\Services\GoogleSheetService::class)->getService();
            $rows = $service->spreadsheets_values->get($this->spreadsheetId, 'Proposal_db!A:P')->getValues();
            
            $targetRow = null;
            $currentStatus = 'FALSE';

            if ($rows) {
                foreach ($rows as $index => $row) {
                    if (($row[0] ?? '') == $proposal_id) {
                        $targetRow = $index + 1;
                        $currentStatus = strtoupper($row[15] ?? 'FALSE');
                        break;
                    }
                }
            }

            if (!$targetRow) throw new \Exception("Data tidak ditemukan!");

            $newStatus = ($currentStatus == 'TRUE') ? 'FALSE' : 'TRUE';
            $body = new ValueRange(['values' => [[$newStatus]]]);
            $service->spreadsheets_values->update($this->spreadsheetId, "Proposal_db!P$targetRow", $body, ['valueInputOption' => 'RAW']);

            // Update lokal (Opsional)
            Proposal::where('proposal_id', $proposal_id)->update(['is_checked' => \Illuminate\Support\Facades\DB::raw($newStatus == 'TRUE' ? 'true' : 'false')]);

            return back()->with('success', 'Status Verifikasi Diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // --- Private Helpers ---

    private function syncToSheets($p)
    {
        $service = app(\App\Services\GoogleSheetService::class)->getService();

        $values = [[
            $p->proposal_id, 
            date('d/m/Y H:i'), 
            $p->kategori, 
            $p->pemohon,
            $p->nama_proker, 
            $p->bentuk_kegiatan, 
            $p->tempat, 
            $p->anggaran, 
            $p->target_peserta, 
            $p->jumlah_panitia, 
            $p->link_pdf, 
            $p->cp_nama, 
            $p->cp_wa, 
            $p->cp_email, 
            $p->cp_line, 
            'FALSE'
        ]];

        $body = new ValueRange(['values' => $values]);
        $service->spreadsheets_values->append($this->spreadsheetId, 'Proposal_db!A1', $body, ['valueInputOption' => 'RAW']);
    }

    private function updateStatusInSheets($p)
    {
        $service = app(\App\Services\GoogleSheetService::class)->getService();
        $rows = $service->spreadsheets_values->get($this->spreadsheetId, 'Proposal_db!A:A')->getValues();
        
        if ($rows) {
            foreach ($rows as $index => $row) {
                if (($row[0] ?? '') == $p->proposal_id) {
                    $rowNum = $index + 1;
                    $newVal = $p->is_checked ? 'TRUE' : 'FALSE';
                    $body = new ValueRange(['values' => [[$newVal]]]);
                    // Kolom P adalah kolom ke-16 (Status Verifikasi)
                    $service->spreadsheets_values->update($this->spreadsheetId, "Proposal_db!P$rowNum", $body, ['valueInputOption' => 'RAW']);
                    break;
                }
            }
        }
    }

    private function deleteFromSheets($idValue, $sheetName)
    {
        $service = app(\App\Services\GoogleSheetService::class)->getService();
        $rows = $service->spreadsheets_values->get($this->spreadsheetId, "$sheetName!A:A")->getValues();
        
        if ($rows) {
            foreach ($rows as $index => $row) {
                if (($row[0] ?? '') == $idValue) {
                    // StartIndex di API Google Sheets bersifat inclusive (mulai dari 0)
                    $batchUpdate = new BatchUpdateSpreadsheetRequest([
                        'requests' => [
                            'deleteDimension' => [
                                'range' => [
                                    'sheetId' => env('GID_PROPOSAL', 0), // Pastikan GID tab Proposal_db adalah 0
                                    'dimension' => 'ROWS',
                                    'startIndex' => $index,
                                    'endIndex' => $index + 1
                                ]
                            ]
                        ]
                    ]);
                    $service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdate);
                    break;
                }
            }
        }
    }
}