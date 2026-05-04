<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class BeritaAcara extends Model
{
    use Sushi;

    // Tentukan nama tabel manual (Gunakan nama unik)
    protected $table = 'berita_acara_sushi';

    public $timestamps = false;

    protected $schema = [
        'id'             => 'integer',
        'waktu_input'    => 'string',
        'email_pembuat'  => 'string',
        'departemen'     => 'string',
        'nama_kegiatan'  => 'string',
        'ketua'          => 'string',
        'link_dokumen'   => 'string',
        'asal_unit_akun' => 'string',
    ];


    public function getRows()
    {
        $client = new \Google\Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->refreshToken(env('GOOGLE_REFRESH_TOKEN'));

        $service = new \Google\Service\Sheets($client);
        $spreadsheetId = env('GSHEET_BERITA_ACARA_ID'); 
        $range = 'BeritaAcara_db!A2:G'; 

        try {
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $rows = $response->getValues();
            if (!$rows) return [];

            return collect($rows)
                ->filter(fn($row) => !empty($row[0]))
                ->map(function ($row, $index) {
                return [
                    'id'             => $index + 1,
                    'waktu_input'    => $row[0] ?? '',
                    'email_pembuat'  => $row[1] ?? '',
                    'departemen'     => $row[2] ?? '',
                    'nama_kegiatan'  => $row[3] ?? '',
                    'ketua'          => $row[4] ?? '',
                    'link_dokumen'   => $row[5] ?? '',
                    'asal_unit_akun' => $row[6] ?? '-', 
                ];
            })->toArray();
        } catch (\Exception $e) {
            return []; 
        }
    }
}