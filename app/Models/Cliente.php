<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes'; // Nome da tabela no banco de dados

    protected $primaryKey = 'cliente_id'; // Chave primária da tabela

    public $timestamps = true; // Habilita os campos created_at e updated_at

    protected $fillable = [
        'cli_nome',
        'cli_endereco',
        'cli_cidade',
        'cli_estado',
        'cli_cep'
    ];

    // Relacionamento ou métodos adicionais, se houver
}