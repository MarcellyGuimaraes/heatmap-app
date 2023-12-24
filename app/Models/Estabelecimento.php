<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estabelecimento extends Model
{
    protected $table = 'estabelecimentos';

    protected $primaryKey = 'est_id';

    public $timestamps = true;

    protected $fillable = [
        'est_nome',
        'est_endereco',
        'est_cidade',
        'est_estado',
        'est_cep',
    ];

}