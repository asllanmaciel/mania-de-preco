<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'conta_id',
        'user_id',
        'area',
        'acao',
        'entidade_tipo',
        'entidade_id',
        'descricao',
        'metadados',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'metadados' => 'array',
    ];

    public function conta()
    {
        return $this->belongsTo(Conta::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
