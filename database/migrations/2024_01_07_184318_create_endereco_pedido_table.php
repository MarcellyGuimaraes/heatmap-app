<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('endereco_pedido', function (Blueprint $table) {
            $table->bigIncrements('endereco_entrega_id');
            $table->unsignedBigInteger('cliente_id');
            $table->string('end_rua');
            $table->string('end_numero');
            $table->string('end_bairro');
            $table->string('end_cidade');
            $table->string('end_estado');
            $table->string('end_cep');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('endereco_pedido');
    }
};