<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // drop table if exists
        //set foreign key checks to 0
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_items');

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique(); // Formato: YYMES00001
            $table->string('uuid')->unique();
            
            // Información del cliente
            $table->string('customer_full_name');
            $table->string('customer_dni', 20);
            $table->string('customer_email');
            $table->string('customer_phone', 20);
            
            // Dirección del cliente
            $table->string('customer_province');
            $table->string('customer_city');
            $table->string('customer_district');
            
            // Información de la orden
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('pending'); // pending, processing, shipped, delivered, cancelled
            $table->timestamp('order_date');
            
            // Metadata
            $table->string('source')->nullable(); // web, mobile, api
            $table->text('user_agent')->nullable();
            $table->bigInteger('timestamp')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Índices
            $table->index('order_number');
            $table->index('customer_email');
            $table->index('status');
            $table->index('order_date');
        });

        // Tabla de items de la orden
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedInteger('product_id');
            
            // Información del producto en el momento de la orden
            $table->string('product_name');
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('total_price', 10, 2);
            $table->text('product_image')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('catalogo_producto');
            
            // Índices
            $table->index('order_id');
            $table->index('product_id');
        });
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
} 