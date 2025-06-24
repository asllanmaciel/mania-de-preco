<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanoAssinatura extends Model
{
    use HasFactory;

    protected $fillable = [
        'loja_id', 'nome_plano', 'valor', 'validade', 'status'
    ];

    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }
}
