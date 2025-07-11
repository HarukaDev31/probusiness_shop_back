<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationIdsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Agregar campos de ubicación por ID
            $table->unsignedInteger('customer_departamento_id')->nullable()->after('customer_phone');
            $table->unsignedInteger('customer_provincia_id')->nullable()->after('customer_departamento_id');
            $table->unsignedInteger('customer_distrito_id')->nullable()->after('customer_provincia_id');
            
            // Agregar índices para mejorar el rendimiento
            $table->index('customer_departamento_id');
            $table->index('customer_provincia_id');
            $table->index('customer_distrito_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Remover índices
            $table->dropIndex(['customer_departamento_id']);
            $table->dropIndex(['customer_provincia_id']);
            $table->dropIndex(['customer_distrito_id']);
            
            // Remover columnas
            $table->dropColumn([
                'customer_departamento_id',
                'customer_provincia_id',
                'customer_distrito_id'
            ]);
        });
    }
}
