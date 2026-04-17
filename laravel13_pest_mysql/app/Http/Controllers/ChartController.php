<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Services\KafkaProducerService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ChartController extends Controller
{
    public function generateChart(KafkaProducerService $kafkaService): JsonResponse 
    {
        $sales = Sale::all();

        // Check if empty before processing
        if ($sales->isEmpty()) {
            return response()->json(['message' => 'Sales data not found.'], 404);
        }

        // Map data for response
        $salesData = $sales->map(fn($sale) => [
            'salesamount' => $sale->salesamount,
            'salesdate' => $sale->salesdate
        ]);

        // Kafka message payload
        $data = [
            'event' => 'sales_chart',
            'sales' => $sales->toArray() // Use -> instead of .
        ];

        // Publish to Kafka
        $kafkaService->publishMessage('central-topic', $data);

        return response()->json($salesData, 200);
    }
}
