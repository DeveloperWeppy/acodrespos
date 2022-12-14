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
            $table->foreignId('companie_id')->references('id')->on('companies');
            $table->foreignId('client_id')->references('id')->on('users');
            $table->integer('check_percentage')->default(0);
            $table->foreignId('reservation_reason_id')->references('id')->on('reservation_reasons');
            $table->longText('description');
            $table->integer('mesas')->default(0);
            $table->integer('personas')->default(0);
            $table->string('payment_status', 255);
            $table->integer('reservation_status')->default(0);
            $table->integer('active')->default(0);
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
