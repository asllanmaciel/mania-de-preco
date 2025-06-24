<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'slug',
        'descricao',
    ];

    /**
     * Relacionamento: Uma categoria tem muitos produtos.
     */
    public function produtos()
    {
        return $this->hasMany(Produto::class);
    }
}
