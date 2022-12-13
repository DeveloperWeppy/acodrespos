<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePqrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pqrs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 250);
            $table->string('email', 250);
            $table->string('phone', 20);
            $table->string('type_radicate', 250);
            $table->string('num_order', 250)->nullable();
            $table->foreignId('order_id')->nullable()->references('id')->on('orders');
            $table->text('message', 2500);
            $table->string('evidence', 255)->nullable();
            $table->string('status', 50)->default('radicado');
            $table->text('answer_radicate', 2500)->nullable();
            $table->string('evidence_answer', 255)->nullable();
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
        Schema::dropIfExists('pqrs');
    }
}
