<?php

namespace App\Models;

use App\Services\Precos\AlertaPrecoEvaluator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertaPreco extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::created(function (AlertaPreco $alerta) {
            if ($alerta->status === 'inativo') {
                return;
            }

            app(AlertaPrecoEvaluator::class)->avaliar($alerta);
        });

        static::updated(function (AlertaPreco $alerta) {
            if ($alerta->wasChanged([
                'preco_base',
                'ultimo_preco_menor',
                'menor_preco_historico',
                'variacao_desde_ativacao',
                'variacao_percentual_desde_ativacao',
                'loja_id_referencia',
                'disparado_em',
                'ultima_avaliacao_em',
            ])) {
                return;
            }

            if ($alerta->status === 'inativo') {
                return;
            }

            app(AlertaPrecoEvaluator::class)->avaliar($alerta);
        });
    }

    protected $table = 'alertas_precos';

    protected $fillable = [
        'user_id',
        'produto_id',
        'loja_id_referencia',
        'preco_desejado',
        'preco_base',
        'ultimo_preco_menor',
        'menor_preco_historico',
        'variacao_desde_ativacao',
        'variacao_percentual_desde_ativacao',
        'disparado_em',
        'ultima_avaliacao_em',
        'status',
    ];

    protected $casts = [
        'preco_desejado' => 'decimal:2',
        'preco_base' => 'decimal:2',
        'ultimo_preco_menor' => 'decimal:2',
        'menor_preco_historico' => 'decimal:2',
        'variacao_desde_ativacao' => 'decimal:2',
        'variacao_percentual_desde_ativacao' => 'decimal:2',
        'disparado_em' => 'datetime',
        'ultima_avaliacao_em' => 'datetime',
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function lojaReferencia()
    {
        return $this->belongsTo(Loja::class, 'loja_id_referencia');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
