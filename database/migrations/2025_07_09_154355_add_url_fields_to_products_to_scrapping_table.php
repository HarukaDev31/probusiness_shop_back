<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUrlFieldsToProductsToScrappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catalogo_producto', function (Blueprint $table) {
            $table->text('url_tienda')->nullable()->after('status');
            $table->text('url_alibaba')->nullable()->after('url_tienda');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_to_scrapping', function (Blueprint $table) {
            $table->dropColumn(['url_tienda', 'url_alibaba']);
        });
    }
}
