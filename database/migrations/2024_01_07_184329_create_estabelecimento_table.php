<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('estabelecimentos', function (Blueprint $table) {
            $table->bigIncrements('est_id');
            $table->string('est_nome');
            $table->string('est_endereco');
            $table->string('est_cidade');
            $table->string('est_estado');
            $table->string('est_cep');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('estabelecimentos');
    }
};