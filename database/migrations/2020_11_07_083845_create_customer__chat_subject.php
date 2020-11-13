<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerChatSubject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_chat_subject', function (Blueprint $table) {
            $table->id();
            $table->string('subject_name')->nullable();
            $table->bigInteger('user_id')->default();
            $table->tinyInteger('hasReply')->default();
            $table->tinyInteger('status')->default();
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
        Schema::dropIfExists('customer_chat_subject');
    }
}
