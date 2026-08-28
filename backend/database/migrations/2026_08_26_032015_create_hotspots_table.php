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
        Schema::create('hotspots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('panorama_id')->constrained('panoramas')->cascadeOnDelete();
            $table->decimal('yaw', 7, 2)->default(0);
            $table->decimal('pitch', 7, 2)->default(0);
            $table->string('title')->nullable();
            $table->foreignId('target_panorama_id')->constrained('panoramas')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotspots');
    }
};
