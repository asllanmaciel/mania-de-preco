<?php

namespace App\Http\Controllers\Web\Admin;

use App\Support\Notificacoes\CentralNotificacoes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NotificacaoController extends AdminController
{
    public function __invoke(Request $request, CentralNotificacoes $central): View
    {
        $conta = $this->contaAtual($request);
        $capacidades = $request->user()->capacidadesNaConta($conta);

        return $this->responder($request, 'admin.notificacoes.index', [
            'notificacoes' => $central->admin($conta, $request->user(), $capacidades),
        ], $conta);
    }

    public function interagir(Request $request, CentralNotificacoes $central): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $capacidades = $request->user()->capacidadesNaConta($conta);
        $dados = $request->validate([
            'chave' => ['required', 'string', 'max:160'],
            'acao' => ['required', Rule::in(['ler', 'dispensar'])],
        ]);

        $notificacoes = $central->admin($conta, $request->user(), $capacidades);
        abort_unless($notificacoes->contains('chave', $dados['chave']), 404);

        $central->interagir(
            $request->user(),
            'admin',
            'conta:' . $conta->id,
            $dados['chave'],
            $dados['acao'],
            $conta->id
        );

        return back()->with('status', $dados['acao'] === 'dispensar'
            ? 'Notificacao dispensada por 24 horas.'
            : 'Notificacao marcada como vista.');
    }
}
