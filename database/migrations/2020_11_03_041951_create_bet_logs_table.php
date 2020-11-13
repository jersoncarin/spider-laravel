<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBetLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bet_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->default('1');
            $table->bigInteger('fight_no')->default('0');
            $table->bigInteger('fight_id')->default('0');
            $table->string('side',30);
            $table->string('action',30);
            $table->unsignedDecimal('amount',15,2)->default('0');
            $table->unsignedDecimal('bet',15,2)->default('0');
            $table->unsignedDecimal('balance',15,2)->default('0');
            $table->timestamp('logs_date')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bet_logs');
    }
}

