<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Requests\NewOrderRequest;
use Carbon\Carbon;

class NewOrderController extends Controller
{
    private $ERROR_ORDER = 'Error creando la orden';
    private $SUCCESS_ORDER = 'Orden creada exitosamente';

    /**
     * Crear una nueva orden
     */
    public function store(NewOrderRequest $request)
    {
        DB::beginTransaction();
        try {
            // Obtener el token del header
            $token = $request->header('Authorization');
            if (!$token) {
                return response()->json([
                    'error' => 'Token de autorización requerido'
                ], 401);
            }

            // Remover 'Bearer ' del token si está presente
            $token = str_replace('Bearer ', '', $token);

            // Obtener el usuario por el token
            $user = DB::table('users')
                ->where('api_token', $token)
                ->first();
            
            if (!$user) {
                return response()->json([
                    'error' => 'Token inválido'
                ], 401);
            }

            // Validar precios y calcular totales
            $validatedItems = $this->validateAndCalculateItems($request->input('order.items'));
            
            if (!$validatedItems['valid']) {
                return response()->json([
                    'error' => 'Error en validación de precios',
                    'details' => $validatedItems['errors']
                ], 422);
            }

            // Generar número de orden en formato YYMES00001
            $orderNumber = $this->generateOrderNumber();
            
            // Obtener nombres de ubicación antes de crear la orden
            $departamento = DB::table('departamento')
                ->where('Id_Departamento', $request->input('customer.address.departamento_id'))
                ->value('No_Departamento');
            
            $provincia = DB::table('provincia')
                ->where('Id_Provincia', $request->input('customer.address.provincia_id'))
                ->value('No_Provincia');
            
            $distrito = DB::table('distrito')
                ->where('Id_Distrito', $request->input('customer.address.distrito_id'))
                ->value('No_Distrito');

            // Crear la orden principal
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $user->id,
                'order_number' => $orderNumber,
                'uuid' => Str::uuid()->toString(),
                
                // Información del cliente
                'customer_full_name' => $request->input('customer.fullName'),
                'customer_dni' => $request->input('customer.dni'),
                'customer_email' => $request->input('customer.email'),
                'customer_phone' => $request->input('customer.phone'),
                
                // Dirección del cliente (IDs)
                'customer_departamento_id' => $request->input('customer.address.departamento_id'),
                'customer_provincia_id' => $request->input('customer.address.provincia_id'),
                'customer_distrito_id' => $request->input('customer.address.distrito_id'),
                
                // Dirección del cliente (nombres)
                'customer_province' => $provincia,
                'customer_city' => $departamento,
                'customer_district' => $distrito,
                
                // Información de la orden
                'total_amount' => $validatedItems['calculated_total'],
                'status' => $request->input('order.status', 'pending'),
                'order_date' => Carbon::parse($request->input('order.orderDate')),
                
                // Metadata
                'source' => $request->input('metadata.source'),
                'user_agent' => $request->input('metadata.userAgent'),
                'timestamp' => $request->input('metadata.timestamp'),
                
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Crear los items de la orden
            foreach ($validatedItems['items'] as $item) {
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $item['productId'],
                    'product_name' => $item['name'],
                    'unit_price' => $item['calculated_price'],
                    'quantity' => $item['quantity'],
                    'total_price' => $item['calculated_total'],
                    'product_image' => $item['image'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Enviar correo de confirmación
            $customer = [
                'fullName' => $request->input('customer.fullName'),
                'dni' => $request->input('customer.dni'),
                'email' => $request->input('customer.email'),
                'phone' => $request->input('customer.phone'),
                'address' => [
                    'province' => $provincia,
                    'city' => $departamento,
                    'district' => $distrito,
                ],
            ];
            $order = [
                'items' => array_map(function($item) {
                    return [
                        'productId' => $item['productId'],
                        'name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['calculated_price'],
                        'total' => $item['calculated_total'],
                        'image' => $item['image'] ?? null,
                    ];
                }, $validatedItems['items']),
                'total' => $validatedItems['calculated_total'],
            ];
            \Mail::to($customer['email'])->send(
                new \App\Mail\OrderConfirmationMail(
                    $customer,
                    $order,
                    $orderNumber,
                    public_path('storage/logo_header.png'),
                    public_path('storage/logo_footer.png')
                )
            );

            DB::commit();

            Log::info('Orden creada exitosamente', [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'customer_email' => $request->input('customer.email'),
                'total_amount' => $validatedItems['calculated_total']
            ]);

            return response()->json([
                'message' => $this->SUCCESS_ORDER,
                'order_id' => $orderNumber,
                'order_uuid' => DB::table('orders')->where('id', $orderId)->value('uuid'),
                'total_amount' => $validatedItems['calculated_total']
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->ERROR_ORDER, [
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => $this->ERROR_ORDER,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Validar precios y calcular totales
     */
    private function validateAndCalculateItems($items)
    {
        $validatedItems = [];
        $calculatedTotal = 0;
        $errors = [];

        foreach ($items as $index => $item) {
            // Obtener el producto de la base de datos
            $product = DB::table('catalogo_producto')
                ->where('id', $item['productId'])
                ->first();

            if (!$product) {
                $errors[] = "Producto con ID {$item['productId']} no encontrado";
                continue;
            }

            // Obtener el precio correcto según la cantidad
            $correctPrice = $this->getPriceForQuantity($product, $item['quantity']);
            
            if ($correctPrice === null) {
                $errors[] = "No hay precio disponible para la cantidad {$item['quantity']} del producto {$product->nombre}";
                continue;
            }

            // Calcular el total correcto
            $calculatedItemTotal = $correctPrice * $item['quantity'];

            // Verificar si el precio enviado coincide con el calculado
            if (abs($item['price'] - $correctPrice) > 0.01) {
                $errors[] = "El precio del producto '{$product->nombre}' no coincide. Enviado: {$item['price']}, Correcto: {$correctPrice}";
            }

            // Verificar si el total del item coincide
            if (abs($item['total'] - $calculatedItemTotal) > 0.01) {
                $errors[] = "El total del item '{$product->nombre}' no coincide. Enviado: {$item['total']}, Correcto: {$calculatedItemTotal}";
            }

            $validatedItems[] = [
                'productId' => $item['productId'],
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'calculated_price' => $correctPrice,
                'calculated_total' => $calculatedItemTotal,
                'image' => $item['image'] ?? null,
            ];

            $calculatedTotal += $calculatedItemTotal;
        }

        return [
            'valid' => empty($errors),
            'items' => $validatedItems,
            'calculated_total' => $calculatedTotal,
            'errors' => $errors
        ];
    }

    /**
     * Obtener el precio correcto según la cantidad
     */
    private function getPriceForQuantity($product, $quantity)
    {
        // Decodificar el campo prices (asumiendo que es JSON)
        $prices = json_decode($product->prices ?? '{}', true);
        
        if (!$prices || !is_array($prices)) {
            // Si no hay precios por cantidad, usar el precio base
            return $product->precio ?? null;
        }

        // Ordenar por cantidad de menor a mayor
        ksort($prices, SORT_NUMERIC);

        $selectedPrice = null;
        
        foreach ($prices as $minQuantity => $price) {
            if ($quantity >= (int)$minQuantity) {
                $selectedPrice = $price;
            } else {
                break;
            }
        }

        // Si no se encontró precio por cantidad, usar el precio base
        return $selectedPrice ?? $product->precio ?? null;
    }

    /**
     * Generar número de orden en formato YYMMM00001
     * Donde MMM son las primeras 3 letras del mes en español
     */
    private function generateOrderNumber()
    {
        $year = date('y'); // 24
        $month = date('n'); // 1-12 (sin ceros a la izquierda)
        
        // Array con los nombres de los meses en español
        $meses = [
            1 => 'ENE', 2 => 'FEB', 3 => 'MAR', 4 => 'ABR', 5 => 'MAY', 6 => 'JUN',
            7 => 'JUL', 8 => 'AGO', 9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DIC'
        ];
        
        $mesAbrev = $meses[$month]; // Obtener las 3 letras del mes
        
        // Obtener el siguiente número de orden para este mes
        $currentMonth = date('Y-m');
        $lastOrder = DB::table('orders')
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastOrder) {
            // Extraer el número de secuencia del último orden del mes
            $lastOrderNumber = $lastOrder->order_number;
            if (preg_match('/\d{5}$/', $lastOrderNumber, $matches)) {
                $sequence = (int)$matches[0] + 1;
            }
        }

        return $year . $mesAbrev . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Obtener las órdenes del usuario
     */
    public function myOrders(Request $request)
    {
        try {
            // Obtener el token del header
            $token = $request->header('Authorization');
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token de autorización requerido'
                ], 401);
            }

            // Remover 'Bearer ' del token si está presente
            $token = str_replace('Bearer ', '', $token);

            // Obtener el usuario por el token
            $user = DB::table('users')
                ->where('api_token', $token)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token inválido'
                ], 401);
            }

            // Obtener el filtro de fecha del query parameter
            $dateFilter = $request->query('filter');

            // Construir la consulta base
            $query = DB::table('orders')->where('user_id', $user->id);

            // Aplicar filtros de fecha según el parámetro
            if ($dateFilter) {
                switch ($dateFilter) {
                    case 'last_30_days':
                        $query->where('order_date', '>=', Carbon::now()->subDays(30));
                        break;
                    
                    case 'last_3_months':
                        $query->where('order_date', '>=', Carbon::now()->subMonths(3));
                        break;
                    
                    case '2025':
                        $query->whereYear('order_date', 2025);
                        break;
                    
                    case '2024':
                        $query->whereYear('order_date', 2024);
                        break;
                    
                    case '2023':
                        $query->whereYear('order_date', 2023);
                        break;
                    
                    case '2022':
                        $query->whereYear('order_date', 2022);
                        break;
                    
                    case '2021':
                        $query->whereYear('order_date', 2021);
                        break;
                    
                    default:
                        // Si no es un filtro válido, no aplicar filtro
                        break;
                }
            }

            // Obtener las órdenes del usuario con filtros aplicados
            $orders = $query->orderBy('created_at', 'desc')->get();

            $formattedOrders = [];

            foreach ($orders as $order) {
                // Obtener los items de la orden
                $items = DB::table('order_items')
                    ->where('order_id', $order->id)
                    ->get();

                $formattedItems = [];
                foreach ($items as $item) {
                    $formattedItems[] = [
                        'productId' => $item->product_id,
                        'name' => $item->product_name,
                        'price' => $item->unit_price,
                        'quantity' => $item->quantity,
                        'total' => $item->total_price,
                        'image' => $item->product_image
                    ];
                }

                // Calcular fecha estimada de entrega (60 días después de la orden)
                $estimatedDelivery = Carbon::parse($order->order_date)->addDays(60);

                $formattedOrders[] = [
                    'id' => $order->id,
                    'orderNumber' => $order->order_number,
                    'status' => $order->status,
                    'total' => $order->total_amount,
                    'orderDate' => Carbon::parse($order->order_date)->toISOString(),
                    'estimatedDelivery' => $estimatedDelivery->toISOString(),
                    'items' => $formattedItems
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $formattedOrders,
                'filter_applied' => $dateFilter ?? 'all',
                'total_orders' => count($formattedOrders)
            ], 200);

        } catch (Exception $e) {
            Log::error('Error obteniendo órdenes del usuario', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Obtener detalles de una orden específica
     */
    public function getOrderDetails(Request $request, $orderId)
    {
        try {
            // Obtener el token del header
            $token = $request->header('Authorization');
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token de autorización requerido'
                ], 401);
            }

            // Remover 'Bearer ' del token si está presente
            $token = str_replace('Bearer ', '', $token);

            // Obtener el usuario por el token
            $user = DB::table('users')
                ->where('api_token', $token)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token inválido'
                ], 401);
            }

