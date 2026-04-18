<?php

namespace App\Services\Precos;

use App\Models\AlertaPreco;
use App\Models\Preco;

class AlertaPrecoEvaluator
{
    public function avaliar(AlertaPreco $alerta): AlertaPreco
    {
        $alerta->loadMissing(['produto', 'lojaReferencia']);

        if ($alerta->status === 'inativo') {
            return $alerta;
        }

        $melhorOferta = Preco::query()
            ->where('produto_id', $alerta->produto_id)
            ->whereHas('loja', fn ($query) => $query->where('status', 'ativo'))
            ->with('loja')
            ->orderBy('preco')
            ->first();

        $ultimoPreco = $melhorOferta ? round((float) $melhorOferta->preco, 2) : null;
        $precoBase = $alerta->preco_base !== null
            ? round((float) $alerta->preco_base, 2)
            : $ultimoPreco;
        $menorHistorico = $this->menorHistorico($alerta, $ultimoPreco);
        $variacaoValor = $this->variacaoValor($precoBase, $ultimoPreco);
        $variacaoPercentual = $this->variacaoPercentual($precoBase, $ultimoPreco);
        $novoStatus = $ultimoPreco !== null && $ultimoPreco <= (float) $alerta->preco_desejado
            ? 'atendido'
            : 'ativo';

        $disparadoEm = $alerta->disparado_em;

        if ($novoStatus === 'atendido' && $alerta->status !== 'atendido') {
            $disparadoEm = now();
        }

        $alerta->forceFill([
            'loja_id_referencia' => $melhorOferta?->loja_id,
            'preco_base' => $precoBase,
            'ultimo_preco_menor' => $ultimoPreco,
            'menor_preco_historico' => $menorHistorico,
            'variacao_desde_ativacao' => $variacaoValor,
            'variacao_percentual_desde_ativacao' => $variacaoPercentual,
            'disparado_em' => $disparadoEm,
            'ultima_avaliacao_em' => now(),
            'status' => $novoStatus,
        ])->saveQuietly();

        return $alerta->fresh(['produto', 'user', 'lojaReferencia']);
    }

    public function avaliarProduto(int $produtoId): void
    {
        AlertaPreco::query()
            ->where('produto_id', $produtoId)
            ->whereIn('status', ['ativo', 'atendido'])
            ->get()
            ->each(fn (AlertaPreco $alerta) => $this->avaliar($alerta));
    }

    private function menorHistorico(AlertaPreco $alerta, ?float $ultimoPreco): ?float
    {
        $menorAtual = $alerta->menor_preco_historico !== null
            ? round((float) $alerta->menor_preco_historico, 2)
            : null;

        if ($ultimoPreco === null) {
            return $menorAtual ?? ($alerta->preco_base !== null ? round((float) $alerta->preco_base, 2) : null);
        }

        if ($menorAtual === null) {
            return $ultimoPreco;
        }

        return min($menorAtual, $ultimoPreco);
    }

    private function variacaoValor(?float $precoBase, ?float $ultimoPreco): ?float
    {
        if ($precoBase === null || $ultimoPreco === null) {
            return null;
        }

        return round($ultimoPreco - $precoBase, 2);
    }

    private function variacaoPercentual(?float $precoBase, ?float $ultimoPreco): ?float
    {
        if ($precoBase === null || $ultimoPreco === null || $precoBase <= 0) {
            return null;
        }

        return round((($ultimoPreco - $precoBase) / $precoBase) * 100, 2);
    }
}
