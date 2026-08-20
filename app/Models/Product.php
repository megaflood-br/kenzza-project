<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; // Importante para gerar o slug

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nome',
        'ean',
        'slug', // Adicionado ao fillable
        'sku',
        'descricao',
        'preco_distribuidor',
        'estoque',
        'imagem',
        'conta_azul_id',
        'category_id',
        'peso',
        'largura',
        'altura',
        'comprimento'
    ];

    /**
     * CONFIGURAÇÃO DE SEO: Links Amigáveis
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function boot()
    {
        parent::boot();

        // Gera o slug automaticamente ao criar o produto
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->nome);
            }
        });

        // Atualiza o slug se o nome for alterado
        static::updating(function ($product) {
            if ($product->isDirty('nome')) {
                $product->slug = Str::slug($product->nome);
            }
        });
    }

    /**
     * TRATAMENTO DE STRING PARA NÚMERO
     */
    private function formatToFloat($value)
    {
        if (is_null($value) || $value === '') return 0.0;
        if (is_numeric($value)) return (float) $value;

        $clean = preg_replace('/[^\d,.]/', '', $value);
        $clean = str_replace('.', '', $clean);
        $clean = str_replace(',', '.', $clean);

        return (float) $clean;
    }

    /**
     * ACESSORS DE PREÇO (Dinâmicos)
     */
    public function getPrecoSalaoAttribute()
    {
        $margin = DB::table('settings')->where('key', 'margin_salon')->value('value') ?? 40;
        $base = $this->formatToFloat($this->preco_distribuidor);

        return round($base * (1 + ($margin / 100)), 2);
    }

    public function getPrecoConsumidorAttribute()
    {
        $margin = DB::table('settings')->where('key', 'margin_consumer')->value('value') ?? 140;
        $base = $this->formatToFloat($this->preco_distribuidor);

        return round($base * (1 + ($margin / 100)), 2);
    }

    public function getPrecoAtualAttribute()
    {
        $user = Auth::user();
        $base = $this->formatToFloat($this->preco_distribuidor);

        if (!$user) {
            return $this->getPrecoConsumidorAttribute();
        }

        return match ($user->role) {
            'distributor' => $base,
            'salon'       => $this->getPrecoSalaoAttribute(),
            'admin'       => $base,
            'consumer'    => $this->getPrecoConsumidorAttribute(),
            default       => $this->getPrecoConsumidorAttribute(),
        };
    }

    /**
     * Relacionamentos
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFichaAttribute()
    {
        // 1. Primeiro, tenta achar com o nome exato
        $ficha = \App\Models\ProductSheet::where('nome', $this->nome)->first();

        // 2. Se não achar, procura se o nome da ficha (menor) faz parte do nome do produto (maior)
        if (!$ficha) {
            $ficha = \App\Models\ProductSheet::whereRaw("? LIKE CONCAT('%', nome, '%')", [$this->nome])->first();
        }

        return $ficha;
    }

    public function sheet()
    {
        return $this->hasOne(ProductSheet::class, 'product_id');
    }
}
