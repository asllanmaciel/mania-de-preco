<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\Conta;
use App\Services\Auditoria\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ConfiguracaoContaController extends AdminController
{
    public function __construct(private readonly AuditLogger $audit)
    {
    }

    public function edit(Request $request): View
    {
        $conta = $this->contaAtual($request);

        return $this->responder($request, 'admin.configuracoes.edit', [
            'contaConfiguracao' => $conta,
            'preferencias' => $this->preferenciasAtuais($conta),
            'segmentos' => $this->segmentos(),
            'portes' => $this->portes(),
        ], $conta);
    }

    public function update(Request $request): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $dados = $this->validar($request, $conta);

        $antes = $conta->only([
            'nome_fantasia',
            'razao_social',
            'documento',
            'email',
            'telefone',
            'site',
            'instagram',
            'segmento',
            'porte',
            'cidade',
            'uf',
            'timezone',
        ]);

        $preferencias = [
            'canal_suporte' => $dados['canal_suporte'] ?? null,
            'frequencia_relatorio' => $dados['frequencia_relatorio'] ?? null,
            'receber_alertas_operacionais' => $request->boolean('receber_alertas_operacionais'),
        ];

        $conta->update([
            'nome_fantasia' => $dados['nome_fantasia'],
            'razao_social' => $dados['razao_social'] ?? null,
            'documento' => $dados['documento'] ?? null,
            'email' => $dados['email'] ?? null,
            'telefone' => $dados['telefone'] ?? null,
            'site' => $dados['site'] ?? null,
            'instagram' => $dados['instagram'] ?? null,
            'segmento' => $dados['segmento'] ?? null,
            'porte' => $dados['porte'] ?? null,
            'endereco' => $dados['endereco'] ?? null,
            'numero' => $dados['numero'] ?? null,
            'bairro' => $dados['bairro'] ?? null,
            'cidade' => $dados['cidade'] ?? null,
            'uf' => ! empty($dados['uf']) ? strtoupper($dados['uf']) : null,
            'cep' => $dados['cep'] ?? null,
            'logo' => $dados['logo'] ?? null,
            'cor_marca' => $dados['cor_marca'] ?? null,
            'descricao_publica' => $dados['descricao_publica'] ?? null,
            'timezone' => $dados['timezone'] ?? null,
            'preferencias' => $preferencias,
        ]);

        $this->audit->registrar($request, $conta, 'configuracoes', 'conta_atualizada', 'Configuracoes da conta atualizadas.', $conta, [
            'antes' => $antes,
            'depois' => $conta->only(array_keys($antes)),
        ]);

        return redirect()
            ->route('admin.configuracoes.edit')
            ->with('status', 'Configuracoes da conta atualizadas com sucesso.');
    }

    private function validar(Request $request, Conta $conta): array
    {
        return $request->validate([
            'nome_fantasia' => ['required', 'string', 'max:255'],
            'razao_social' => ['nullable', 'string', 'max:255'],
            'documento' => ['nullable', 'string', 'max:30', Rule::unique('contas', 'documento')->ignore($conta->id)],
            'email' => ['nullable', 'email', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:30'],
            'site' => ['nullable', 'url', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'segmento' => ['nullable', Rule::in(array_keys($this->segmentos()))],
            'porte' => ['nullable', Rule::in(array_keys($this->portes()))],
            'endereco' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:50'],
            'bairro' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:255'],
            'uf' => ['nullable', 'string', 'size:2'],
            'cep' => ['nullable', 'string', 'max:20'],
            'logo' => ['nullable', 'string', 'max:255'],
            'cor_marca' => ['nullable', 'string', 'max:20'],
            'descricao_publica' => ['nullable', 'string', 'max:1200'],
            'timezone' => ['nullable', 'string', 'max:80'],
            'canal_suporte' => ['nullable', Rule::in(['email', 'whatsapp', 'telefone'])],
            'frequencia_relatorio' => ['nullable', Rule::in(['diaria', 'semanal', 'mensal'])],
            'receber_alertas_operacionais' => ['nullable', 'boolean'],
        ]);
    }

    private function preferenciasAtuais(Conta $conta): array
    {
        return array_merge([
            'canal_suporte' => 'whatsapp',
            'frequencia_relatorio' => 'semanal',
            'receber_alertas_operacionais' => true,
        ], $conta->preferencias ?? []);
    }

    private function segmentos(): array
    {
        return [
            'mercado' => 'Mercado e mercearia',
            'farmacia' => 'Farmacia e saude',
            'pet' => 'Pet shop',
            'conveniencia' => 'Conveniencia',
            'moda' => 'Moda e acessorios',
            'outros' => 'Outros segmentos',
        ];
    }

    private function portes(): array
    {
        return [
            'solo' => 'Operacao solo',
            'pequena' => 'Pequena empresa',
            'media' => 'Media empresa',
            'rede' => 'Rede ou multiunidade',
        ];
    }
}
