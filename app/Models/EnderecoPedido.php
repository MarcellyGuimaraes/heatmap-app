<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnderecoPedido extends Model
{
    protected $table = 'endereco_pedido';

    protected $primaryKey = 'endereco_entrega_id';

    public $timestamps = true;

    protected $fillable = [
        'cliente_id',
        'end_rua',
        'end_numero',
        'end_bairro',
        'end_cidade',
        'end_estado',
        'end_cep',
    ];

    public function pedido()
    {
        return $this->hasOne(Pedido::class, 'endereco_entrega_id');
    }

    public function enderecoCompleto()
    {
        return $this->end_rua . ', ' . $this->end_numero . ', ' . $this->end_cidade . ', ' . $this->end_estado;
    }
}
