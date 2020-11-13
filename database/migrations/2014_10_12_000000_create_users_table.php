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
            $table->id();
            $table->string('username',8)->nullable();
            $table->unsignedBigInteger('phone_number')->default('0');
            $table->unsignedBigInteger('referral_code')->default('0');
            $table->tinyInteger('user_role')->default('0');
            $table->tinyInteger('activation')->default('0');
            $table->string('password');
            $table->timestamp('registered_date')->default(DB::raw('CURRENT_TIMESTAMP'));
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
