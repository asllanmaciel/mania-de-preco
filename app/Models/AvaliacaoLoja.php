<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvaliacaoLoja extends Model
{
    use HasFactory;

    protected $fillable = [
        'loja_id', 'user_id', 'nota', 'comentario'
    ];

    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
