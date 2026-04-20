<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Assinatura;
use App\Models\Conta;
use App\Models\Plano;
use App\Services\Auditoria\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContaAssinaturaController extends Controller
{
    public function __construct(private readonly AuditLogger $audit)
    {
    }

    public function create(Request $request, Conta $conta): View
    {
        return view('super-admin.assinaturas.create', [
            'user' => $request->user(),
            'conta' => $conta,
            'assinatura' => new Assinatura([
                'status' => 'trial',
                'ciclo_cobranca' => 'mensal',
                'inicia_em' => now()->toDateString(),
                'expira_em' => now()->addDays(14)->toDateString(),
            ]),
            'planos' => Plano::where('status', 'ativo')->orderBy('valor_mensal')->get(),
        ]);
    }

    public function store(Request $request, Conta $conta): RedirectResponse
    {
        $dados = $this->validar($request);
        $plano = Plano::findOrFail($dados['plano_id']);

        $this->encerrarAssinaturasAbertas($conta, null, $dados['status']);

        $assinatura = Assinatura::create([
            'conta_id' => $conta->id,
            'plano_id' => $plano->id,
            'status' => $dados['status'],
            'ciclo_cobranca' => $dados['ciclo_cobranca'],
            'valor' => $this->resolverValor($plano, $dados),
            'inicia_em' => $dados['inicia_em'],
            'expira_em' => $dados['expira_em'] ?? null,
            'cancelada_em' => $dados['cancelada_em'] ?? null,
            'observacoes' => $dados['observacoes'] ?? null,
            'billing_provider' => $dados['billing_provider'] ?? null,
        ]);

        $this->audit->registrar($request, $conta, 'assinaturas', 'assinatura_criada', "Assinatura {$plano->nome} criada para a conta.", $assinatura, [
            'plano_id' => $plano->id,
            'status' => $dados['status'],
            'ciclo_cobranca' => $dados['ciclo_cobranca'],
        ]);

        return redirect()
            ->route('super-admin.contas.show', $conta)
            ->with('status', 'Assinatura criada para a conta.');
    }

    public function edit(Request $request, Conta $conta, Assinatura $assinatura): View
    {
        abort_unless($assinatura->conta_id === $conta->id, 404);

        return view('super-admin.assinaturas.edit', [
            'user' => $request->user(),
            'conta' => $conta,
            'assinatura' => $assinatura,
            'planos' => Plano::orderBy('valor_mensal')->get(),
        ]);
    }

    public function update(Request $request, Conta $conta, Assinatura $assinatura): RedirectResponse
    {
        abort_unless($assinatura->conta_id === $conta->id, 404);

        $dados = $this->validar($request);
        $plano = Plano::findOrFail($dados['plano_id']);

        $this->encerrarAssinaturasAbertas($conta, $assinatura->id, $dados['status']);

        $antes = $assinatura->only(['plano_id', 'status', 'ciclo_cobranca', 'valor', 'expira_em']);
        $assinatura->update([
            'plano_id' => $plano->id,
            'status' => $dados['status'],
            'ciclo_cobranca' => $dados['ciclo_cobranca'],
            'valor' => $this->resolverValor($plano, $dados),
            'inicia_em' => $dados['inicia_em'],
            'expira_em' => $dados['expira_em'] ?? null,
            'cancelada_em' => $dados['cancelada_em'] ?? null,
            'observacoes' => $dados['observacoes'] ?? null,
            'billing_provider' => $dados['billing_provider'] ?? null,
        ]);

        $this->audit->registrar($request, $conta, 'assinaturas', 'assinatura_atualizada', "Assinatura da conta atualizada para {$plano->nome}.", $assinatura, [
            'antes' => $antes,
            'depois' => $assinatura->only(['plano_id', 'status', 'ciclo_cobranca', 'valor', 'expira_em']),
        ]);

        return redirect()
            ->route('super-admin.contas.show', $conta)
            ->with('status', 'Assinatura atualizada para a conta.');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'plano_id' => ['required', 'exists:planos,id'],
            'status' => ['required', 'in:trial,ativa,inadimplente,cancelada,encerrada'],
            'ciclo_cobranca' => ['required', 'in:mensal,anual'],
            'valor' => ['nullable', 'numeric', 'min:0'],
            'inicia_em' => ['required', 'date'],
            'expira_em' => ['nullable', 'date'],
            'cancelada_em' => ['nullable', 'date'],
            'observacoes' => ['nullable', 'string'],
            'billing_provider' => ['nullable', Rule::in(['asaas'])],
        ]);
    }

    private function resolverValor(Plano $plano, array $dados): float
    {
        if (array_key_exists('valor', $dados) && $dados['valor'] !== null) {
            return (float) $dados['valor'];
        }

        return $dados['ciclo_cobranca'] === 'anual'
            ? (float) $plano->valor_anual
            : (float) $plano->valor_mensal;
    }

    private function encerrarAssinaturasAbertas(Conta $conta, ?int $ignorarId, string $novoStatus): void
    {
        if (! in_array($novoStatus, ['trial', 'ativa', 'inadimplente'], true)) {
            return;
        }

        $conta->assinaturas()
            ->when($ignorarId, fn ($query) => $query->whereKeyNot($ignorarId))
            ->whereIn('status', ['trial', 'ativa', 'inadimplente'])
            ->get()
            ->each(function (Assinatura $assinatura): void {
                $assinatura->forceFill([
                    'status' => 'encerrada',
                    'cancelada_em' => now(),
                ])->saveQuietly();
            });
    }
}
