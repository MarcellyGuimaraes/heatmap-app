<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->bigIncrements('ped_id');
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('endereco_entrega_id');
            $table->string('ped_status');
            $table->decimal('ped_valor_total', 10, 2);
            $table->text('ped_observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pedidos');
    }
};