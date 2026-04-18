<?php

namespace App\Services\Auditoria;

use App\Models\AuditLog;
use App\Models\Conta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditLogger
{
    public function registrar(
        Request $request,
        ?Conta $conta,
        string $area,
        string $acao,
        string $descricao,
        ?Model $entidade = null,
        array $metadados = []
    ): AuditLog {
        return AuditLog::create([
            'conta_id' => $conta?->id,
            'user_id' => $request->user()?->id,
            'area' => $area,
            'acao' => $acao,
            'entidade_tipo' => $entidade ? $entidade::class : null,
            'entidade_id' => $entidade?->getKey(),
            'descricao' => $descricao,
            'metadados' => $metadados ?: null,
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 1024),
        ]);
    }
}
