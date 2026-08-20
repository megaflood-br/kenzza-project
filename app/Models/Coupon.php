<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'tipo',
        'valor',
        'limite_uso',
        'vezes_usado',
        'validade',
        'ativo'
    ];

    /**
     * Mapeamento de Casts para garantir integridade dos tipos nativos
     */
    protected $casts = [
        'ativo' => 'boolean',
        'validade' => 'datetime',
        'valor' => 'float',
        'limite_uso' => 'integer',
        'vezes_usado' => 'integer'
    ];

    /**
     * Relacionamento N para N: Um cupom pode ser restrito a várias categorias
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'coupon_category');
    }
}
