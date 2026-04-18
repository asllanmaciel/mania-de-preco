<?php

namespace App\Http\Controllers\Web\Admin;

use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditoriaController extends AdminController
{
    public function __invoke(Request $request): View
    {
        $conta = $this->contaAtual($request);
        $area = trim((string) $request->string('area'));
        $acao = trim((string) $request->string('acao'));
        $userId = $request->integer('user_id');

        $logs = $conta->auditLogs()
            ->with('usuario')
            ->when($area !== '', fn ($query) => $query->where('area', $area))
            ->when($acao !== '', fn ($query) => $query->where('acao', $acao))
            ->when($userId > 0, fn ($query) => $query->where('user_id', $userId))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return $this->responder($request, 'admin.auditoria.index', [
            'logs' => $logs,
            'areaSelecionada' => $area,
            'acaoSelecionada' => $acao,
            'usuarioSelecionado' => $userId,
            'areas' => $conta->auditLogs()->select('area')->distinct()->orderBy('area')->pluck('area'),
            'acoes' => $conta->auditLogs()->select('acao')->distinct()->orderBy('acao')->pluck('acao'),
            'usuarios' => $conta->usuarios()->orderBy('name')->get(),
        ], $conta);
    }
}
