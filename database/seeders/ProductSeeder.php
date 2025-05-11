<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        // seeder for category table 
        for ($i = 0; $i < 20; $i++) {
            DB::table('catalogo_producto_category')->insert([
                'name' => $faker->unique()->word,
                'slug' => $faker->unique()->slug,
            ]);
        }
        for ($i = 0; $i < 100000; $i++) {
            DB::table('catalogo_producto')->insert([
                'nombre' => 'Producto-' . $faker->unique()->numberBetween(1000, 99999999),
                'precio' => $faker->randomFloat(2, 10, 1000),
                'qty_box' => $faker->numberBetween(1, 50),
                'cbm_box' => $faker->randomFloat(2, 0.1, 10),
                'dias_entrega' => $faker->numberBetween(1, 30),
                'delivery' => $faker->randomFloat(2, 10, 200),
                'colores' => $faker->randomElement(['Rojo,Azul,Verde', 'Negro,Blanco', '']),
                'notas' => $faker->optional(0.7, '')->sentence,
                'whechat_phone' => $faker->optional(0.8, null)->numerify('9########'),
                'contact_card_url' => 'http://localhost:8080/probusinees-intranet/assets/catalogo_productos/1746671071_Imagen%20de%20WhatsApp%202024-12-12%20a%20las%2003.15.25_4a4fb705.jpg',
                'main_image_url' => 'http://localhost:8080/probusinees-intranet/assets/catalogo_productos/1746671071_aduana.png',
                'aditional_image1_url' => 'http://localhost:8080/probusinees-intranet/assets/catalogo_productos/1746671071_aduana.png',
                'aditional_image2_url' => 'http://localhost:8080/probusinees-intranet/assets/catalogo_productos/1746671071_aduana.png',
                'aditional_video1_url' => 'http://localhost:8080/probusinees-intranet/assets/catalogo_productos/1746671071_It%20Takes%20Two%20%20%202025-04-19%2000-06-20.mp4',
                'moq' => $faker->numberBetween(1, 20),
                'servicio_impo' => $faker->randomFloat(2, 100, 500),
                'arancel' => $faker->randomFloat(2, 1, 10),
                'igv' => 18.0, // IGV fijo
                'antidumping' => $faker->optional(0.3, 0)->randomFloat(2, 0, 1000),
                'percepcion' => 2.0, // Percepción fija
                'precio_usd' => $faker->optional(1)->randomFloat(2, 50, 500),
                'precio_peru' => $faker->optional(1)->randomFloat(2, 100, 2000),
                'status' => $faker->randomElement(['EN TIENDA', 'COTIZADO']),
                'category_id' => $faker->numberBetween(1, 20),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
