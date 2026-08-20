<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaKit extends Model
{
    /**
     * Atributos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'titulo',
        'categoria',
        'arquivo_path',
        'tipo',
    ];
}