            // Obtener la orden y verificar que pertenece al usuario
            $order = DB::table('orders')
                ->where('id', $orderId)
                ->where('user_id', $user->id)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orden no encontrada o no autorizada'
                ], 404);
            }

            // Obtener los items de la orden
            $items = DB::table('order_items')
                ->where('order_id', $order->id)
                ->get();

            $formattedItems = [];
            foreach ($items as $item) {
                $formattedItems[] = [
                    'productId' => $item->product_id,
                    'name' => $item->product_name,
                    'price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'total' => $item->total_price,
                    'image' => $item->product_image
                ];
            }

            // Calcular fecha estimada de entrega (60 días después de la orden)
            $estimatedDelivery = Carbon::parse($order->order_date)->addDays(60);

            // Formatear detalles completos de la orden
            $orderDetails = [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'uuid' => $order->uuid,
                
                // Información del cliente
                'customer' => [
                    'full_name' => $order->customer_full_name,
                    'dni' => $order->customer_dni,
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone,
                    'address' => [
                        'province' => $order->customer_province,
                        'city' => $order->customer_city,
                        'district' => $order->customer_district
                    ]
                ],
                
                // Información de la orden
                'order' => [
                    'status' => $order->status,
                    'order_date' => Carbon::parse($order->order_date)->toISOString(),
                    'estimated_delivery' => $estimatedDelivery->toISOString(),
                    'items' => $formattedItems,
                    'subtotal' => $order->total_amount,
                    'total_items' => count($formattedItems)
                ],
                
                // Información de pago (si existe)
                'payment' => [
                    'method' => $order->payment_method,
                    'status' => $order->payment_status,
                    'amount' => $order->payment_amount ?? $order->total_amount,
                    'payment_date' => $order->payment_date ? Carbon::parse($order->payment_date)->toISOString() : null,
                    'transaction_id' => $order->transaction_id,
                    'notes' => $order->payment_notes
                ],
                
                // Metadata
                'metadata' => [
                    'source' => $order->source,
                    'user_agent' => $order->user_agent,
                    'timestamp' => $order->timestamp
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $orderDetails
            ], 200);

        } catch (Exception $e) {
            Log::error('Error obteniendo detalles de la orden', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }
} 