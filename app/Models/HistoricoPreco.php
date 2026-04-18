<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoPreco extends Model
{
    use HasFactory;

    protected $table = 'historicos_precos';

    protected $fillable = [
        'preco_id',
        'produto_id',
        'produto_nome',
        'loja_id',
        'loja_nome',
        'user_id',
        'tipo_preco',
        'url_produto',
        'evento',
        'preco_anterior',
        'preco_atual',
        'variacao_valor',
        'variacao_percentual',
    ];

    protected $casts = [
        'preco_anterior' => 'decimal:2',
        'preco_atual' => 'decimal:2',
        'variacao_valor' => 'decimal:2',
        'variacao_percentual' => 'decimal:2',
    ];

    public function preco()
    {
        return $this->belongsTo(Preco::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
