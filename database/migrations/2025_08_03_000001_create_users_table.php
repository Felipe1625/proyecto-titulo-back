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
        Schema::create('usuario', function (Blueprint $table) {
            $table->increments('id_usuario');
            $table->unsignedInteger('id_tipo_usuario');
            $table->foreign('id_tipo_usuario')->references('id_tipo_usuario')->on('tipo_usuario')->onDelete('cascade');
            $table->string('nombre_usuario');
            $table->string('email_usuario')->unique();
            $table->string('password_usuario');
            $table->string('url_img_usuario')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
