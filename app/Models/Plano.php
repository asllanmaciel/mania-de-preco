<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plano extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'valor_mensal',
        'valor_anual',
        'limite_usuarios',
        'limite_lojas',
        'limite_produtos',
        'recursos',
        'status',
    ];

    protected $casts = [
        'valor_mensal' => 'decimal:2',
        'valor_anual' => 'decimal:2',
        'recursos' => 'array',
    ];

    public function assinaturas()
    {
        return $this->hasMany(Assinatura::class);
    }
}
