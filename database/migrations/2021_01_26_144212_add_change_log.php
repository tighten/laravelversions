<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChangeLog extends Migration
{
    public function up()
    {
        Schema::table('laravel_versions', function (Blueprint $table) {
            $table->string('first_release')->nullable()->after('patch');
            $table->longText('changelog')->nullable()->after('first_release');
            $table->integer('order')->default(0)->after('changelog');
        });
    }

    public function down()
    {
        Schema::table('laravel_versions', function (Blueprint $table) {
            $table->dropColumn('first_release');
            $table->dropColumn('changelog');
            $table->dropColumn('order');
        });
    }
}
