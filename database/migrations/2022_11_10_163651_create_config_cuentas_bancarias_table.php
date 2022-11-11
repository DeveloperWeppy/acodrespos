<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigCuentasBancariasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_cuentas_bancarias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rid')->references('id')->on('companies');
            $table->string('name_receptor', 250)->nullable();
            $table->string('name_bank', 250);
            $table->string('type_document', 250);
            $table->string('number_document', 250);
            $table->string('type_account', 250);
            $table->string('number_account', 250);
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
        Schema::dropIfExists('config_cuentas_bancarias');
    }
}
