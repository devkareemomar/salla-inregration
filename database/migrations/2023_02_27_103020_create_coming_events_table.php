<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coming_events', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('merchant_id');
            $table->string('event');
            $table->bigInteger('order_id');
            $table->string('status');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->enum('message',['sent','failed'])->nullable();
            $table->text('event_json');
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
        Schema::dropIfExists('coming_events');
    }
};
