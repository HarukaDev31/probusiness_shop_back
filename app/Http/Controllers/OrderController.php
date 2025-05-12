<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Requests\OrderRequest;

class OrderController extends Controller
{
    private $ERROR_ORDER = 'Error creando la orden';
    private $SUCCESS_ORDER = 'Orden creada con exito';
    public function store(OrderRequest $request)
    {
        DB::beginTransaction();
        try {
           
            $uuid = Str::uuid()->toString();
            $orderId = DB::table('orders')->insertGetId([
                'order_id' => $uuid,
                'email' => $request['email'],
                'full_name' => $request['fullName'],
                'dni' => $request['dni'],
                'document_type' => $request['documentType'],
                'phone' => $request['phone'],
                'address' => $request['address'],
                'ruc' => $request['ruc'],
                'business_name' => $request['businessName'],
                'created_at' => now(),
                'city' => $request['city'],
            ]);
            foreach ($request['items'] as $item) {
                $product = DB::table('catalogo_producto')->where('id', $item['id'])->first();
                if (!$product) {
                    DB::rollBack();
                    Log::error('Product not found', ['product_id' => $item['id']]);
                    return response()->json(['error' => $this->ERROR_ORDER,], 422);
                }
                Log::info('Product found', ['product_id' => $item['id']]);
                Log::info('Order created', ['order_id' => $orderId]);
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->precio,
                ]);
            }
            DB::commit();
            return response()->json(['message' => $this->SUCCESS_ORDER, 'order_id' => $uuid], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->ERROR_ORDER, [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);
            return response()->json(['error' => 'Validation failed', 'message' => $e->getMessage()], 422);
        }
    }
}
