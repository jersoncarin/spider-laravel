<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('type',15)->nullable();
            $table->unsignedDecimal('amount',15,2)->default('0');
            $table->bigInteger('sender_number')->default('0');
            $table->bigInteger('reciever_number')->default('0');
            $table->bigInteger('reference_number')->default('0');
            $table->text('screenshot_path')->nullable();
            $table->bigInteger('account_number')->default('0');
            $table->string('account_name',100)->nullable();
            $table->string('withdraw_msg',255)->nullable();
            $table->bigInteger('contact_number')->default('0');
            $table->timestamp('request_date')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requests');
    }
}
