<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContaPagar extends Model
{
    use HasFactory;

    protected $table = 'contas_pagar';

    protected $fillable = [
        'conta_id',
        'loja_id',
        'categoria_financeira_id',
        'fornecedor_nome',
        'descricao',
        'valor_total',
        'valor_pago',
        'vencimento',
        'pagamento_previsto_em',
        'pago_em',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'valor_total' => 'decimal:2',
        'valor_pago' => 'decimal:2',
        'vencimento' => 'date',
        'pagamento_previsto_em' => 'date',
        'pago_em' => 'datetime',
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
