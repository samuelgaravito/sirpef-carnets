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
        Schema::create('tbl_info_carnet', function (Blueprint $table) {
            $table->id();
            $table->text('texto_superior');
            $table->text('texto_inferior');
            $table->string('sello'); // URL imagen
            $table->string('firma')->nullable(); // URL imagen, nulo permitido
            $table->string('imagen_fondo'); // URL imagen
            $table->string('imagen_pie_pagina'); // URL imagen
            $table->boolean('estatus')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_info_carnet');
    }
};
