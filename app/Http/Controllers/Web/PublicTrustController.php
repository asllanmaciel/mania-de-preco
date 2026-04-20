<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ChamadoSuporte;
use App\Notifications\ChamadoSuporteAbertoNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicTrustController extends Controller
{
    public function termos(): View
    {
        return view('institucional.termos', $this->dadosBase());
    }

    public function privacidade(): View
    {
        return view('institucional.privacidade', $this->dadosBase());
    }

    public function suporte(): View
    {
        return view('institucional.suporte', [
            ...$this->dadosBase(),
            'categoriasSuporte' => ChamadoSuporte::categoriasDisponiveis(),
            'prioridadesSuporte' => ChamadoSuporte::prioridadesDisponiveis(),
            'statusOperacional' => $this->statusOperacional(),
            'perguntasFrequentes' => $this->perguntasFrequentes(),
        ]);
    }

    public function abrirChamado(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:40'],
            'empresa' => ['nullable', 'string', 'max:255'],
            'categoria' => ['required', 'in:' . implode(',', array_keys(ChamadoSuporte::categoriasDisponiveis()))],
            'prioridade' => ['required', 'in:' . implode(',', array_keys(ChamadoSuporte::prioridadesDisponiveis()))],
            'assunto' => ['required', 'string', 'max:255'],
            'mensagem' => ['required', 'string', 'min:20', 'max:5000'],
            'origem_url' => ['nullable', 'url', 'max:255'],
            'aceite_termos' => ['accepted'],
        ]);

        $usuario = $request->user();
        $conta = $usuario?->contasAtivas()->first();

        $chamado = ChamadoSuporte::create([
            ...collect($dados)->except('aceite_termos')->all(),
            'conta_id' => $conta?->id,
            'user_id' => $usuario?->id,
            'protocolo' => $this->gerarProtocolo(),
            'status' => 'novo',
            'ip' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1024, ''),
            'termos_aceitos_em' => now(),
            'termos_versao' => config('legal.termos_versao'),
            'privacidade_versao' => config('legal.privacidade_versao'),
        ]);

        Notification::route('mail', $chamado->email)
            ->notify(new ChamadoSuporteAbertoNotification($chamado));

        return redirect()
            ->route('suporte')
            ->with('status', "Chamado {$chamado->protocolo} aberto com sucesso. Nossa equipe ja tem o contexto para priorizar o atendimento.");
    }

    private function dadosBase(): array
    {
        return [
            'ultimaAtualizacao' => config('legal.ultima_atualizacao'),
            'versaoTermos' => config('legal.termos_versao'),
            'versaoPrivacidade' => config('legal.privacidade_versao'),
            'emailSuporte' => config('mail.from.address', 'suporte@maniadepreco.com.br'),
        ];
    }

    private function gerarProtocolo(): string
    {
        do {
            $protocolo = 'MP-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (ChamadoSuporte::where('protocolo', $protocolo)->exists());

        return $protocolo;
    }

    private function statusOperacional(): array
    {
        return [
            ['nome' => 'Busca publica', 'status' => 'Operando', 'descricao' => 'Catalogo, comparativos e paginas de lojas disponiveis.'],
            ['nome' => 'Painel lojista', 'status' => 'Operando', 'descricao' => 'Admin, precos, financeiro e equipe em funcionamento.'],
            ['nome' => 'Cobranca', 'status' => 'Monitorado', 'descricao' => 'Base preparada para Asaas e Mercado Pago com webhooks auditaveis.'],
        ];
    }

    private function perguntasFrequentes(): array
    {
        return [
            [
                'pergunta' => 'Esqueci minha senha. O que faco?',
                'resposta' => 'Use a opcao de recuperacao na tela de login. Se o e-mail nao chegar, abra um chamado com o e-mail de acesso e horario da tentativa.',
            ],
            [
                'pergunta' => 'Encontrei um preco errado. Como aviso?',
                'resposta' => 'Abra um chamado em Produtos, lojas e precos com o link da pagina, nome da loja, preco exibido e preco esperado.',
            ],
            [
                'pergunta' => 'Como tratar duvidas de cobranca?',
                'resposta' => 'Informe nome da conta, status exibido na area de assinatura e qualquer link ou mensagem de cobranca que apareceu.',
            ],
            [
                'pergunta' => 'Posso pedir remocao ou revisao de dados?',
                'resposta' => 'Sim. Use a categoria Dados e privacidade e informe o e-mail vinculado ao acesso para iniciarmos a verificacao.',
            ],
        ];
    }
}
