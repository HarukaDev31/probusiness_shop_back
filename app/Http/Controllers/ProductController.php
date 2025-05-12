<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perCategory = $request->input('per_category', 50);
        $categorySlug = $request->input('category', null);
        $search = $request->input('search', null);
        $page = $request->input('current_page', 1);
        $allCategories = $request->input('all_categories', false);

        if ($allCategories) {
            // Obtener todas las categorías
            $categories = DB::table('catalogo_producto_category')->get();

            $productsByCategory = collect();

            foreach ($categories as $category) {
                $products = DB::table('catalogo_producto as p')
                    ->join('catalogo_producto_category as c', 'p.category_id', '=', 'c.id')
                    ->select('p.*', 'c.name as category_name', 'c.id as category_id')
                    ->where('p.status', 'EN TIENDA')
                    ->where('p.category_id', $category->id)
                    ->where(function ($query) use ($search) {
                        if ($search) {
                            $query->where('p.nombre', 'LIKE', '%' . $search . '%')
                                ->orWhere('c.name', 'LIKE', '%' . $search . '%');
                        }
                    })
                    ->orderBy('p.id')
                    ->limit(5) // 5 productos por categoría
                    ->get();

                $productsByCategory = $productsByCategory->merge($products);
            }

            $total = $productsByCategory->count();

            return response()->json([
                'status' => 'success',
                'data' => $productsByCategory,
                'meta' => [
                    'total' => $total,
                    'per_page' => $total,
                    'current_page' => 1,
                    'from' => 1,
                    'to' => $total,
                    'last_page' => 1,
                ],
            ], 200);
        } else {
            // Lógica original cuando all_categories es false
            $products = DB::table('catalogo_producto as p')
                ->join(DB::raw('(
                SELECT 
                    p1.id,
                    p1.category_id,
                    @rn := IF(@prev = p1.category_id, @rn + 1, 1) AS row_num,
                    @prev := p1.category_id
                FROM 
                    catalogo_producto p1,
                    (SELECT @rn := 0, @prev := 0) AS vars
                WHERE 
                    p1.status = "EN TIENDA"
                ORDER BY 
                    p1.category_id, p1.id
            ) AS ranked'), 'p.id', '=', 'ranked.id')
                ->join('catalogo_producto_category as c', 'p.category_id', '=', 'c.id')
                ->select('p.*', 'c.name as category_name', 'c.id as category_id')
                ->where('p.status', 'EN TIENDA')
                ->where(function ($query) use ($categorySlug) {
                    if ($categorySlug) {
                        $query->where('c.slug', $categorySlug);
                    }
                })
                ->where(function ($query) use ($search) {
                    if ($search) {
                        $query->where('p.nombre', 'LIKE', '%' . $search . '%')
                            ->orWhere('c.name', 'LIKE', '%' . $search . '%');
                    }
                });

            $total = $products->count();
            $perCategory = $perCategory == "all" ? $total : $perCategory;

            if ($perCategory != "all") {
                $products = $products->offset(($page - 1) * $perCategory)
                    ->limit($perCategory);
            }

            $products = $products->orderBy('p.category_id')
                ->orderBy('p.id')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $products,
                'meta' => [
                    'total' => $total,
                    'per_page' => $perCategory,
                    'current_page' => $page,
                    'from' => ($page - 1) * $perCategory + 1,
                    'to' => min($page * $perCategory, $total),
                    'last_page' => ceil($total / $perCategory),
                ],
            ], 200);
        }
    }
    public function show($id)
    {
        $product = DB::table('catalogo_producto as p')
            ->join('catalogo_producto_category as c', 'p.category_id', '=', 'c.id')
            ->select(
                'p.*',
                'c.name as category_name',
                'c.slug as category_slug',
                'c.id as category_id'
            )
            ->where('p.id', $id)
            ->first();

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $product,
        ], 200);
    }
}
