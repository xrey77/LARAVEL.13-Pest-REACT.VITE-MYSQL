<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class ProductsearchController extends Controller
{
    public function productSearch(string $key) {
        try {
            $products = Product::where('descriptions', 'LIKE', '%' . $key . '%')->get();
            if ($products->count() == 0) {
                return response()->json(['message' => 'Product not found.'],404);
            }
            return response()->json(['message' => 'Searched found..', 'products' => $products],200);
        } catch(\Exceptions $e) {
            return response()->json(['message' => $e->getMessage()],500);
        }

    }    
}
