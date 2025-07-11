<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        
        Schema::table('users', function (Blueprint $table) {
            // Campos de ubicación
            $table->unsignedInteger('departamento_id')->nullable()->after('whatsapp');
            $table->unsignedInteger('provincia_id')->nullable()->after('departamento_id');
            $table->unsignedInteger('distrito_id')->nullable()->after('provincia_id');
            
            // Campos adicionales del usuario
            $table->string('dni', 20)->nullable()->after('distrito_id');
            $table->integer('edad')->nullable()->after('dni');
            
            // Foreign keys
            $table->foreign('departamento_id')->references('Id_Departamento')->on('departamento');
            $table->foreign('provincia_id')->references('Id_Provincia')->on('provincia');
            $table->foreign('distrito_id')->references('Id_Distrito')->on('distrito');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['departamento_id']);
            $table->dropForeign(['provincia_id']);
            $table->dropForeign(['distrito_id']);
            
            // Drop columns
            $table->dropColumn([
                'departamento_id',
                'provincia_id', 
                'distrito_id',
                'dni',
                'edad',
                'sexo'
            ]);
        });
    }
} 