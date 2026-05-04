<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;
use App\Services\GoogleSheetService;

class SesiPresensi extends Model
{
    use Sushi;

    protected $table = 'sesi_presensi_sushi';
    public $timestamps = false;

    protected $schema = [
        'id_sesi'         => 'string',
        'nama_kegiatan'   => 'string',
        'kategori'        => 'string',
        'unit_host'       => 'string',
        'tgl_pelaksanaan' => 'string',
        'is_active'       => 'string',
    ];

    public function getRows()
    {
        try {
            $service = app(GoogleSheetService::class); // PAKAI app()
            $spreadsheetId = env('GSHEET_PRESENSI_ID');
            $rows = $service->readSheet($spreadsheetId, 'Sesi_Presensi!A2:F');

            if (!$rows) return [];

            return collect($rows)
                ->filter(fn($row) => !empty($row[0])) // Abaikan baris kosong
                ->map(function ($row) {
                return [
                    'id_sesi'         => $row[0] ?? '',
                    'nama_kegiatan'   => $row[1] ?? '',
                    'kategori'        => $row[2] ?? '',
                    'unit_host'       => $row[3] ?? '',
                    'tgl_pelaksanaan' => $row[4] ?? '',
                    'is_active'       => $row[5] ?? '0',
                ];
            })->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    public static function find($idSesi)
    {
        return self::all()->where('id_sesi', $idSesi)->first();
    }
}