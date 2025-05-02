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
        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestamo_id')->constrained('prestamos')->onDelete('cascade');
            $table->string('nombre_articulo', 100);
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['Prendado', 'Retirado', 'Vencido', 'Vendido'])->default('Prendado');
            $table->string('foto_url', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articulos');
    }
};
