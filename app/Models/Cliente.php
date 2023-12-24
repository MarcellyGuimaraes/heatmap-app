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
        'cli_numero',
        'cli_bairro',
        'cli_cidade',
        'cli_estado',
        'cli_cep'
    ];

    public function enderecoCompleto()
    {
        return $this->cli_endereco . ', ' . $this->cli_numero . ', ' . $this->cli_cidade . ', ' . $this->cli_estado;
    }
}