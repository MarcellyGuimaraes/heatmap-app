<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $primaryKey = 'ped_id';

    public $timestamps = true;

    protected $fillable = [
        'cliente_id',
        'endereco_entrega_id',
        'ped_status',
        'ped_valor_total',
        'ped_observacoes',
    ];

    public function enderecoEntrega()
    {
        return $this->belongsTo(EnderecoPedido::class, 'endereco_entrega_id');
    }

}
