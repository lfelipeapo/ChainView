<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('dccategory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cdcategory');
            $table->unsignedBigInteger('cdcategoryowner')->nullable();
            $table->string('nmcategory', 255)->nullable();
            $table->string('idcategory', 50)->nullable();
            $table->unsignedTinyInteger('fglogo')->nullable();
            $table->unsignedTinyInteger('fgenablephysfile')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dccategory');
    }
}
