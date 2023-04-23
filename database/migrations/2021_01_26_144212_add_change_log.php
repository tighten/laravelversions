<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChangeLog extends Migration
{
    public function up()
    {
        Schema::table('laravel_versions', function (Blueprint $table) {
            $table->string('semver')->nullable()->after('patch');
            $table->string('first_release')->nullable()->after('semver');
            $table->boolean('is_front')->default(false)->after('is_lts');
            $table->longText('changelog')->nullable()->after('is_front');
            $table->integer('order')->default(0)->after('changelog');
        });
    }

    public function down()
    {
        Schema::table('laravel_versions', function (Blueprint $table) {
            $table->dropColumn('semver');
            $table->dropColumn('first_release');
            $table->dropColumn('is_front');
            $table->dropColumn('changelog');
            $table->dropColumn('order');
        });
    }
}
