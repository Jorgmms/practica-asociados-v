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
        Schema::create('asociados', function (Blueprint $table) {
            $table->id(); // Columna ID autoincremental
            $table->string('nombre'); // Columna de texto para el nombre
            $table->string('DPI')->unique(); // Columna de texto para DPI, unique no se puede repetir
            
            $table->string('correo')->nullable(); // Columna pa el correo, nullable significa que puede quedar vacio
            $table->string('telefono')->nullable(); // Columna pa el cel
            $table->timestamps(); // quien crea las columnas created_at y update_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asociados');
    }
};
