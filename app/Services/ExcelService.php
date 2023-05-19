<?php

namespace App\Services;

use App\Jobs\ExcelParsingJob;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Redis;

class ExcelService
{
    public function parsFile($file): void
    {
        $redis = new Redis();

        $path = storage_path('app/' . $file->storeAs('temp', $file->getClientOriginalName()));

        $spreadsheet = IOFactory::load($path);
        $worksheet = $spreadsheet->getActiveSheet();

        $totalRows = $worksheet->getHighestRow();

        $size = 1000;

        $total = ceil($totalRows / $size);

        $progressKey = uniqid();

        $redis->set($progressKey . ':totalRows', $totalRows);

        for ($i = 1; $i <= $total; $i++) {
            $startRow = ($i - 1) * $size + 1;
            $endRow = min($startRow + $size - 1, $totalRows);

            ExcelParsingJob::dispatch($path, $startRow, $endRow, $progressKey);
        }
    }
}
