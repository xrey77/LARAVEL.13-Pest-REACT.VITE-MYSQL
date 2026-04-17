<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\KafkaProducerService;
use OpenApi\Attributes as OA;

class AddproductController extends Controller
{
    #[OA\Post(
        path: "/addproduct",
        tags: ["Products"],
        summary: "Add a new product",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "descriptions", type: "string"),
                    new OA\Property(property: "qty", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Product created successfully"
            ), // Removed the extra ")" that was here
            new OA\Response(
                response: 400,
                description: "Bad Request",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Product Description already exists!")
                    ]
                )
            )
        ]
    )]    
    public function addProduct(Request $request, KafkaProducerService $kafkaService) {
        $product = Product::where('descriptions', $request->descriptions)->first();
        if ($product) {
            return response()->json(['message' => 'Product Description is already exists!'],404);
        }
        $product->category = $request->category;
        $product->descriptions = $request->descriptions;
        $product->qty = $request->qty;
        $product->unit = $request->unit;
        $product->costprice = $request->costprice;
        $product->sellprice = $request->sellprice;
        $product->saleprice = $request->saleprice;
        $product->productpicture = $request->productpicture;
        $product->alertstocks = $request->alertstocks;
        $product->criticalstocks = $request->criticalstocks;
        $product->save();

        $data = [
            'event' => 'add_product',
            'product' => $product
        ];

        $kafkaService->publishMessage('central-topic', $data, $product);

        return response()->json(['message' => 'New Product Created Successfully.'],200);
    }    
}
