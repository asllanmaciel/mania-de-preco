<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaFinanceira extends Model
{
    use HasFactory;

    protected $table = 'categorias_financeiras';

    protected $fillable = [
        'conta_id',
        'nome',
        'slug',
        'tipo',
        'cor',
        'icone',
        'descricao',
        'ativa',
    ];

    protected $casts = [
        'ativa' => 'boolean',
    ];

    public function conta()
    {
        return $this->belongsTo(Conta::class);
    }

    public function movimentacoes()
    {
        return $this->hasMany(MovimentacaoFinanceira::class);
    }

    public function contasPagar()
    {
        return $this->hasMany(ContaPagar::class);
    }

    public function contasReceber()
    {
        return $this->hasMany(ContaReceber::class);
    }
}
