<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perCategory = $request->input('per_category', 0);
        $categorySlug = $request->input('category', null);
        $search = $request->input('search', null);
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
        ) AS ranked'), function ($join) use ($perCategory) {
                if ($perCategory == "all") {
                    $join->on('p.id', '=', 'ranked.id');
                } else {
                    $join->on('p.id', '=', 'ranked.id')
                        ->where('ranked.row_num', '<=', $perCategory);
                }
            })->join('catalogo_producto_category as c', 'p.category_id', '=', 'c.id')->select(
                'p.*',
                'c.name as category_name',
                'c.id as category_id'
            )->where('p.status', 'EN TIENDA')
            ->where(function ($query) use ($categorySlug) {
                if ($categorySlug) {
                    $query->where('c.slug', $categorySlug);
                }
            })->where(function ($query) use ($search) {
                if ($search) {
                    $query->where('p.nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('c.name', 'LIKE', '%' . $search . '%');
                }})->get();

        return response()->json([
            'status' => 'success',
            'data' => $products,
        ], 200);
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
