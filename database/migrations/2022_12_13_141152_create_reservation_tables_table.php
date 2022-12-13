<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservation_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('companie_id')->references('id')->on('companies');
            $table->foreignId('table_id')->references('id')->on('tables');
            $table->string('price', 255);
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
        Schema::dropIfExists('reservation_tables');
    }
}
