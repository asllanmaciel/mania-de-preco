<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimentacaoFinanceira extends Model
{
    use HasFactory;

    protected $fillable = [
        'conta_id',
        'loja_id',
        'conta_financeira_id',
        'categoria_financeira_id',
        'user_id',
        'tipo',
        'origem',
        'descricao',
        'valor',
        'data_movimentacao',
        'status',
        'observacoes',
        'metadados',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_movimentacao' => 'datetime',
        'metadados' => 'array',
    ];

    public function conta()
    {
        return $this->belongsTo(Conta::class);
    }

    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }

    public function contaFinanceira()
    {
        return $this->belongsTo(ContaFinanceira::class);
    }

    public function categoriaFinanceira()
    {
        return $this->belongsTo(CategoriaFinanceira::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
