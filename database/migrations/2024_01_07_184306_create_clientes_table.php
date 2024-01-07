<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->bigIncrements('cliente_id');
            $table->string('cli_nome');
            $table->string('cli_endereco');
            $table->string('cli_numero');
            $table->string('cli_bairro');
            $table->string('cli_cidade');
            $table->string('cli_estado');
            $table->string('cli_cep');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes');
    }
};