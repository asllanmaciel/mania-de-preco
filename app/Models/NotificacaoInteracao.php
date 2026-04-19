<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificacaoInteracao extends Model
{
    protected $table = 'notificacao_interacoes';

    protected $fillable = [
        'user_id',
        'conta_id',
        'contexto',
        'escopo',
        'chave',
        'lida_em',
        'dispensada_ate',
    ];

    protected $casts = [
        'lida_em' => 'datetime',
        'dispensada_ate' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function conta()
    {
        return $this->belongsTo(Conta::class);
    }
}
