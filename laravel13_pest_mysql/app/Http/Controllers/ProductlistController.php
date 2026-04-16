<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class ProductlistController extends Controller
{
    public function listProducts(Request $request, int $page) 
    {
        $perPage = 5;
        $skip = ($page - 1) * $perPage;
        try {
            $products = Product::skip($skip)->take($perPage)->get();
            $totalrecords = Product::count(); 
            $totpage = ceil($totalrecords / $perPage);


            if ($products->count() == 0) {
                return response()->json(['message' => 'Products is empty.'],404);
            }
            return response()->json(['message' => 'Product Retrieved Successfully.', 'totalrecords' => $totalrecords, 'page' => $page,'totpage'=> $totpage, 'products' => $products],200);
        } catch(\Exceptions $e) {
            return response()->json(['message' => $e->getMessage()],500);
        }
    }
    
}
