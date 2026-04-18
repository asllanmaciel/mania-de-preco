<?php

namespace App\Services\Financeiro;

use App\Models\ContaFinanceira;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use App\Models\MovimentacaoFinanceira;

class TituloFinanceiroSynchronizer
{
    public function syncContaPagar(ContaPagar $titulo, ?int $userId = null): void
    {
        $titulo->loadMissing('movimentacaoFinanceira');

        $contasAfetadas = array_filter([
            $titulo->conta_financeira_id,
            $titulo->movimentacaoFinanceira?->conta_financeira_id,
        ]);

        if ($titulo->status !== 'paga' || ! $titulo->conta_financeira_id) {
            $this->removerMovimentacaoVinculada($titulo, $contasAfetadas);
            return;
        }

        $movimentacao = $titulo->movimentacaoFinanceira ?? new MovimentacaoFinanceira();

        $movimentacao->fill([
            'conta_id' => $titulo->conta_id,
            'loja_id' => $titulo->loja_id,
            'conta_financeira_id' => $titulo->conta_financeira_id,
            'categoria_financeira_id' => $titulo->categoria_financeira_id,
            'user_id' => $userId ?? $movimentacao->user_id,
            'tipo' => 'despesa',
            'origem' => 'pagamento',
            'descricao' => $titulo->descricao,
            'valor' => $titulo->valor_total,
            'data_movimentacao' => $titulo->pago_em ?? $titulo->pagamento_previsto_em ?? $titulo->vencimento ?? now(),
            'status' => 'realizada',
            'observacoes' => $titulo->observacoes,
            'metadados' => [
                'origem_titulo' => 'conta_pagar',
                'titulo_id' => $titulo->id,
            ],
        ]);

        $movimentacao->save();

        if ((int) $titulo->movimentacao_financeira_id !== (int) $movimentacao->id) {
            $titulo->forceFill([
                'movimentacao_financeira_id' => $movimentacao->id,
            ])->saveQuietly();
        }

        $this->recalcularSaldos(array_merge($contasAfetadas, [$movimentacao->conta_financeira_id]));
    }

    public function syncContaReceber(ContaReceber $titulo, ?int $userId = null): void
    {
        $titulo->loadMissing('movimentacaoFinanceira');

        $contasAfetadas = array_filter([
            $titulo->conta_financeira_id,
            $titulo->movimentacaoFinanceira?->conta_financeira_id,
        ]);

        if ($titulo->status !== 'recebida' || ! $titulo->conta_financeira_id) {
            $this->removerMovimentacaoVinculada($titulo, $contasAfetadas);
            return;
        }

        $movimentacao = $titulo->movimentacaoFinanceira ?? new MovimentacaoFinanceira();

        $movimentacao->fill([
            'conta_id' => $titulo->conta_id,
            'loja_id' => $titulo->loja_id,
            'conta_financeira_id' => $titulo->conta_financeira_id,
            'categoria_financeira_id' => $titulo->categoria_financeira_id,
            'user_id' => $userId ?? $movimentacao->user_id,
            'tipo' => 'receita',
            'origem' => 'pagamento',
            'descricao' => $titulo->descricao,
            'valor' => $titulo->valor_total,
            'data_movimentacao' => $titulo->recebido_em ?? $titulo->recebimento_previsto_em ?? $titulo->vencimento ?? now(),
            'status' => 'realizada',
            'observacoes' => $titulo->observacoes,
            'metadados' => [
                'origem_titulo' => 'conta_receber',
                'titulo_id' => $titulo->id,
            ],
        ]);

        $movimentacao->save();

        if ((int) $titulo->movimentacao_financeira_id !== (int) $movimentacao->id) {
            $titulo->forceFill([
                'movimentacao_financeira_id' => $movimentacao->id,
            ])->saveQuietly();
        }

        $this->recalcularSaldos(array_merge($contasAfetadas, [$movimentacao->conta_financeira_id]));
    }

    public function removeContaPagar(ContaPagar $titulo): void
    {
        $titulo->loadMissing('movimentacaoFinanceira');

        $this->removerMovimentacaoVinculada($titulo, [
            $titulo->conta_financeira_id,
            $titulo->movimentacaoFinanceira?->conta_financeira_id,
        ]);
    }

    public function removeContaReceber(ContaReceber $titulo): void
    {
        $titulo->loadMissing('movimentacaoFinanceira');

        $this->removerMovimentacaoVinculada($titulo, [
            $titulo->conta_financeira_id,
            $titulo->movimentacaoFinanceira?->conta_financeira_id,
        ]);
    }

    private function removerMovimentacaoVinculada(ContaPagar|ContaReceber $titulo, array $contasAfetadas): void
    {
        $movimentacao = $titulo->movimentacaoFinanceira;

        if ($movimentacao) {
            $contasAfetadas[] = $movimentacao->conta_financeira_id;
            $movimentacao->delete();
        }

        if ($titulo->movimentacao_financeira_id) {
            $titulo->forceFill([
                'movimentacao_financeira_id' => null,
            ])->saveQuietly();
        }

        $this->recalcularSaldos($contasAfetadas);
    }

    private function recalcularSaldos(array $contaIds): void
    {
        $ids = collect($contaIds)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        foreach ($ids as $contaId) {
            $contaFinanceira = ContaFinanceira::find($contaId);

            if (! $contaFinanceira) {
                continue;
            }

            $receitas = $contaFinanceira->movimentacoes()
                ->where('status', 'realizada')
                ->where('tipo', 'receita')
                ->sum('valor');

            $despesas = $contaFinanceira->movimentacoes()
                ->where('status', 'realizada')
                ->where('tipo', 'despesa')
                ->sum('valor');

            $contaFinanceira->forceFill([
                'saldo_atual' => $contaFinanceira->saldo_inicial + $receitas - $despesas,
            ])->saveQuietly();
        }
    }
}
