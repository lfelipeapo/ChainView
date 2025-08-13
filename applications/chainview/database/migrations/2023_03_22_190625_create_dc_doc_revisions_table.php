<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDcDocRevisionsTable extends Migration
{
    public function up()
    {
        Schema::create('dcdocrevision', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cdrevision');
            $table->unsignedTinyInteger('fgcurrent')->nullable();
            $table->unsignedTinyInteger('fgtrainrequired')->nullable();
            $table->text('dssummary')->nullable();
            $table->string('nmtitle', 255)->nullable();
            $table->string('iddocument', 50)->nullable();
            $table->unsignedBigInteger('cdcategory')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dcdocrevision');
    }
}
