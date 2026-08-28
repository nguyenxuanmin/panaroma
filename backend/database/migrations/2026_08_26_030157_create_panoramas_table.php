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
        Schema::create('panoramas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('floor_id')->constrained('floors')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->unsignedInteger('number');
            $table->string('thumbnail')->nullable();
            $table->string('url')->nullable();
            $table->decimal('map_x', 6, 2)->nullable();
            $table->decimal('map_y', 6, 2)->nullable();
            $table->decimal('map_angle', 7, 2)->default(0);
            $table->decimal('default_yaw', 7, 2)->default(0);
            $table->decimal('default_pitch', 7, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('panoramas');
    }
};
