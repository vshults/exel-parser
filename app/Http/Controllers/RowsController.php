<?php

namespace App\Http\Controllers;

use App\Models\Row;

class RowsController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json((new Row)->orderBy('date')->get()->toArray());
    }
}
