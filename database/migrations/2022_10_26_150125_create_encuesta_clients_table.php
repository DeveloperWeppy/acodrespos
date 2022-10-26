<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEncuestaClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encuesta_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_question')->references('id')->on('encuesta_ordens');
            $table->string('answer', 250);
            $table->foreignId('id_ratings')->references('id')->on('ratings');
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
        Schema::dropIfExists('encuesta_clients');
    }
}
