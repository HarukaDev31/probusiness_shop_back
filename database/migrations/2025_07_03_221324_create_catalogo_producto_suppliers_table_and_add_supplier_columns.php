<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogoProductoSuppliersTableAndAddSupplierColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Crear tabla catalogo_producto_suppliers
        Schema::create('catalogo_producto_suppliers', function (Blueprint $table) {
            $table->id();
            $table->text('supplier_name');
            $table->timestamps();
        });

        // Agregar columnas a catalogo_producto
        Schema::table('catalogo_producto', function (Blueprint $table) {
            $table->unsignedInteger('supplier_id')->nullable();
            $table->text('packaging_info')->nullable();
            $table->text('delivery_lead_times')->nullable();
        });

        // Agregar foreign key
//        Schema::table('catalogo_producto', function (Blueprint $table) {
  //          $table->foreign('supplier_id')->references('id')->on('catalogo_producto_suppliers');
    //    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar foreign key
        Schema::table('catalogo_producto', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
        });

        // Eliminar columnas de catalogo_producto
        Schema::table('catalogo_producto', function (Blueprint $table) {
            $table->dropColumn(['supplier_id', 'packaging_info', 'delivery_lead_times']);
        });

        // Eliminar tabla catalogo_producto_suppliers
        Schema::dropIfExists('catalogo_producto_suppliers');
    }
}
