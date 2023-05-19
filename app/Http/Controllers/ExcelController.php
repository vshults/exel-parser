<?php

namespace App\Http\Controllers;

use App\Services\ExcelService;
use Illuminate\Http\Request;

class ExcelController extends Controller
{
    /**
     * @var \App\Services\ExcelService
     */
    private ExcelService $excelService;

    public function __construct()
    {
        $this->excelService = new ExcelService;
    }

    public function upload(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        $file = $request->file('file');

        $this->excelService->parsFile($file);

        return response()->json(['message' => 'File uploaded and processed successfully']);
    }
}
