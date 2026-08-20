<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSheet extends Model
{
    protected $fillable = [
        'foto', 'pdf', 'nome', 'descricao', 'ativos_tecnologia',
        'funcoes', 'modo_usar', 'dados_analiticos', 'seguranca'
    ];
}