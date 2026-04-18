<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContaFinanceira extends Model
{
    use HasFactory;

    protected $table = 'contas_financeiras';

    protected $fillable = [
        'conta_id',
        'loja_id',
        'nome',
        'tipo',
        'instituicao',
        'agencia',
        'numero',
        'saldo_inicial',
        'saldo_atual',
        'ativa',
    ];

    protected $casts = [
        'saldo_inicial' => 'decimal:2',
        'saldo_atual' => 'decimal:2',
        'ativa' => 'boolean',
    ];

    public function conta()
    {
        return $this->belongsTo(Conta::class);
    }

    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }

    public function movimentacoes()
    {
        return $this->hasMany(MovimentacaoFinanceira::class);
    }
}
