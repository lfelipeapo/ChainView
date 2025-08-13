<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDcCatDocAttribsTable extends Migration
{
    public function up()
    {
        Schema::create('dccatdocattrib', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cdcategory');
            $table->unsignedBigInteger('cdattribute');
            $table->unsignedTinyInteger('fgrequired')->nullable();
            $table->unsignedBigInteger('nrorder')->nullable();
            $table->string('nmdefaultvalue', 255)->nullable();
            $table->unsignedBigInteger('vldefaultvalue')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dccatdocattrib');
    }
}
