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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('secondary_password')->nullable()->after('password');
        });

        // Seed mật khẩu phụ mặc định cho mọi project hiện có (owner-only)
        \Illuminate\Support\Facades\DB::table('projects')->update([
            'secondary_password' => \Illuminate\Support\Facades\Hash::make(\App\Models\Project::SECONDARY_DEFAULT),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('secondary_password');
        });
    }
};
