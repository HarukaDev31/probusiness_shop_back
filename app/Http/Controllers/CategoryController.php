<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        // Fetch all categories from the database
        $categories = DB::table('catalogo_producto_category')->get();

        // Return the view with the categories
        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ],200);
    }
}
