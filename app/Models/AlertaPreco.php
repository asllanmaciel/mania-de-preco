<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertaPreco extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'produto_id', 'preco_desejado', 'status'
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
