<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddproductController extends Controller
{
    public function addProduct(Request $request) {
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
        return response()->json(['message' => 'New Product Created Successfully.'],200);
    }    
}
