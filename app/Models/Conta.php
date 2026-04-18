<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conta extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome_fantasia',
        'slug',
        'razao_social',
        'documento',
        'email',
        'telefone',
        'status',
        'trial_ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'conta_user')
            ->withPivot(['papel', 'ativo', 'ultimo_acesso_em'])
            ->withTimestamps();
    }

    public function lojas()
    {
        return $this->hasMany(Loja::class);
    }

    public function categoriasFinanceiras()
    {
        return $this->hasMany(CategoriaFinanceira::class);
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

    public function assinaturas()
    {
        return $this->hasMany(Assinatura::class);
    }
}
