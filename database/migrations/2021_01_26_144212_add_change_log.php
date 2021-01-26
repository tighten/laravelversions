<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChangeLog extends Migration
{
    public function up()
    {
        Schema::table('laravel_versions', function (Blueprint $table) {
            $table->text('changelog')->nullable();
            $table->boolean('is_front')->default(false);
        });
    }

    public function down()
    {
        Schema::table('laravel_versions', function (Blueprint $table) {
            $table->dropColumn('changelog');
            $table->dropColumn('is_front');
        });
    }
}
