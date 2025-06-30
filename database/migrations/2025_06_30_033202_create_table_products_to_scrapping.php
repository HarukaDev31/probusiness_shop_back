<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableProductsToScrapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('products_to_scrapping')) {
            Schema::dropIfExists('products_to_scrapping');
        }
        Schema::create('products_to_scrapping', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('category_id');
            $table->string('name');
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->foreign('category_id')->references('id')->on('catalogo_producto_category')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_products_to_scrapping');
    }
}
