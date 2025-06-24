<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loja extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome', 'cnpj', 'telefone', 'whatsapp', 'email',
        'site', 'instagram', 'facebook', 'endereco', 'numero',
        'bairro', 'cidade', 'uf', 'cep', 'latitude', 'longitude',
        'tipo_loja', 'status', 'logo'
    ];

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
}
