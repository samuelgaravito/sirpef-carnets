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
        Schema::table('tbl_info_carnet', function (Blueprint $table) {
            // Agrega los nuevos campos después de la imagen del pie de página de la configuración
            $table->string('imagen_fondo_reverso')->nullable()->after('imagen_pie_pagina');
            $table->string('imagen_qr')->nullable()->after('imagen_fondo_reverso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_info_carnet', function (Blueprint $table) {
            $table->dropColumn(['imagen_fondo_reverso', 'imagen_qr']);
        });
    }
};