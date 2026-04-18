<?php

namespace App\Support\Billing;

use App\Models\Assinatura;
use App\Models\Conta;
use App\Models\Preco;
use App\Models\Produto;

class ContaUsageMeter
{
    private const RECURSOS = [
        'usuarios' => [
            'campo' => 'limite_usuarios',
            'rotulo' => 'usuarios',
            'singular' => 'usuario',
        ],
        'lojas' => [
            'campo' => 'limite_lojas',
            'rotulo' => 'lojas',
            'singular' => 'loja',
        ],
        'produtos' => [
            'campo' => 'limite_produtos',
            'rotulo' => 'produtos',
            'singular' => 'produto',
        ],
    ];

    public function resumo(Conta $conta): array
    {
        $assinatura = $this->assinaturaAtual($conta);
        $plano = $assinatura?->plano;

        return [
            'assinatura' => $assinatura,
            'plano' => $plano,
            'metricas' => collect(array_keys(self::RECURSOS))
                ->mapWithKeys(fn (string $recurso) => [$recurso => $this->metrica($conta, $recurso, $assinatura)])
                ->all(),
        ];
    }

    public function atingiuLimite(Conta $conta, string $recurso): bool
    {
        $metrica = $this->metrica($conta, $recurso);

        return ! $metrica['ilimitado'] && $metrica['usado'] >= $metrica['limite'];
    }

    public function podeAdicionar(Conta $conta, string $recurso, int $quantidade = 1): bool
    {
        $metrica = $this->metrica($conta, $recurso);

        if ($metrica['ilimitado']) {
            return true;
        }

        return ($metrica['usado'] + $quantidade) <= $metrica['limite'];
    }

    public function podeVincularProduto(Conta $conta, int $produtoId, ?Preco $precoAtual = null): bool
    {
        $limite = $this->limite($conta, 'produtos');

        if ($limite === null) {
            return true;
        }

        $produtosVinculados = $this->produtosVinculadosQuery($conta)
            ->when($precoAtual, fn ($query) => $query->where('precos.id', '!=', $precoAtual->id))
            ->distinct()
            ->pluck('precos.produto_id');

        if ($produtosVinculados->contains($produtoId)) {
            return true;
        }

        return $produtosVinculados->count() + 1 <= $limite;
    }

    public function mensagemBloqueio(Conta $conta, string $recurso): string
    {
        $metrica = $this->metrica($conta, $recurso);
        $plano = $metrica['plano_nome'];
        $rotulo = self::RECURSOS[$recurso]['rotulo'] ?? $recurso;

        if ($metrica['ilimitado']) {
            return 'Este recurso nao possui limite operacional no plano atual.';
        }

        return "O plano {$plano} permite ate {$metrica['limite']} {$rotulo}. Para continuar crescendo, ajuste o plano da conta no super admin.";
    }

    public function metrica(Conta $conta, string $recurso, ?Assinatura $assinatura = null): array
    {
        $assinatura ??= $this->assinaturaAtual($conta);
        $limite = $this->limite($conta, $recurso, $assinatura);
        $usado = $this->uso($conta, $recurso);
        $percentual = $limite ? min(100, (int) round(($usado / $limite) * 100)) : 0;

        return [
            'chave' => $recurso,
            'rotulo' => self::RECURSOS[$recurso]['rotulo'] ?? $recurso,
            'singular' => self::RECURSOS[$recurso]['singular'] ?? $recurso,
            'usado' => $usado,
            'limite' => $limite,
            'ilimitado' => $limite === null,
            'percentual' => $percentual,
            'disponivel' => $limite === null ? null : max(0, $limite - $usado),
            'em_alerta' => $limite !== null && $percentual >= 80 && $usado < $limite,
            'excedido' => $limite !== null && $usado >= $limite,
            'plano_nome' => $assinatura?->plano?->nome ?? 'sem plano',
        ];
    }

    private function limite(Conta $conta, string $recurso, ?Assinatura $assinatura = null): ?int
    {
        $campo = self::RECURSOS[$recurso]['campo'] ?? null;

        if (! $campo) {
            return null;
        }

        $assinatura ??= $this->assinaturaAtual($conta);
        $limite = (int) ($assinatura?->plano?->{$campo} ?? 0);

        return $limite > 0 ? $limite : null;
    }

    private function uso(Conta $conta, string $recurso): int
    {
        return match ($recurso) {
            'usuarios' => $conta->usuarios()->wherePivot('ativo', true)->count(),
            'lojas' => $conta->lojas()->count(),
            'produtos' => Produto::query()
                ->whereHas('precos.loja', fn ($query) => $query->where('conta_id', $conta->id))
                ->count(),
            default => 0,
        };
    }

    private function assinaturaAtual(Conta $conta): ?Assinatura
    {
        if ($conta->relationLoaded('assinaturas')) {
            return $conta->assinaturas
                ->loadMissing('plano')
                ->sortByDesc('id')
                ->first();
        }

        return $conta->assinaturas()
            ->with('plano')
            ->latest('id')
            ->first();
    }

    private function produtosVinculadosQuery(Conta $conta)
    {
        return Preco::query()
            ->whereHas('loja', fn ($query) => $query->where('conta_id', $conta->id));
    }
}
