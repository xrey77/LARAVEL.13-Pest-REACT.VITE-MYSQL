<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function generateChart() {
        $sales = Sale::all();
        $data = $sales->map(fn($sale) => [
            'amount' => $sale->amount,
            'date' => $sale->date
        ]);
        return response()->json($data, 200);
    }
}
