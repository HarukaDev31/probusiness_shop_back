<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Descarga y guarda un video de Alibaba
     */
    private function downloadAndSaveVideo($videoUrl, $productId)
    {
        try {
            // Verificar si es una URL de Alibaba
            if (strpos($videoUrl, 'gv.videocdn.alibaba.com') !== false || 
                strpos($videoUrl, 'api.allorigins.win') !== false) {
                
                Log::info('Downloading video from Alibaba: ' . $videoUrl);
                
                // Crear directorio para videos si no existe
                $videoPath = 'public/videos/products/' . $productId;
                if (!Storage::exists($videoPath)) {
                    Storage::makeDirectory($videoPath);
                }
                
                // Generar nombre único para el archivo
                $fileName = 'video_' . time() . '_' . uniqid() . '.mp4';
                $fullPath = $videoPath . '/' . $fileName;
                
                // Configuración específica para videos de Alibaba
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $videoUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 600); // 10 minutos de timeout para videos grandes
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Accept: video/mp4,video/*;q=0.9,*/*;q=0.8',
                    'Accept-Language: en-US,en;q=0.5',
                    'Accept-Encoding: gzip, deflate',
                    'Connection: keep-alive',
                    'Upgrade-Insecure-Requests: 1',
                    'Cache-Control: no-cache',
                    'Pragma: no-cache'
                ]);
                curl_setopt($ch, CURLOPT_ENCODING, ''); // Aceptar cualquier encoding
                curl_setopt($ch, CURLOPT_MAXREDIRS, 10); // Máximo 10 redirecciones
                
                // Para URLs de Alibaba específicamente, agregar headers adicionales
                if (strpos($videoUrl, 'gv.videocdn.alibaba.com') !== false) {
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Accept: video/mp4,video/*;q=0.9,*/*;q=0.8',
                        'Accept-Language: en-US,en;q=0.5',
                        'Accept-Encoding: gzip, deflate',
                        'Connection: keep-alive',
                        'Upgrade-Insecure-Requests: 1',
                        'Cache-Control: no-cache',
                        'Pragma: no-cache',
                        'Referer: https://www.alibaba.com/',
                        'Origin: https://www.alibaba.com',
                        'Sec-Fetch-Dest: video',
                        'Sec-Fetch-Mode: cors',
                        'Sec-Fetch-Site: same-site'
                    ]);
                }
                
                $videoContent = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
                $contentLength = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
                
                if (curl_errno($ch)) {
                    Log::error('cURL Error: ' . curl_error($ch));
                    curl_close($ch);
                    return $videoUrl; // Retornar URL original si falla
                }
                
                curl_close($ch);
                
                Log::info('Download response - HTTP Code: ' . $httpCode . ', Content-Type: ' . $contentType . ', Content-Length: ' . $contentLength);
                
                if ($httpCode === 200 && !empty($videoContent)) {
                    // Verificar que el contenido sea realmente un video
                    if (strpos($contentType, 'video/') === 0 || 
                        strpos($contentType, 'application/octet-stream') === 0 ||
                        strpos($contentType, 'binary/octet-stream') === 0 ||
                        $contentLength > 1000) { // Mínimo 1KB para ser considerado un video válido
                        
                        // Guardar el video
                        Storage::put($fullPath, $videoContent);
                        
                        // Verificar que el archivo se guardó correctamente
                        if (Storage::exists($fullPath)) {
                            $fileSize = Storage::size($fullPath);
                            Log::info('Video saved successfully. File size: ' . $fileSize . ' bytes');
                            
                            // Retornar APP_URL + ruta relativa del video guardado
                            $relativePath = 'storage/' . str_replace('public/', '', $fullPath);
                            $fullUrl = config('app.url') . '/' . $relativePath;
                            Log::info('Video downloaded and saved successfully: ' . $fullUrl);
                            
                            return $fullUrl;
                        } else {
                            Log::error('Failed to save video file to storage');
                            return $videoUrl;
                        }
                    } else {
                        Log::error('Downloaded content is not a valid video. Content-Type: ' . $contentType);
                        return $videoUrl;
                    }
                } else {
                    Log::error('Failed to download video. HTTP Code: ' . $httpCode . ', Content Length: ' . strlen($videoContent));
                    return $videoUrl; // Retornar URL original si falla
                }
            }
            
            return $videoUrl; // Retornar URL original si no es de Alibaba
        } catch (\Exception $e) {
            Log::error('Error downloading video: ' . $e->getMessage());
            return $videoUrl; // Retornar URL original en caso de error
        }
    }


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

        // Obtener las imágenes del producto desde catalogo_producto_media
        $media = DB::table('catalogo_producto_media')
            ->where('id_catalogo_producto', $id)
            ->select('id', 'url', 'type', 'created_at')
            ->orderBy('created_at', 'asc')
            ->get();

        // Agregar las imágenes al producto
        $product->media = $media;

        return response()->json([
            'status' => 'success',
            'data' => $product,
        ], 200);
    }
    public function store(Request $request)
    {
        try {
            Log::info('Request: ' . json_encode($request->all()));
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

                    // Convertir a float con validación
                    if (!empty($cleanPrice) && is_numeric($cleanPrice)) {
                        $numericPrice = (float)$cleanPrice;
                        // Multiplicar por TC
                        $price = $numericPrice * 3.7;
                    }
                }

                // Procesar el quantity del primer elemento para el moq
                if (isset($firstPrice['quantity'])) {
                    $quantity = $firstPrice['quantity'];

                    // Extraer el número del quantity
                    if (preg_match('/^(\d+)/', $quantity, $matches)) {
                        // Si empieza con número, usar ese número
                        $moq = (int)$matches[1];
                    } elseif (preg_match('/>=?\s*(\d+)/', $quantity, $matches)) {
                        // Si es formato ">= n", usar n
                        $moq = (int)$matches[1];
                    }

                }
            }
            //from $images get first 3 images and 1 video if exists and set them to the respective fields
            $images = $request->input('images', []);
            $mainImage = isset($images[0]) ? $images[0] : null;
            $aditionalImage1 = isset($images[1]) ? $images[1] : null;
            $aditionalImage2 = isset($images[2]) ? $images[2] : null;
            $aditionalVideo1 = null;
            
            // Procesar imágenes y videos
            $processedImages = [];
            $processedVideos = [];
            
            foreach ($images as $image) {
                if (str_ends_with($image, '.mp4') || str_ends_with($image, '.mov') || str_ends_with($image, '.avi') || str_ends_with($image, '.wmv') || str_ends_with($image, '.flv') || str_ends_with($image, '.webm') ||
                    strpos($image, 'gv.videocdn.alibaba.com') !== false || 
                    strpos($image, 'api.allorigins.win') !== false) {
                    $processedVideos[] = $image;
                } else {
                    $processedImages[] = $image;
                }
            }
            
            // Asignar las primeras 3 imágenes
            $mainImage = isset($processedImages[0]) ? $processedImages[0] : null;
            $aditionalImage1 = isset($processedImages[1]) ? $processedImages[1] : null;
            $aditionalImage2 = isset($processedImages[2]) ? $processedImages[2] : null;
            
            // Asignar el primer video
            $aditionalVideo1 = isset($processedVideos[0]) ? $processedVideos[0] : null;
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
                $existingSupplier = DB::table('catalogo_producto_suppliers')
                    ->where('supplier_name', $supplierName)
                    ->first();

                if ($existingSupplier) {
                    $supplierId = $existingSupplier->id;
                } else {
                    // Crear nuevo supplier
                    $supplierId = DB::table('catalogo_producto_suppliers')->insertGetId([
                        'supplier_name' => $supplierName,

                    ]);
                }
            }

            // Procesar todos los precios del array para prices_range
            foreach ($prices as &$row) {

                $cleanPrice = $row['price'];
                $cleanPrice = str_replace(['USD', ',     ', ' '], '', $cleanPrice);
                $cleanPrice = preg_replace('/[^\d.,]/', '', $cleanPrice); // Solo números, puntos y comas
                $cleanPrice = str_replace(',', '.', $cleanPrice); // Normalizar decimales
                $cleanPrice = trim($cleanPrice);


                // Convertir a float con validación
                if (empty($cleanPrice) || !is_numeric($cleanPrice)) {
                    Log::error('Invalid numeric value: "' . $cleanPrice . '"');
                    $numericPrice = 0;
                } else {
                    $numericPrice = (float)$cleanPrice;
                }


                // Multiplicar por TC
                $finalPrice = $numericPrice * 3.7;

                // Formatear con prefijo
                $row['price'] = number_format($finalPrice, 2);
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

            // Procesar y descargar video de Alibaba si existe
            if ($aditionalVideo1) {
                $processedVideoUrl = $this->downloadAndSaveVideo($aditionalVideo1, $productId);
                if ($processedVideoUrl !== $aditionalVideo1) {
                    // Actualizar el producto con la nueva URL del video
                    DB::table('catalogo_producto')
                        ->where('id', $productId)
                        ->update(['aditional_video1_url' => $processedVideoUrl]);
                }
            }

            // Insertar todas las imágenes en la tabla catalogo_producto_media
            if (!empty($images)) {
                $mediaData = [];
                foreach ($images as $imageUrl) {
                    $type = 'image';
                    $finalUrl = $imageUrl;
                    
                    // Si es un video, procesarlo agrega validacion para que maneje este caso
                    if (str_ends_with($imageUrl, '.mp4') || str_ends_with($imageUrl, '.mov') || str_ends_with($imageUrl, '.avi') || str_ends_with($imageUrl, '.wmv') || str_ends_with($imageUrl, '.flv') || str_ends_with($imageUrl, '.webm') || 
                        strpos($imageUrl, 'gv.videocdn.alibaba.com') !== false || 
                        strpos($imageUrl, 'api.allorigins.win') !== false) {
                        $type = 'video';
                        $finalUrl = $this->downloadAndSaveVideo($imageUrl, $productId);
                    }
                    
                    $mediaData[] = [
                        'id_catalogo_producto' => $productId,
                        'url' => $finalUrl,
                        'type' => $type,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                if (!empty($mediaData)) {
                    DB::table('catalogo_producto_media')->insert($mediaData);
                }
            }

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
