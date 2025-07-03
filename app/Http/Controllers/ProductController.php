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
        $supplierId = $request->input('supplier', null);
        if ($allCategories) {
            // Obtener todas las categorías
            $categories = DB::table('catalogo_producto_category')->get();

            $productsByCategory = collect();

            foreach ($categories as $category) {
                $products = DB::table('catalogo_producto as p')
                    ->join('catalogo_producto_category as c', 'p.category_id', '=', 'c.id')
                    ->select('p.*', 'c.name as category_name', 'c.id as category_id')
                    ->where('p.status', 'EN TIENDA')
                    ->where(function ($query) use ($supplierId) {
                        if ($supplierId) {
                            $query->where('p.supplier_id', $supplierId);
                        }
                    })
                    ->where('p.category_id', $category->id)
                    ->where(function ($query) use ($search) {
                        if ($search) {
                            $query->where('p.nombre', 'LIKE', '%' . $search . '%')
                                ->orWhere('c.name', 'LIKE', '%' . $search . '%');
                        }
                    })

                    ->orderBy('p.id')
                    ->limit($perCategory) // 5 productos por categoría
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
                    p1.status = "EN TIENDA"' .
                    ($supplierId ? ' AND p1.supplier_id = ' . $supplierId : '') . '
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
                })
                ->where(function ($query) use ($supplierId) {
                    if ($supplierId) {
                        $query->where('p.supplier_id', $supplierId);
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
        try {
            $prices = $request->input('prices', []);

            // Extraer el primer precio y quantity para el precio principal y moq
            $price = 0;
            $moq = 1;

            if (!empty($prices) && isset($prices[0])) {
                $firstPrice = $prices[0];

                // Procesar el precio del primer elemento
                if (isset($firstPrice['price'])) {
                    $cleanPrice = $firstPrice['price'];
                    $cleanPrice = str_replace(['USD', '$', ' '], '', $cleanPrice); // Remover USD, $, y espacios
                    $cleanPrice = preg_replace('/[^\d.,]/', '', $cleanPrice); // Solo números, puntos y comas
                    $cleanPrice = str_replace(',', '.', $cleanPrice); // Normalizar decimales
                    $cleanPrice = trim($cleanPrice);

                    Log::info('First price after cleaning: "' . $cleanPrice . '"');

                    // Convertir a float con validación
                    if (!empty($cleanPrice) && is_numeric($cleanPrice)) {
                        $numericPrice = (float)$cleanPrice;
                        Log::info('First price converted to float: ' . $numericPrice);
                        // Multiplicar por TC
                        $price = $numericPrice * 3.7;
                        Log::info('First price after multiplication by 3.7: ' . $price);
                    }
                }

                // Procesar el quantity del primer elemento para el moq
                if (isset($firstPrice['quantity'])) {
                    $quantity = $firstPrice['quantity'];
                    Log::info('First quantity: ' . $quantity);

                    // Extraer el número del quantity
                    if (preg_match('/^(\d+)/', $quantity, $matches)) {
                        // Si empieza con número, usar ese número
                        $moq = (int)$matches[1];
                    } elseif (preg_match('/>=?\s*(\d+)/', $quantity, $matches)) {
                        // Si es formato ">= n", usar n
                        $moq = (int)$matches[1];
                    }

                    Log::info('Extracted MOQ: ' . $moq);
                }
            }
            //from $images get first 3 images and 1 video if exists and set them to the respective fields
            $images = $request->input('images', []);
            $mainImage = isset($images[0]) ? $images[0] : null;
            $aditionalImage1 = isset($images[1]) ? $images[1] : null;
            $aditionalImage2 = isset($images[2]) ? $images[2] : null;
            $aditionalVideo1 = //find in images if there url ends in video extension and set it to the field
                $aditionalVideo1 = null;
            foreach ($images as $image) {
                if (str_ends_with($image, '.mp4') || str_ends_with($image, '.mov') || str_ends_with($image, '.avi') || str_ends_with($image, '.wmv') || str_ends_with($image, '.flv') || str_ends_with($image, '.webm')) {
                    $aditionalVideo1 = $image;
                    break;
                }
            }
            $year = date('Y');
            $month = date('m');
            $count = DB::table('catalogo_producto')
                ->where('status', 'EN TIENDA')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count() + 1; // +1 to get the next count
            $cod_producto = sprintf('%s-%s-%04d', $year, $month, $count);

            // Manejar supplier
            $supplierId = null;
            $supplierName = $request->input('supplier_name');

            if ($supplierName) {
                $supplierName = trim(strtoupper($supplierName));
                Log::info('Processing supplier: ' . $supplierName);

                // Buscar si el supplier ya existe
                $existingSupplier = DB::table('catalogo_producto_suppliers')
                    ->where('supplier_name', $supplierName)
                    ->first();

                if ($existingSupplier) {
                    $supplierId = $existingSupplier->id;
                    Log::info('Found existing supplier with ID: ' . $supplierId);
                } else {
                    // Crear nuevo supplier
                    $supplierId = DB::table('catalogo_producto_suppliers')->insertGetId([
                        'supplier_name' => $supplierName,

                    ]);
                    Log::info('Created new supplier with ID: ' . $supplierId);
                }
            }

            // Procesar todos los precios del array para prices_range
            foreach ($prices as &$row) {
                Log::info('Processing price: ' . json_encode($row['price']));

                $cleanPrice = $row['price'];
                $cleanPrice = str_replace(['USD', ',     ', ' '], '', $cleanPrice);
                $cleanPrice = preg_replace('/[^\d.,]/', '', $cleanPrice); // Solo números, puntos y comas
                $cleanPrice = str_replace(',', '.', $cleanPrice); // Normalizar decimales
                $cleanPrice = trim($cleanPrice);

                Log::info('After cleaning: "' . $cleanPrice . '"');

                // Convertir a float con validación
                if (empty($cleanPrice) || !is_numeric($cleanPrice)) {
                    Log::error('Invalid numeric value: "' . $cleanPrice . '"');
                    $numericPrice = 0;
                } else {
                    $numericPrice = (float)$cleanPrice;
                }

                Log::info('Converted to float: ' . $numericPrice);

                // Multiplicar por TC
                $finalPrice = $numericPrice * 3.7;
                Log::info('After multiplication: ' . $finalPrice);

                // Formatear con prefijo
                $row['price'] = number_format($finalPrice, 2);
                Log::info('Final formatted price: ' . $row['price']);
            }


            $data = [
                'nombre' => $request->input('description'),
                'precio' => $price,
                'contact_card_url' => $request->input('contact_card_url'),
                'main_image_url' => $mainImage,
                'aditional_image1_url' => $aditionalImage1,
                'aditional_image2_url' => $aditionalImage2,
                'aditional_video1_url' => $aditionalVideo1,
                'moq' => $moq,
                'status' => "EN TIENDA",
                'cod_producto' => $cod_producto,
                'category_id' => $request->input('category_id'),
                'supplier_id' => $supplierId,
                'packaging_info' => json_encode($request->input('packaging_info')),
                'delivery_lead_times' => json_encode($request->input('delivery_lead_times')),
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
        } catch (\Exception $e) {
            Log::error('Error creating product: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
