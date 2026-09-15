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
        Schema::table('panaromas', function (Blueprint $table) {
            $table->foreignId('building_id')->nullable()->after('floor_id')->constrained('buildings')->cascadeOnDelete();
        });
        Schema::table('panaromas', function (Blueprint $table) {
            try {
                $table->dropForeign(['floor_id']);
            } catch (\Throwable $e) {}
        });
        Schema::table('panaromas', function (Blueprint $table) {
            $table->foreignId('floor_id')->nullable()->change();
        });
        Schema::table('panaromas', function (Blueprint $table) {
            $table->foreign('floor_id')->references('id')->on('floors')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panaromas', function (Blueprint $table) {
            //
        });
    }
};
