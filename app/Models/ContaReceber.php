<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContaReceber extends Model
{
    use HasFactory;

    protected $table = 'contas_receber';

    protected $fillable = [
        'conta_id',
        'loja_id',
        'categoria_financeira_id',
        'cliente_nome',
        'descricao',
        'valor_total',
        'valor_recebido',
        'vencimento',
        'recebimento_previsto_em',
        'recebido_em',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'valor_total' => 'decimal:2',
        'valor_recebido' => 'decimal:2',
        'vencimento' => 'date',
        'recebimento_previsto_em' => 'date',
        'recebido_em' => 'datetime',
    ];

    public function conta()
    {
        return $this->belongsTo(Conta::class);
    }

    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }

    public function categoriaFinanceira()
    {
        return $this->belongsTo(CategoriaFinanceira::class);
    }
}
