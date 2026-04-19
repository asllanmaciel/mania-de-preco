<?php

namespace App\Http\Controllers\Web\Cliente;

use App\Http\Controllers\Controller;
use App\Support\Notificacoes\CentralNotificacoes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NotificacaoController extends Controller
{
    public function __invoke(Request $request, CentralNotificacoes $central): View
    {
        return view('cliente.notificacoes.index', [
            'user' => $request->user(),
            'notificacoes' => $central->cliente($request->user()),
        ]);
    }

    public function interagir(Request $request, CentralNotificacoes $central): RedirectResponse
    {
        $dados = $request->validate([
            'chave' => ['required', 'string', 'max:160'],
            'acao' => ['required', Rule::in(['ler', 'dispensar'])],
        ]);

        $notificacoes = $central->cliente($request->user());
        abort_unless($notificacoes->contains('chave', $dados['chave']), 404);

        $central->interagir(
            $request->user(),
            'cliente',
            'cliente',
            $dados['chave'],
            $dados['acao']
        );

        return back()->with('status', $dados['acao'] === 'dispensar'
            ? 'Notificacao dispensada por 24 horas.'
            : 'Notificacao marcada como vista.');
    }
}
