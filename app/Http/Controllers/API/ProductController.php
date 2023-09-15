<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function list(Request $request, Category $category = null) //api
    {
        try {
            $products = Product::get()->toArray();
            if ($category != null)
                $products = Product::where('category_id', $category->id)->get()->toArray();
            return response()->json([
                'status' => 'success',
                'message' => 'Product list.',
                "data" => $products
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function price(Request $request, Product $product, Branch $branch) //api
    {
        try {
            return response()->json([
                'status' => 'success',
                'message' => 'Product price list.',
                "data" => $product->price()->whereIn('store_id', $branch->stores()->get()->pluck('id')->toArray())->get()->toArray()
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 500);
        }
    }
}