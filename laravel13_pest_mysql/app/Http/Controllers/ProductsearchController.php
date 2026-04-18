<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;
use App\Services\KafkaProducerService;

class ProductsearchController extends Controller
{
    #[OA\Get(
        path: '/api/productsearch/{page}/{key}',
        tags: ['Products'],
        summary: 'Search products by description',
    )]
    #[OA\Parameter(
        name: 'key',
        in: 'path',
        required: true,
        description: 'Search keyword for descriptions',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: 200,
        description: 'Searched found',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Searched found..'),
                new OA\Property(
                    property: 'products',
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Product')
                )
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Product not found')]
    #[OA\Response(response: 500, description: 'Server Error')]   
    public function productSearch(Request $request, int $page, string $key, KafkaProducerService $kafkaService) {

        $perPage = 5;
        $skip = ($page - 1) * $perPage;

        try {

            $products = Product::skip($skip)->take($perPage)->get();
            $query = Product::where('descriptions', 'LIKE', '%' . $key . '%');
            $totalrecords = $query->count();
            $totpage = ceil($totalrecords / $perPage);
            $products = $query->skip($skip)->take($perPage)->get();

            if ($products->isEmpty()) {
                return response()->json(['message' => 'Product not found.'], 404);
            }

            $data = [
                'event' => 'product_search',
                'products' => $products->toArray(),
                'timestamp' => now()->toIso8601String(),
            ];            

            $kafkaService->publishMessage('central-topic', $data, $products);
            return response()->json(['message' => 'Searched found..',
            'page' => $page,
            'totpage' => $totpage,
            'totalrecords' => $totalrecords,
            'products' => $products],200);

        } catch(\Exceptions $e) {
            return response()->json(['message' => $e->getMessage()],500);
        }

    }    
}
