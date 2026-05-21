<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = new \App\Services\GoogleSheetService();
$spreadsheetId = env('GSHEET_EVALUASI_ID');

if (!$spreadsheetId) {
    echo "NO SPREADSHEET ID FOUND\n";
    exit;
}

$rows = $service->readSheet($spreadsheetId, 'Evaluasi_db!A2:H');
$units = [];
$updateData = [];

foreach ($rows as $index => $row) {
    if (isset($row[5]) && $row[5] === 'UNIT') {
        $units[] = [
            'row' => $index + 2,
            'id_eval' => $row[0] ?? '-',
            'proker' => $row[3] ?? '-',
            'pemohon' => $row[5]
        ];
        
        // We will update all UNIT to FIT as requested by user
        $updateData[] = [
            'range' => 'Evaluasi_db!F' . ($index + 2),
            'values' => [['FIT']]
        ];
    }
}

echo json_encode($units, JSON_PRETTY_PRINT) . "\n";

if (count($updateData) > 0) {
    $client = $service->getService();
    $batchRequest = new \Google\Service\Sheets\BatchUpdateValuesRequest([
        'valueInputOption' => 'USER_ENTERED',
        'data' => $updateData
    ]);
    
    try {
        $client->spreadsheets_values->batchUpdate($spreadsheetId, $batchRequest);
        echo count($updateData) . " ROWS UPDATED TO FIT SUCCESFULLY\n";
    } catch (\Exception $e) {
        echo "ERROR UPDATING: " . $e->getMessage() . "\n";
    }
} else {
    echo "NO ROWS NEED FIXING\n";
}
