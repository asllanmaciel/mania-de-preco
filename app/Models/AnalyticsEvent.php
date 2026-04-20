<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AnalyticsEvent extends Model
{
    protected $fillable = [
        'user_id',
        'conta_id',
        'evento',
        'area',
        'sujeito_type',
        'sujeito_id',
        'metadata',
        'ip',
        'user_agent',
        'ocorreu_em',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'ocorreu_em' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function conta(): BelongsTo
    {
        return $this->belongsTo(Conta::class);
    }

    public function sujeito(): MorphTo
    {
        return $this->morphTo();
    }
}
