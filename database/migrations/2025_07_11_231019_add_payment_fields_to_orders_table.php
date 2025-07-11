<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Campos de pago
            $table->enum('payment_method', ['transferencia', 'efectivo', 'tarjeta', 'yape', 'plin'])->nullable()->after('total_amount');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->after('payment_method');
            $table->decimal('payment_amount', 10, 2)->nullable()->after('payment_status');
            $table->timestamp('payment_date')->nullable()->after('payment_amount');
            $table->string('transaction_id')->nullable()->after('payment_date');
            $table->text('payment_notes')->nullable()->after('transaction_id');
            
            // Índices para consultas de pago
            $table->index('payment_status');
            $table->index('payment_method');
            $table->index('payment_date');
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
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['payment_method']);
            $table->dropIndex(['payment_date']);
            
            // Remover columnas
            $table->dropColumn([
                'payment_method',
                'payment_status',
                'payment_amount',
                'payment_date',
                'transaction_id',
                'payment_notes'
            ]);
        });
    }
}
