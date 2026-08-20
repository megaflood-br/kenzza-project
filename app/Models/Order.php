<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\OrderObserver;

#[ObservedBy([OrderObserver::class])]
class Order extends Model
{
    // Adicionamos os campos de logística aqui na lista de permissões
    protected $fillable = [
    'user_id',
    'total',
    'metodo_pagamento',
    'status',
    'codigo_rastreio',
    'external_id',
    'frete',
    'shipping_service_id',
    'url_etiqueta',
    'melhor_envio_id',
    'base_order_id',
    'origin',
    'marketplace_order_id',
    'comissao_gerente', // <--- ADICIONE ESTA LINHA
];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
