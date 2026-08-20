<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
   protected $fillable = ['user_id', 'subject', 'message', 'status', 'priority'];

public function user() {
    return $this->belongsTo(User::class);
}

public function replies() {
    return $this->hasMany(TicketReply::class);
}
public function getStatusLabelAttribute()
{
    return match ($this->status) {
        'open'        => 'Aberto',
        'in_progress' => 'Em Atendimento',
        'closed'      => 'Encerrado',
        default       => $this->status,
    };
}

/**
     * Tradução da Prioridade
     */
    public function getPriorityLabelAttribute()
    {
        return match ($this->priority) {
            'low'    => 'Baixa',
            'medium' => 'Média',
            'high'   => 'Alta',
            default  => $this->priority,
        };
    }
}
