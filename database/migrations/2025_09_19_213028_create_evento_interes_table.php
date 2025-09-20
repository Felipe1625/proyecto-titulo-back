<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('evento_interes', function (Blueprint $table) {
            $table->unsignedBigInteger('id_evento');
            $table->unsignedBigInteger('id_interes');

            // Definición de las claves foráneas
            $table->foreign('id_evento')->references('id_evento')->on('evento')->onDelete('cascade');
            $table->foreign('id_interes')->references('id_interes')->on('interes')->onDelete('cascade');

            // Definición de la clave primaria compuesta
            $table->primary(['id_evento', 'id_interes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evento_interes');
    }
};
