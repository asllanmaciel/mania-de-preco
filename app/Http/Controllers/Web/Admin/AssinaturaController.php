<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\Plano;
use App\Support\Billing\ContaUsageMeter;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssinaturaController extends AdminController
{
    public function __invoke(Request $request, ContaUsageMeter $usageMeter): View
    {
        $conta = $this->contaAtual($request);
        $usoPlano = $usageMeter->resumo($conta);
        $assinaturaAtual = $usoPlano['assinatura'];

        $historicoAssinaturas = $conta->assinaturas()
            ->with('plano')
            ->latest('id')
            ->take(6)
            ->get();

        $planosDisponiveis = Plano::query()
            ->where('status', 'ativo')
            ->orderBy('valor_mensal')
            ->get();

        return $this->responder($request, 'admin.assinatura.show', [
            'assinaturaAtual' => $assinaturaAtual,
            'usoPlano' => $usoPlano,
            'historicoAssinaturas' => $historicoAssinaturas,
            'planosDisponiveis' => $planosDisponiveis,
            'mensagemStatus' => $this->mensagemStatus($assinaturaAtual),
        ], $conta);
    }

    private function mensagemStatus($assinatura): array
    {
        if (! $assinatura) {
            return [
                'tipo' => 'alerta',
                'titulo' => 'Assinatura ainda nao configurada',
                'descricao' => 'A conta ainda nao possui assinatura ativa. O super admin precisa vincular um plano para liberar uma leitura comercial completa.',
            ];
        }

        return match ($assinatura->status) {
            'ativa' => [
                'tipo' => 'positivo',
                'titulo' => 'Assinatura ativa',
                'descricao' => 'O plano esta ativo e a conta pode continuar operando dentro dos limites contratados.',
            ],
            'trial' => [
                'tipo' => 'alerta',
                'titulo' => 'Periodo de teste em andamento',
                'descricao' => 'A conta esta em avaliacao. Acompanhe os limites e conclua a assinatura antes do fim do periodo de teste.',
            ],
            'inadimplente' => [
                'tipo' => 'risco',
                'titulo' => 'Pagamento pendente',
                'descricao' => 'Existe uma pendencia de cobranca. Regularize para evitar bloqueios comerciais e interrupcao da operacao.',
            ],
            'cancelada', 'encerrada' => [
                'tipo' => 'risco',
                'titulo' => 'Assinatura sem vigencia ativa',
                'descricao' => 'A assinatura nao esta ativa. Fale com o suporte para reativar o plano ou contratar uma nova condicao.',
            ],
            default => [
                'tipo' => 'alerta',
                'titulo' => 'Status comercial em analise',
                'descricao' => 'A assinatura possui um status que precisa de acompanhamento do time responsavel.',
            ],
        };
    }
}
