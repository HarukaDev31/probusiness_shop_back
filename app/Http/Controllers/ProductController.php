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
    public function store(Request $request)
    {
        //id|nombre|precio|qty_box|cbm_box|dias_entrega|delivery|colores|notas|whechat_phone|contact_card_url|main_image_url|aditional_image1_url|aditional_image2_url|aditional_video1_url|moq|servicio_impo|arancel|igv|antidumping|percepcion|status|precio_peru|precio_usd|cod_producto|category_id|prices_range|
        $price = $request->input('price');
        Log::info('Original price: ' . json_encode($price));
        Log::info('Original price length: ' . strlen($price));
        Log::info('Original price hex: ' . bin2hex($price));

        // Paso 1: Limpiar más agresivamente
        $cleanPrice = $price;
        $cleanPrice = str_replace(['USD', '$', ' '], '', $cleanPrice); // Remover USD, $, y espacios
        $cleanPrice = preg_replace('/[^\d.,]/', '', $cleanPrice); // Solo números, puntos y comas
        $cleanPrice = str_replace(',', '.', $cleanPrice); // Normalizar decimales
        $cleanPrice = trim($cleanPrice);

        Log::info('After cleaning: "' . $cleanPrice . '"');
        Log::info('Cleaned price length: ' . strlen($cleanPrice));

        // Paso 2: Convertir a float con validación
        if (empty($cleanPrice) || !is_numeric($cleanPrice)) {
            Log::error('Invalid numeric value: "' . $cleanPrice . '"');
            $price = 0;
        } else {
            $numericPrice = (float)$cleanPrice;
            Log::info('Converted to float: ' . $numericPrice);

            // Paso 3: Multiplicar por TC
            $price = $numericPrice * 3.7;
            Log::info('After multiplication by 3.7: ' . $price);
        }
        Log::info('Processed price: ' . $price);
        //from $images get first 3 images and 1 video if exists and set them to the respective fields
        $images = $request->input('images', []);
        $mainImage = isset($images[0]) ? $images[0] : null;
        $aditionalImage1 = isset($images[1]) ? $images[1] : null;
        $aditionalImage2 = isset($images[2]) ? $images[2] : null;
        $aditionalVideo1 = isset($images[3]) ? $images[3] : null;
        //FROM PRODUCTS GET CODE YEAR-MONTH-(4 DIGITS WITH COUNT IN TIENDA PRODUCTS)
        // Assuming the product code is generated based on the current date and a count of products in "EN TIENDA" status
        $year = date('Y');
        $month = date('m');
        $count = DB::table('catalogo_producto')
            ->where('status', 'EN TIENDA')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count() + 1; // +1 to get the next count
        $cod_producto = sprintf('%s-%s-%04d', $year, $month, $count);

        $prices = $request->input('prices', []);
        foreach ($prices as &$row) {
            Log::info('Original price: ' . json_encode($row['price']));
            Log::info('Original price length: ' . strlen($row['price']));
            Log::info('Original price hex: ' . bin2hex($row['price']));

            // Paso 1: Limpiar más agresivamente
            $cleanPrice = $row['price'];
            $cleanPrice = str_replace(['USD', ',     ', ' '], '', $cleanPrice);
            $cleanPrice = preg_replace('/[^\d.,]/', '', $cleanPrice); // Solo números, puntos y comas
            $cleanPrice = str_replace(',', '.', $cleanPrice); // Normalizar decimales
            $cleanPrice = trim($cleanPrice);

            Log::info('After cleaning: "' . $cleanPrice . '"');
            Log::info('Cleaned price length: ' . strlen($cleanPrice));

            // Paso 2: Convertir a float con validación
            if (empty($cleanPrice) || !is_numeric($cleanPrice)) {
                Log::error('Invalid numeric value: "' . $cleanPrice . '"');
                $numericPrice = 0;
            } else {
                $numericPrice = (float)$cleanPrice;
            }

            Log::info('Converted to float: ' . $numericPrice);

            // Paso 3: Multiplicar por TC
            $finalPrice = $numericPrice * 3.7;
            Log::info('After multiplication: ' . $finalPrice);

            // Paso 4: Formatear con prefijo
            $row['price'] = number_format($finalPrice, 2);
            Log::info('Final formatted price: ' . $row['price']);
        }


        $data = [
            'nombre' => $request->input('description'),
            'precio' => $price,
            // 'qty_box' => $request->input('qty_box'),
            // 'cbm_box' => $request->input('cbm_box'),
            // 'dias_entrega' => $request->input('dias_entrega'),
            // 'delivery' => $request->input('delivery'),
            // 'colores' => $request->input('colores'),
            // 'notas' => $request->input('notas'),
            // 'whechat_phone' => $request->input('whechat_phone'),
            'contact_card_url' => $request->input('contact_card_url'),
            'main_image_url' => $mainImage,
            'aditional_image1_url' => $aditionalImage1,
            'aditional_image2_url' => $aditionalImage2,
            'aditional_video1_url' => $aditionalVideo1,
            'moq' => 1,
            // 'servicio_impo' => $request->input('servicio_impo'),
            // 'arancel' => $request->input('arancel'),
            // 'igv' => $request->input('igv'),
            // 'antidumping' => $request->input('antidumping'),
            // 'percepcion' => $request->input('percepcion'),
            'status' => "EN TIENDA",
            // 'precio_peru' => $request->input('precio_peru'),
            // 'precio_usd' => $request->input('precio_usd'),
            'cod_producto' => $cod_producto,
            'category_id' => 1,
            'prices_range' => json_encode($prices),
            'attributes' => json_encode($request->input('attributes', [])),
            'product_details' => json_encode($request->input('iframe_content', [])['reconstructed_html']),
        ];
        $productId = DB::table('catalogo_producto')->insertGetId($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully',
            'data' => ['id' => $productId],
        ], 201);
    }
}
