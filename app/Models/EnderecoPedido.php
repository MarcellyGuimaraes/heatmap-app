<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnderecoPedido extends Model
{
    protected $table = 'endereco_pedido'; // Nome da tabela no banco de dados

    protected $primaryKey = 'endereco_entrega_id'; // Chave primária da tabela

    public $timestamps = true; // Habilita os campos created_at e updated_at

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
}
