<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;
use App\Services\GoogleSheetService;

class Evaluasi extends Model
{
    use Sushi;

    protected $table = 'evaluasi_sushi';
    public $timestamps = false;

    protected $schema = [
        'id'          => 'integer',
        'id_eval'     => 'string',
        'tgl_rapat'   => 'string',
        'kategori'    => 'string',
        'nama_proker' => 'string',
        'link_doc'    => 'string',
        'pemohon'     => 'string',
        'status'      => 'string',
        'tempat'      => 'string',
    ];

    public function getRows()
    {
        $service = new GoogleSheetService();
        $spreadsheetId = env('GSHEET_EVALUASI_ID');
        $rows = $service->readSheet($spreadsheetId, 'Evaluasi_db!A2:H');

        if (!$rows) return [];

        return collect($rows)
            ->filter(fn($row) => !empty($row[0]))
            ->map(function ($row, $index) {
            return [
                'id'          => $index + 1,
                'id_eval'     => $row[0] ?? null,
                'tgl_rapat'   => $row[1] ?? null,
                'kategori'    => $row[2] ?? null,
                'nama_proker' => $row[3] ?? null,
                'link_doc'    => $row[4] ?? null,
                'pemohon'     => $row[5] ?? null,
                'status'      => $row[6] ?? 'FALSE',
                'tempat'      => $row[7] ?? '-',
            ];
        })->toArray();
    }
}