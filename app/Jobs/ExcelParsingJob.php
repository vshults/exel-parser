<?php

namespace App\Jobs;

use App\Models\Row;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Redis;
use Illuminate\Support\Facades\DB;
use App\Events\RowCreated;

class ExcelParsingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $filePath;
    protected int $startRow;
    protected int $endRow;
    protected string $progressKey;

    /**
     * Create a new job instance.
     *
     * @param string $filePath
     * @param int    $startRow
     * @param int    $endRow
     * @param string $progressKey
     *
     * @return void
     */
    public function __construct(string $filePath, int $startRow, int $endRow, string $progressKey)
    {
        $this->filePath = $filePath;
        $this->startRow = $startRow;
        $this->endRow = $endRow;
        $this->progressKey = $progressKey;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \RedisException
     */
    public function handle()
    {
        $redis = new Redis();
        $spreadsheet = IOFactory::load($this->filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        for ($row = $this->startRow + 1; $row <= $this->endRow; $row++) {
            $id = $worksheet->getCell('A' . $row)->getCalculatedValue();
            $name = $worksheet->getCell('B' . $row)->getValue();
            $date = $worksheet->getCell('C' . $row)->getValue();

            if ($id !== null && $name !== null && $date !== null) {

                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date);
                $formattedDate = $date->format('Y-m-d');

                $result = (new Row)->updateOrCreate(['id' => $id,], ['name' => $name, 'date' => $formattedDate]);
                event(new RowCreated($result));
            }

            $redis->incr($this->progressKey . ':processedRows');
        }
    }
}
