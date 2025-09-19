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
        Schema::create('interes', function (Blueprint $table) {
            $table->increments('id_interes');
            
            $table->unsignedInteger('id_categoria_interes');
            $table->foreign('id_categoria_interes')
                  ->references('id_categoria_interes')
                  ->on('categoria_interes')
                  ->onDelete('cascade');

            $table->string('nombre_interes');
            $table->string('descripcion_interes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interes');
    }
};
