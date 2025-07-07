<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catalogo_producto_category', function (Blueprint $table) {
            $table->string('img_url')->nullable()->after('slug');
        });

        // Renombrar las primeras 6 categorías
        $nombres = [
            1 => ['name' => 'Bebes', 'img_url' => 'https://intranet.probusiness.pe/assets/tienda/categorias/bebes.webp'],
            2 => ['name' => 'Tecnologia', 'img_url' => 'https://intranet.probusiness.pe/assets/tienda/categorias/tecnologia.webp'],
            3 => ['name' => 'Hogar', 'img_url' => 'https://intranet.probusiness.pe/assets/tienda/categorias/hogar.webp'],
            4 => ['name' => 'Mascotas', 'img_url' => 'https://intranet.probusiness.pe/assets/tienda/categorias/mascotas.webp'],
            5 => ['name' => 'Joyas gafas y relojes', 'img_url' => 'https://intranet.probusiness.pe/assets/tienda/categorias/joyas.webp'],
            6 => ['name' => 'Maletas, bolsas y fundas', 'img_url' => 'https://intranet.probusiness.pe/assets/tienda/categorias/maletas.webp'],
        ];
        foreach ($nombres as $id => $data) {
            DB::table('catalogo_producto_category')->where('id', $id)->update([
                'name' => $data['name'],
                'img_url' => $data['img_url'],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalogo_producto_category', function (Blueprint $table) {
            $table->dropColumn('img_url');
        });
        // No revertimos los nombres para evitar pérdida de información
    }
}; 