<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('image')->nullable();
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('user_type');
            $table->integer('role');
            $table->string('reference')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_change_password')->default(false);
            $table->rememberToken();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
