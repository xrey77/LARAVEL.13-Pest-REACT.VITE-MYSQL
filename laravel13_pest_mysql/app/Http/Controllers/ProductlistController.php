<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;
use App\Services\KafkaProducerService;

class ProductlistController extends Controller
{
    #[OA\Get(
        path: '/api/productlist/{page}',
        summary: 'Get paginated list of products',
        tags: ['Products']
    )]
    #[OA\Parameter(
        name: 'page',
        in: 'path',
        required: true,
        description: 'The page number to retrieve',
        schema: new OA\Schema(type: 'integer')
    )]    
    #[OA\Response(
        response: 200,
        description: 'Product Retrieved Successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Product Retrieved Successfully.'),
                new OA\Property(property: 'totalrecords', type: 'integer', example: 50),
                new OA\Property(property: 'page', type: 'integer', example: 1),
                new OA\Property(property: 'totpage', type: 'integer', example: 10),
                new OA\Property(
                    property: 'products',
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Product')
                )
            ]
        )
    )]
    #[OA\Response(response: 404, description: 'Products is empty')]
    #[OA\Response(response: 500, description: 'Server Error')]
    public function listProducts(Request $request, KafkaProducerService $kafkaService, int $page) 
    {
        $perPage = 5;
        $skip = ($page - 1) * $perPage;
        try {
            $products = Product::skip($skip)->take($perPage)->get();
            $totalrecords = Product::count(); 
            $totpage = ceil($totalrecords / $perPage);

            if ($products->isEmpty()) {
                return response()->json(['message' => 'Product not found.'], 404);
            }

            $data = [
                'event' => 'product_list',
                'totalrecords' => $totalrecords,
                'page' => $page,
                'totpage' => $totpage,
                'products' => $products
            ];            

            $kafkaService->publishMessage('central-topic', $data, $products);
            return response()->json(['message' => 'Product Retrieved Successfully.', 'totalrecords' => $totalrecords, 'page' => $page,'totpage'=> $totpage, 'products' => $products],200);
        } catch(\Exceptions $e) {
            return response()->json(['message' => $e->getMessage()],500);
        }
    }
    
}
