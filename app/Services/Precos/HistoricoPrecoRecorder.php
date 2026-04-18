<?php

namespace App\Services\Precos;

use App\Models\HistoricoPreco;
use App\Models\Loja;
use App\Models\Preco;
use App\Models\Produto;
use Illuminate\Support\Facades\Auth;

class HistoricoPrecoRecorder
{
    public function registrarCriacao(Preco $preco): void
    {
        $preco->loadMissing(['produto', 'loja']);

        HistoricoPreco::create([
            'preco_id' => $preco->id,
            'produto_id' => $preco->produto_id,
            'produto_nome' => $preco->produto?->nome ?? 'Produto nao informado',
            'loja_id' => $preco->loja_id,
            'loja_nome' => $preco->loja?->nome ?? 'Loja nao informada',
            'user_id' => Auth::id(),
            'tipo_preco' => $preco->tipo_preco,
            'url_produto' => $preco->url_produto,
            'evento' => 'criado',
            'preco_anterior' => null,
            'preco_atual' => $preco->preco,
            'variacao_valor' => null,
            'variacao_percentual' => null,
        ]);
    }

    public function registrarAtualizacao(Preco $preco, array $snapshot): void
    {
        $preco->loadMissing(['produto', 'loja']);

        $precoAnterior = $this->decimalOuNulo($snapshot['preco'] ?? null);
        $precoAtual = $this->decimalOuNulo($preco->preco);
        $variacaoValor = $this->variacaoValor($precoAnterior, $precoAtual);
        $variacaoPercentual = $this->variacaoPercentual($precoAnterior, $precoAtual);

        HistoricoPreco::create([
            'preco_id' => $preco->id,
            'produto_id' => $preco->produto_id,
            'produto_nome' => $preco->produto?->nome ?? $this->resolverProdutoNome($snapshot['produto_id'] ?? null),
            'loja_id' => $preco->loja_id,
            'loja_nome' => $preco->loja?->nome ?? $this->resolverLojaNome($snapshot['loja_id'] ?? null),
            'user_id' => Auth::id(),
            'tipo_preco' => $preco->tipo_preco,
            'url_produto' => $preco->url_produto,
            'evento' => 'atualizado',
            'preco_anterior' => $precoAnterior,
            'preco_atual' => $precoAtual,
            'variacao_valor' => $variacaoValor,
            'variacao_percentual' => $variacaoPercentual,
        ]);
    }

    public function registrarRemocao(Preco $preco, array $snapshot): void
    {
        HistoricoPreco::create([
            'preco_id' => null,
            'produto_id' => $snapshot['produto_id'] ?? $preco->produto_id,
            'produto_nome' => $this->resolverProdutoNome($snapshot['produto_id'] ?? $preco->produto_id),
            'loja_id' => $snapshot['loja_id'] ?? $preco->loja_id,
            'loja_nome' => $this->resolverLojaNome($snapshot['loja_id'] ?? $preco->loja_id),
            'user_id' => Auth::id(),
            'tipo_preco' => $snapshot['tipo_preco'] ?? $preco->tipo_preco,
            'url_produto' => $snapshot['url_produto'] ?? $preco->url_produto,
            'evento' => 'removido',
            'preco_anterior' => $this->decimalOuNulo($snapshot['preco'] ?? $preco->preco),
            'preco_atual' => null,
            'variacao_valor' => null,
            'variacao_percentual' => null,
        ]);
    }

    private function decimalOuNulo(mixed $valor): ?float
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        return round((float) $valor, 2);
    }

    private function variacaoValor(?float $anterior, ?float $atual): ?float
    {
        if ($anterior === null || $atual === null) {
            return null;
        }

        return round($atual - $anterior, 2);
    }

    private function variacaoPercentual(?float $anterior, ?float $atual): ?float
    {
        if ($anterior === null || $atual === null || $anterior <= 0) {
            return null;
        }

        return round((($atual - $anterior) / $anterior) * 100, 2);
    }

    private function resolverProdutoNome(?int $produtoId): string
    {
        if (! $produtoId) {
            return 'Produto nao informado';
        }

        return Produto::query()->whereKey($produtoId)->value('nome') ?? 'Produto nao informado';
    }

    private function resolverLojaNome(?int $lojaId): string
    {
        if (! $lojaId) {
            return 'Loja nao informada';
        }

        return Loja::query()->whereKey($lojaId)->value('nome') ?? 'Loja nao informada';
    }
}
