<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GeoZoneDelivery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geo_zone_delivery', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('radius',900)->nullable();
            $table->double('price', 8, 2);
            $table->foreignId('restorant_id')->references('id')->on('companies');
            $table->string('colorarea')->nullable();
            $table->integer('active')->default(1);
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
        //
    }
}
