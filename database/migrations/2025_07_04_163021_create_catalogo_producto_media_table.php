<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //drop table if exists
        Schema::dropIfExists('catalogo_producto_media');
        
        Schema::create('catalogo_producto_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_catalogo_producto');
            $table->string('url');
            $table->timestamps();
            $table->enum('type', ['image', 'video'])->default('image');
            $table->index('id_catalogo_producto');
            $table->foreign('id_catalogo_producto')->references('id')->on('catalogo_producto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catalogo_producto_media');
    }
};
