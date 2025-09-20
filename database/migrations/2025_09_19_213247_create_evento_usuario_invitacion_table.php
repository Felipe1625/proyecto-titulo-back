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
        Schema::create('evento_usuario_invitacion', function (Blueprint $table) {
            $table->id('id_evento_usuario_invitacion');
            $table->unsignedBigInteger('fk_id_evento');
            $table->unsignedBigInteger('fk_id_usuario');
            $table->unsignedBigInteger('fk_id_organizador');
            $table->string('estado_invitacion', 50)->default('en espera');
            $table->timestamp('fecha_invitacion')->nullable();
            $table->text('mensaje_organizador')->nullable();
            $table->timestamps();

            // Claves foráneas
            $table->foreign('fk_id_evento')->references('id_evento')->on('evento')->onDelete('cascade');
            $table->foreign('fk_id_usuario')->references('id_usuario')->on('usuario')->onDelete('cascade');
            $table->foreign('fk_id_organizador')->references('id_usuario')->on('usuario')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evento_usuario_invitacion');
    }
};
