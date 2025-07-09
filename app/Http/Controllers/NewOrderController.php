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
            
            // Crear la orden principal
            $orderId = DB::table('orders')->insertGetId([
                'order_number' => $orderNumber,
                'uuid' => Str::uuid()->toString(),
                
                // Información del cliente
                'customer_full_name' => $request->input('customer.fullName'),
                'customer_dni' => $request->input('customer.dni'),
                'customer_email' => $request->input('customer.email'),
                'customer_phone' => $request->input('customer.phone'),
                
                // Dirección del cliente
                'customer_province' => $request->input('customer.address.province'),
                'customer_city' => $request->input('customer.address.city'),
                'customer_district' => $request->input('customer.address.district'),
                
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
                $errors[] = "No hay precio disponible para la cantidad {$item['quantity']} del producto {$product->name}";
                continue;
            }

            // Calcular el total correcto
            $calculatedItemTotal = $correctPrice * $item['quantity'];

            // Verificar si el precio enviado coincide con el calculado
            if (abs($item['price'] - $correctPrice) > 0.01) {
                $errors[] = "El precio del producto '{$product->name}' no coincide. Enviado: {$item['price']}, Correcto: {$correctPrice}";
            }

            // Verificar si el total del item coincide
            if (abs($item['total'] - $calculatedItemTotal) > 0.01) {
                $errors[] = "El total del item '{$product->name}' no coincide. Enviado: {$item['total']}, Correcto: {$calculatedItemTotal}";
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
     * Generar número de orden en formato YYMES00001
     */
    private function generateOrderNumber()
    {
        $year = date('y'); // 24
        $month = date('m'); // 07
        $day = date('d'); // 09
        
        // Obtener el siguiente número de orden para hoy
        $today = date('Y-m-d');
        $lastOrder = DB::table('orders')
            ->whereDate('created_at', $today)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastOrder) {
            // Extraer el número de secuencia del último orden del día
            $lastOrderNumber = $lastOrder->order_number;
            if (preg_match('/\d{5}$/', $lastOrderNumber, $matches)) {
                $sequence = (int)$matches[0] + 1;
            }
        }

        return $year . $month . $day . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }
} 