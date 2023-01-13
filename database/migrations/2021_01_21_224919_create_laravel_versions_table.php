<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('laravel_versions', function (Blueprint $table) {
            $table->id();
            $table->integer('major');
            $table->integer('minor')->default(0);
            $table->integer('patch')->default(0);
            $table->boolean('is_lts')->default(false);
            $table->date('released_at');
            $table->date('ends_bugfixes_at')->nullable();
            $table->date('ends_securityfixes_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('laravel_versions');
    }
};
