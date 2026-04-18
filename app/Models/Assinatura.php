<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assinatura extends Model
{
    use HasFactory;

    protected $fillable = [
        'conta_id',
        'plano_id',
        'status',
        'ciclo_cobranca',
        'valor',
        'inicia_em',
        'expira_em',
        'cancelada_em',
        'observacoes',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'inicia_em' => 'date',
        'expira_em' => 'date',
        'cancelada_em' => 'datetime',
    ];

    public function conta()
    {
        return $this->belongsTo(Conta::class);
    }

    public function plano()
    {
        return $this->belongsTo(Plano::class);
    }
}
