<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('order_id')->unique();
            $table->string('email');
            $table->string('full_name');
            $table->string('dni')->nullable();
            $table->string('document_type');
            $table->string('phone');
            $table->string('address');
            $table->string('ruc')->nullable();
            $table->string('business_name')->nullable();
            $table->string('city');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
