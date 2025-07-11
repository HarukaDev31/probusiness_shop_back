<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Obtener detalles de pago de una orden específica
     */
    public function getPaymentDetails(Request $request, $orderId)
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

            // Formatear detalles de pago
            $paymentDetails = [
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
                    'estimated_delivery' => Carbon::parse($order->order_date)->addDays(60)->toISOString(),
                    'items' => $formattedItems,
                    'subtotal' => $order->total_amount,
                    'total_items' => count($formattedItems)
                ],
                
                // Información de pago
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
                'data' => $paymentDetails
            ], 200);

        } catch (Exception $e) {
            Log::error('Error obteniendo detalles de pago', [
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

    /**
     * Obtener historial de pagos del usuario
     */
    public function getPaymentHistory(Request $request)
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

            // Obtener el filtro de estado de pago
            $paymentStatus = $request->query('status');
            $paymentMethod = $request->query('method');

            // Construir la consulta base
            $query = DB::table('orders')
                ->where('user_id', $user->id)
                ->whereNotNull('payment_method'); // Solo órdenes con método de pago

            // Aplicar filtros
            if ($paymentStatus) {
                $query->where('payment_status', $paymentStatus);
            }

            if ($paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            }

            // Obtener las órdenes con información de pago
            $orders = $query->orderBy('payment_date', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->get();

            $paymentHistory = [];

            foreach ($orders as $order) {
                $paymentHistory[] = [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'payment_method' => $order->payment_method,
                    'payment_status' => $order->payment_status,
                    'amount' => $order->payment_amount ?? $order->total_amount,
                    'payment_date' => $order->payment_date ? Carbon::parse($order->payment_date)->toISOString() : null,
                    'order_date' => Carbon::parse($order->order_date)->toISOString(),
                    'transaction_id' => $order->transaction_id,
                    'status' => $order->status
                ];
            }

            // Calcular estadísticas
            $stats = [
                'total_orders' => count($paymentHistory),
                'total_paid' => $orders->where('payment_status', 'paid')->count(),
                'total_pending' => $orders->where('payment_status', 'pending')->count(),
                'total_failed' => $orders->where('payment_status', 'failed')->count(),
                'total_amount' => $orders->where('payment_status', 'paid')->sum('payment_amount')
            ];

            return response()->json([
                'success' => true,
                'data' => $paymentHistory,
                'stats' => $stats,
                'filters_applied' => [
                    'status' => $paymentStatus ?? 'all',
                    'method' => $paymentMethod ?? 'all'
                ]
            ], 200);

        } catch (Exception $e) {
            Log::error('Error obteniendo historial de pagos', [
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
     * Obtener métodos de pago disponibles
     */
    public function getPaymentMethods()
    {
        $paymentMethods = [
            [
                'id' => 'transferencia',
                'name' => 'Transferencia Bancaria',
                'description' => 'Pago mediante transferencia a cuenta bancaria',
                'icon' => 'bank',
                'available' => true
            ],
            [
                'id' => 'efectivo',
                'name' => 'Efectivo',
                'description' => 'Pago en efectivo al momento de la entrega',
                'icon' => 'cash',
                'available' => true
            ],
            [
                'id' => 'tarjeta',
                'name' => 'Tarjeta de Crédito/Débito',
                'description' => 'Pago con tarjeta de crédito o débito',
                'icon' => 'card',
                'available' => true
            ],
            [
                'id' => 'yape',
                'name' => 'Yape',
                'description' => 'Pago mediante Yape',
                'icon' => 'mobile',
                'available' => true
            ],
            [
                'id' => 'plin',
                'name' => 'Plin',
                'description' => 'Pago mediante Plin',
                'icon' => 'mobile',
                'available' => true
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $paymentMethods
        ], 200);
    }
}
