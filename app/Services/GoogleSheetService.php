<?php

namespace App\Services;

use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

class GoogleSheetService
{
    protected $service;

    public function __construct()
    {
        $client = new \Google\Client();
        
        $clientId     = config('services.google.client_id') ?? env('GOOGLE_CLIENT_ID');
        $clientSecret = config('services.google.client_secret') ?? env('GOOGLE_CLIENT_SECRET');
        $refreshToken = config('services.google.refresh_token') ?? env('GOOGLE_REFRESH_TOKEN');

        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->refreshToken($refreshToken);

        $client->addScope(\Google\Service\Sheets::SPREADSHEETS);
        $this->service = new \Google\Service\Sheets($client);
    }

    public function readSheet($spreadsheetId, $range)
    {
        try {
            $response = $this->service->spreadsheets_values->get($spreadsheetId, $range);
            return $response->getValues();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function appendSheet($spreadsheetId, $range, $values)
    {
        // Pastikan $values selalu terbungkus satu kali sebagai baris baru
        $body = new ValueRange([
            'values' => [$values]
        ]);
        
        $params = ['valueInputOption' => 'USER_ENTERED'];
        return $this->service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
    }

    /**
     * Update Cell: Diperbaiki agar tidak membungkus array secara berlebihan
     */
    public function updateCell($spreadsheetId, $range, $value)
    {
        // LOGIKA BARU: Jika sudah array multidimensi, pakai langsung.
        // Jika cuma satu baris, bungkus jadi multidimensi.
        if (is_array($value)) {
            $data = is_array($value[0] ?? null) ? $value : [$value];
        } else {
            $data = [[$value]];
        }

        $body = new ValueRange(['values' => $data]);
        
        // Pakai USER_ENTERED agar Google menganggap kita mengetik (atau menghapus) manual
        $params = ['valueInputOption' => 'USER_ENTERED'];
        
        return $this->service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
    }

    /**
     * Tambahan fungsi Clear: Lebih bersih untuk menghapus data
     */
    public function clearSheet($spreadsheetId, $range)
    {
        $requestBody = new \Google\Service\Sheets\ClearValuesRequest();
        return $this->service->spreadsheets_values->clear($spreadsheetId, $range, $requestBody);
    }

    /**
     * Logika: Mencari Submission ID di Kolom I (Index 8)
     */
    public function updateOrAppend($spreadsheetId, $data)
    {
        // Ambil data di Kolom I (Submission ID)
        $response = $this->service->spreadsheets_values->get($spreadsheetId, 'Presensi_Detail!I:I');
        $rows = $response->getValues();
        
        $foundRowIndex = -1;
        $newSubmissionId = $data[8] ?? null;

        if ($rows && $newSubmissionId) {
            foreach ($rows as $index => $row) {
                if (isset($row[0]) && trim($row[0]) === trim($newSubmissionId)) {
                    $foundRowIndex = $index + 1; 
                    break;
                }
            }
        }

        $body = new ValueRange(['values' => [$data]]);
        $params = ['valueInputOption' => 'RAW'];

        if ($foundRowIndex !== -1) {
            // REPLACE: Timpa baris lama
            $range = "Presensi_Detail!A{$foundRowIndex}:I{$foundRowIndex}";
            return $this->service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
        }

        // APPEND: Input baru
        return $this->service->spreadsheets_values->append($spreadsheetId, 'Presensi_Detail!A2', $body, $params);
    }

    public function getService()
    {
        return $this->service;
    }
}