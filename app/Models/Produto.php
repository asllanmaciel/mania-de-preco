<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome', 'slug', 'categoria_id', 'marca_id',
        'descricao', 'especificacoes', 'imagem_principal', 'status'
    ];

    protected $casts = [
        'especificacoes' => 'array',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function precos()
    {
        return $this->hasMany(Preco::class);
    }
}
