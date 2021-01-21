<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaravelVersionsTable extends Migration
{
    public function up()
    {
        Schema::create('laravel_versions', function (Blueprint $table) {
            $table->id();
            $table->string('major');
            $table->string('minor');
            $table->string('patch')->nullablle();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('laravel_versions');
    }
}
