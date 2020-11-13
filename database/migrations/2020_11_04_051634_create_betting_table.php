<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('betting_logic', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default();
            $table->integer('fight_id')->default();
            $table->tinyInteger('side')->default();
            $table->unsignedDecimal('amount')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('betting_logic');
    }
}
