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
        Schema::create('evento', function (Blueprint $table) {
            $table->id('id_evento');
            $table->unsignedBigInteger('fk_id_organizador');
            $table->string('nombre_evento', 255);
            $table->text('descripcion_evento')->nullable();
            $table->date('fecha_evento');
            $table->time('hora_inicio_evento');
            $table->time('hora_termino_evento');
            $table->integer('cantidad_personas_evento');
            $table->unsignedBigInteger('fk_id_direccion_particular')->nullable();
            $table->unsignedBigInteger('fk_id_lugar_publico')->nullable();
            $table->string('estado_evento', 50);
            $table->timestamps();

            // Claves foráneas
            $table->foreign('fk_id_organizador')->references('id_usuario')->on('usuario')->onDelete('cascade');
            $table->foreign('fk_id_direccion_particular')->references('id_usuario_direccion')->on('usuario_direccion')->onDelete('set null');
            $table->foreign('fk_id_lugar_publico')->references('id_lugar')->on('lugar_publico')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evento');
    }
};
