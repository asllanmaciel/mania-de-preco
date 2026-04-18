<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loja extends Model
{
    use HasFactory;

    protected $fillable = [
        'conta_id', 'nome', 'cnpj', 'telefone', 'whatsapp', 'email',
        'site', 'instagram', 'facebook', 'endereco', 'numero',
        'bairro', 'cidade', 'uf', 'cep', 'latitude', 'longitude',
        'tipo_loja', 'status', 'logo'
    ];

    public function conta()
    {
        return $this->belongsTo(Conta::class);
    }

    public function precos()
    {
        return $this->hasMany(Preco::class);
    }

    public function avaliacoes()
    {
        return $this->hasMany(AvaliacaoLoja::class);
    }

    public function plano()
    {
        return $this->hasOne(PlanoAssinatura::class);
    }

    public function contasFinanceiras()
    {
        return $this->hasMany(ContaFinanceira::class);
    }

    public function movimentacoesFinanceiras()
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
