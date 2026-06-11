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
        // 1. Añadir el campo a tbl_registros justo después de emision
        Schema::table('tbl_registros', function (Blueprint $table) {
            $table->string('imagen_qr')->nullable()->after('emision');
        });

        // 2. Eliminar el campo que estaba por error en tbl_info_carnet
        Schema::table('tbl_info_carnet', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_info_carnet', 'imagen_qr')) {
                $table->dropColumn('imagen_qr');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Operación inversa si decides hacer un rollback
        Schema::table('tbl_info_carnet', function (Blueprint $table) {
            $table->string('imagen_qr')->nullable()->after('imagen_fondo_reverso');
        });

        Schema::table('tbl_registros', function (Blueprint $table) {
            $table->dropColumn('imagen_qr');
        });
    }
};