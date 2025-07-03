<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScrappingController extends Controller
{
    public function getProductsToScrapping(Request $request)
    {
        //FROM TABLE products_to_scrapping get 2 products per category_id where status is pending
        try {
            $products = DB::select("
                SELECT 
                    id, 
                    category_id, 
                    name, 
                    status
                FROM (
                    SELECT 
                        id, 
                        category_id, 
                        name, 
                        status,
                        @rn := IF(@prev_cat = category_id, @rn + 1, 1) as row_num,
                        @prev_cat := category_id
                    FROM products_to_scrapping, (SELECT @rn := 0, @prev_cat := NULL) vars
                    WHERE status = 'pending'
                    ORDER BY category_id, id
                ) ranked_products
                WHERE row_num <= 1
            ");

            Log::info('Fetched products to scrapping', ['products' => $products]);

            return response()->json([
                'message' => 'Products fetched successfully.',
                'products' => $products,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching products to scrapping', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Error fetching products: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function markProductsCompleted(Request $request)
    {
        try {

            $productIds = $request->input('product_ids');
            Log::info('Marking products as completed', ['product_ids' => $productIds]);
            if (empty($productIds) || !is_array($productIds)) {
                return response()->json([
                    'message' => 'Invalid product IDs provided.',
                ], 400);
            }
            // Update the status of the products to 'completed'
            DB::table('products_to_scrapping')
                ->whereIn('id', $productIds)
                ->update(['status' => 'completed']);

            return response()->json([
                'message' => 'Products updated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating products', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Error updating products: ' . $e->getMessage(),
            ], 500);
        }
    }
}
