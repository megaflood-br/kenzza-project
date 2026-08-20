<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadDistribuidor extends Model
{
    // Esta linha abaixo resolve o erro de MassAssignment
    protected $fillable = [
        'nome',
        'whatsapp',
        'email',
        'cidade',
        'estado'
    ];
}
