<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductPricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insertar productos con precios por cantidad
        $products = [
            [
                'name' => 'Camisetas de Algodón Premium',
                'description' => 'Camisetas de alta calidad en algodón 100%',
                'category_id' => 4, // Ropa y Accesorios
                'precio' => 30.00, // Precio base
                'prices' => json_encode([
                    '1' => 30.00,    // 1-9 unidades
                    '10' => 28.00,   // 10-49 unidades
                    '50' => 25.50,   // 50-99 unidades
                    '100' => 23.00,  // 100+ unidades
                ]),
                'status' => 'EN TIENDA',
                'main_image_url' => 'https://s.alicdn.com/@sc04/kf/Hdd21a08cbb3f4703bd4a37193cd8e8eac.jpg_720x720q50.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pantalones Deportivos',
                'description' => 'Pantalones deportivos cómodos y duraderos',
                'category_id' => 4, // Ropa y Accesorios
                'precio' => 40.00, // Precio base
                'prices' => json_encode([
                    '1' => 40.00,    // 1-9 unidades
                    '10' => 38.00,   // 10-49 unidades
                    '50' => 35.00,   // 50-99 unidades
                    '100' => 32.00,  // 100+ unidades
                ]),
                'status' => 'EN TIENDA',
                'main_image_url' => 'https://sc01.alicdn.com/kf/HTB1QqQbXQvoK1RjSZFNq6AxMVXa6.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Smartphone 5G 128GB',
                'description' => 'Smartphone de última generación con 5G',
                'category_id' => 1, // Electrónica
                'precio' => 800.00, // Precio base
                'prices' => json_encode([
                    '1' => 800.00,   // 1-4 unidades
                    '5' => 780.00,   // 5-9 unidades
                    '10' => 750.00,  // 10+ unidades
                ]),
                'status' => 'EN TIENDA',
                'main_image_url' => 'https://example.com/smartphone.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Laptop 15.6" Intel i7',
                'description' => 'Laptop profesional con procesador Intel i7',
                'category_id' => 1, // Electrónica
                'precio' => 1200.00, // Precio base
                'prices' => json_encode([
                    '1' => 1200.00,  // 1-2 unidades
                    '3' => 1150.00,  // 3-5 unidades
                    '6' => 1100.00,  // 6+ unidades
                ]),
                'status' => 'EN TIENDA',
                'main_image_url' => 'https://example.com/laptop.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sofá 3 plazas gris',
                'description' => 'Sofá moderno y cómodo para sala',
                'category_id' => 2, // Hogar y Cocina
                'precio' => 500.00, // Precio base
                'prices' => json_encode([
                    '1' => 500.00,   // 1 unidad
                    '2' => 480.00,   // 2-3 unidades
                    '4' => 450.00,   // 4+ unidades
                ]),
                'status' => 'EN TIENDA',
                'main_image_url' => 'https://example.com/sofa.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($products as $product) {
            DB::table('catalogo_producto')->insert($product);
        }

        $this->command->info('Productos con precios por cantidad creados exitosamente.');
    }
} 