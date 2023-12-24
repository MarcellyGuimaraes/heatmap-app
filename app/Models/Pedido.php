<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos'; // Nome da tabela no banco de dados

    protected $primaryKey = 'ped_id'; // Chave primária da tabela

    public $timestamps = true; // Habilita os campos created_at e updated_at

    protected $fillable = [
        'cliente_id',
        'endereco_entrega_id', // Referência ao endereço do pedido
        'ped_status',
        'ped_valor_total',
        'ped_observacoes',
    ];

    public function enderecoEntrega()
    {
        return $this->belongsTo(EnderecoPedido::class, 'endereco_entrega_id');
    }

    // Relacionamento ou métodos adicionais, se houver
}
