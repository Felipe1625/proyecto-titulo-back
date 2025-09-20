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
        Schema::create('usuario_direccion', function (Blueprint $table) {
            $table->id('id_usuario_direccion');
            $table->unsignedBigInteger('fk_id_usuario');
            $table->string('nombre_direccion', 255);
            $table->decimal('latitud_direccion', 10, 7);
            $table->decimal('longitud_direccion', 10, 7);
            $table->boolean('direccion_verificada')->default(false);
            $table->timestamps();

            $table->foreign('fk_id_usuario')->references('id_usuario')->on('usuario')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario_direccion');
    }
};
