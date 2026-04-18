<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Conta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait InterageComConta
{
    protected function garantirAcessoConta(Request $request, Conta $conta): void
    {
        $possuiAcesso = $request->user()
            ->contas()
            ->where('contas.id', $conta->id)
            ->wherePivot('ativo', true)
            ->exists();

        if (! $possuiAcesso) {
            throw new HttpException(403, 'Você não tem acesso a esta conta.');
        }
    }

    protected function garantirRecursoDaConta(Model $recurso, Conta $conta, string $campo = 'conta_id'): void
    {
        if ((int) $recurso->{$campo} !== (int) $conta->id) {
            throw new HttpException(404, 'Recurso não encontrado para esta conta.');
        }
    }
}
