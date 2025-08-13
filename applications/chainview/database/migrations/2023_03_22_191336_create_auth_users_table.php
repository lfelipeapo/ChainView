<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthUsersTable extends Migration
{
    public function up()
    {
        Schema::create('auth_user', function (Blueprint $table) {
            $table->id();
            $table->string('password', 128);
            $table->timestamp('last_login')->nullable();
            $table->boolean('is_superuser');
            $table->string('username', 150)->unique();
            $table->string('first_name', 150);
            $table->string('last_name', 150);
            $table->string('email', 254);
            $table->boolean('is_staff');
            $table->boolean('is_active');
            $table->timestamp('date_joined');
        });
    }

    public function down()
    {
        Schema::dropIfExists('auth_user');
    }
}
