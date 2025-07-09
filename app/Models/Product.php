<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'catalogo_producto';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'category_id',
        'status',
        'supplier_id',
        'packaging_info',
        'delivery_lead_times',
        'contact_card_url',
        'main_image_url',
        'aditional_image1_url',
        'aditional_image2_url',
        'aditional_video1_url',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the wishlist items for this product.
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
}
