<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('companie_id')->references('id')->on('companies');
            $table->string('minimum_period', 250);
            $table->integer('condition_period')->default(0);
            $table->integer('percentage_payment')->default(0);
            $table->string('wait_time', 250);
            $table->integer('anticipation_time')->default(0);
            $table->integer('interval_time')->default(0);
            $table->string('standard_price', 250);
            $table->string('update_price', 250);
            $table->integer('check_no_cost')->default(0);
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
        Schema::dropIfExists('reservations_config');
    }
}
