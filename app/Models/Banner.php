<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['titulo', 'imagem_desktop', 'imagem_mobile', 'link', 'ordem', 'ativo'];
}
