<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->integer('companie_id', 11);
            $table->integer('client_id', 11);
            $table->integer('check_percentage', 11);
            $table->foreignId('reservation_reason_id')->references('id')->on('reservation_reasons');
            $table->longText('description');
            $table->integer('mesas', 11);
            $table->integer('personas', 11);
            $table->string('payment_status', 255);
            $table->integer('reservation_status', 11);
            $table->integer('active', 11);
            $table->string('note', 255);
            $table->longText('observations');
            $table->dateTime('date_reservation');
            $table->string('total', 255);
            $table->string('update_price', 255);
            $table->string('pendiente', 255);
            $table->longText('payment_1');
            $table->longText('payment_2');
            $table->longText('payment_3');
            $table->string('url_payment1', 255);
            $table->string('url_payment2', 255);
            $table->string('url_payment3', 255);
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
        Schema::dropIfExists('reservations');
    }
}
