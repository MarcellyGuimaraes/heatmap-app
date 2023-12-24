<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estabelecimento extends Model
{
    protected $table = 'estabelecimentos'; // Nome da tabela no banco de dados

    protected $primaryKey = 'est_id'; // Chave primária da tabela

    public $timestamps = true; // Habilita os campos created_at e updated_at

    protected $fillable = [
        'est_nome',
        'est_endereco',
        'est_cidade',
        'est_estado',
        'est_cep',
    ];

    // Relacionamento ou métodos adicionais, se houver
}