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
        Schema::table('tbl_registros', function (Blueprint $table) {
            $table->unsignedBigInteger('info_carnet_id')->nullable()->after('evento_persona_id');
            $table->string('foto_carnet')->nullable()->after('info_carnet_id');
            $table->boolean('status')->default(true)->after('foto_carnet');

            $table->foreign('info_carnet_id')->references('id')->on('tbl_info_carnet')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_registros', function (Blueprint $table) {
            $table->dropForeign(['info_carnet_id']);
            $table->dropColumn(['info_carnet_id', 'foto_carnet', 'status']);
        });
    }
};
