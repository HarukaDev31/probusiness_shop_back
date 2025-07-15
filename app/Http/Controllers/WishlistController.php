<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wishlist;
use App\Models\Product;

class WishlistController extends Controller
{
    /**
     * Add a product to the user's wishlist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'product_id' => 'required|integer|exists:catalogo_producto,id',
        ]);

        $userId = Auth::id();
        $productId = $request->input('product_id');

        // Verificar si el producto ya está en la wishlist del usuario
        $existingWishlist = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existingWishlist) {
            //remove the product from the wishlist
            $existingWishlist->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Producto eliminado de la lista de deseos',
            ], 200);
        }

        // Crear el nuevo item en la wishlist
        $wishlist = Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        // Obtener información del producto
        $product = Product::find($productId);

        return response()->json([
            'status' => 'success',
            'message' => 'Producto agregado a la lista de deseos',
            'data' => [
                'wishlist_id' => $wishlist->id,
                'product' => $product,
            ],
        ], 201);
    }

    /**
     * Get the user's wishlist.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $userId = Auth::id();

        $wishlist = Wishlist::where('user_id', $userId)
            ->with('product.category')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $wishlist,
        ], 200);
    }

    /**
     * Remove a product from the user's wishlist.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $userId = Auth::id();

        $wishlist = Wishlist::where('user_id', $userId)
            ->where('id', $id)
            ->first();

        if (!$wishlist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item no encontrado en la lista de deseos',
            ], 404);
        }

        $wishlist->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Producto eliminado de la lista de deseos',
        ], 200);
    }
}
