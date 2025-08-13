<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdAttributesTable extends Migration
{
    public function up()
    {
        Schema::create('adattribute', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cdattribute');
            $table->string('nmattribute', 255)->nullable();
            $table->string('nmlabel', 255)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('adattribute');
    }
}

