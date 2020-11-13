<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_info', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('first_name',50)->nullable();
            $table->string('last_name',50)->nullable();
            $table->string('address',255)->nullable();
            $table->string('city',50)->nullable();
            $table->string('country',50)->nullable();
            $table->string('zipcode',50)->nullable();
            $table->text('bio')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_info');
    }
}
