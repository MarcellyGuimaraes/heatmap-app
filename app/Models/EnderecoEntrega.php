<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnderecoEntrega extends Model
{
    protected $table = 'endereco_entrega';
    protected $primaryKey = 'endereco_entrega_id';
    public $timestamps = true;

    protected $fillable = [
        'cliente',
        'rua',
        'cep',
        'numero',
        'bairro',
        'cidade',
        'estado',
    ];
}
