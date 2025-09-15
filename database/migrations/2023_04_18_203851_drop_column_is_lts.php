<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laravel_versions', function (Blueprint $table) {
            $table->dropColumn('is_lts');
        });
    }

    public function down(): void
    {
        Schema::table('laravel_versions', function (Blueprint $table) {
            $table->boolean('is_lts')->default(false);
        });
    }
};
