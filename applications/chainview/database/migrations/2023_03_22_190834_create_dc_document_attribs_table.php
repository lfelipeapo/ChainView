<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDcDocumentAttribsTable extends Migration
{
    public function up()
    {
        Schema::create('dcdocumentattrib', function (Blueprint $table) {
            $table->id();
            $table->string('numero_doc', 20)->nullable();
            $table->unsignedBigInteger('cdattribute');
            $table->string('nmuserupd', 255)->nullable();
            $table->unsignedBigInteger('cdrevision');
            $table->unsignedBigInteger('cdcategory');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dcdocumentattrib');
    }
}

