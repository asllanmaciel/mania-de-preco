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
        'billing_provider',
        'billing_subscription_id',
        'billing_checkout_url',
        'billing_status',
        'billing_last_synced_at',
        'billing_payload',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'inicia_em' => 'date',
        'expira_em' => 'date',
        'cancelada_em' => 'datetime',
        'billing_last_synced_at' => 'datetime',
        'billing_payload' => 'array',
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
