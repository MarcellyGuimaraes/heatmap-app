<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $primaryKey = 'cliente_id';

    public $timestamps = true;

    protected $fillable = [
        'cli_nome',
        'cli_endereco',
        'cli_cidade',
        'cli_estado',
        'cli_cep'
    ];
}