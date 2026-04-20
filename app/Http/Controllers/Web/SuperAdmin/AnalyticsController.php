<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\Loja;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function __invoke(Request $request): View
    {
        $periodo = in_array((int) $request->integer('periodo', 7), [7, 30, 90], true)
            ? (int) $request->integer('periodo', 7)
            : 7;

        $inicio = now()->subDays($periodo - 1)->startOfDay();

        $eventos = AnalyticsEvent::query()
            ->with(['usuario', 'conta'])
            ->where('ocorreu_em', '>=', $inicio)
            ->latest('ocorreu_em')
            ->get();

        $eventosPorTipo = $eventos
            ->groupBy('evento')
            ->map(fn (Collection $grupo, string $evento) => [
                'evento' => $evento,
                'total' => $grupo->count(),
                'percentual' => $this->percentual($grupo->count(), max(1, $eventos->count())),
            ])
            ->sortByDesc('total')
            ->values();

        $eventosPorArea = $eventos
            ->groupBy('area')
            ->map(fn (Collection $grupo, string $area) => [
                'area' => $area,
                'total' => $grupo->count(),
                'percentual' => $this->percentual($grupo->count(), max(1, $eventos->count())),
            ])
            ->sortByDesc('total')
            ->values();

        $funil = $this->funil($eventos);
        $serieDiaria = $this->serieDiaria($eventos, $periodo, $inicio);

        return view('super-admin.analytics', [
            'periodo' => $periodo,
            'metricas' => [
                'eventos' => $eventos->count(),
                'visitantes_estimados' => $eventos->pluck('ip')->filter()->unique()->count(),
                'cadastros' => $eventos->whereIn('evento', ['auth.customer_registered', 'mobile.customer_registered'])->count(),
                'alertas_criados' => $eventos->whereIn('evento', ['mobile.price_alert.created'])->count(),
                'eventos_mobile' => $eventos->where('area', 'mobile')->count(),
                'eventos_publicos' => $eventos->where('area', 'public')->count(),
            ],
            'funil' => $funil,
            'serieDiaria' => $serieDiaria,
            'eventosPorTipo' => $eventosPorTipo->take(10),
            'eventosPorArea' => $eventosPorArea,
            'produtosMaisVistos' => $this->rankingSujeitos($inicio, Produto::class),
            'lojasMaisVistas' => $this->rankingSujeitos($inicio, Loja::class),
            'eventosRecentes' => $eventos->take(12),
        ]);
    }

    private function funil(Collection $eventos): Collection
    {
        $etapas = collect([
            [
                'titulo' => 'Busca e listagem',
                'descricao' => 'Pessoas explorando ofertas e usando filtros.',
                'eventos' => ['public.catalog.filtered', 'mobile.catalog.filtered', 'mobile.offers.listed'],
            ],
            [
                'titulo' => 'Interesse em produto',
                'descricao' => 'Aberturas de produto para comparar oferta e loja.',
                'eventos' => ['public.product.viewed', 'mobile.product.viewed'],
            ],
            [
                'titulo' => 'Interesse em loja',
                'descricao' => 'Aberturas de loja depois da descoberta de preço.',
                'eventos' => ['public.store.viewed', 'mobile.store.viewed'],
            ],
            [
                'titulo' => 'Cadastro',
                'descricao' => 'Consumidores que criaram conta.',
                'eventos' => ['auth.customer_registered', 'mobile.customer_registered'],
            ],
            [
                'titulo' => 'Alerta criado',
                'descricao' => 'Sinal forte de recorrência e intenção de voltar.',
                'eventos' => ['mobile.price_alert.created'],
            ],
        ]);

        $base = null;

        return $etapas->map(function (array $etapa) use ($eventos, &$base) {
            $total = $eventos->whereIn('evento', $etapa['eventos'])->count();
            $base ??= max(1, $total);

            return [
                ...$etapa,
                'total' => $total,
                'percentual' => $this->percentual($total, $base),
            ];
        });
    }

    private function serieDiaria(Collection $eventos, int $periodo, mixed $inicio): Collection
    {
        $porDia = $eventos->groupBy(fn (AnalyticsEvent $evento) => $evento->ocorreu_em?->toDateString());
        $maiorVolume = max(1, $porDia->map->count()->max() ?? 0);

        return collect(range(0, $periodo - 1))->map(function (int $indice) use ($inicio, $porDia, $maiorVolume) {
            $dia = $inicio->copy()->addDays($indice);
            $total = $porDia->get($dia->toDateString(), collect())->count();

            return [
                'label' => $dia->format('d/m'),
                'total' => $total,
                'altura' => max(8, $this->percentual($total, $maiorVolume)),
            ];
        });
    }

    private function rankingSujeitos(mixed $inicio, string $classe): Collection
    {
        $rankings = AnalyticsEvent::query()
            ->selectRaw('sujeito_id, count(*) as total')
            ->where('sujeito_type', $classe)
            ->where('ocorreu_em', '>=', $inicio)
            ->whereNotNull('sujeito_id')
            ->groupBy('sujeito_id')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        $modelos = $classe::query()
            ->whereIn('id', $rankings->pluck('sujeito_id'))
            ->get()
            ->keyBy('id');

        return $rankings->map(fn ($ranking) => [
            'id' => $ranking->sujeito_id,
            'nome' => $modelos->get($ranking->sujeito_id)?->nome ?? 'Item removido',
            'total' => (int) $ranking->total,
        ]);
    }

    private function percentual(int|float $valor, int|float $base): int
    {
        if ($base <= 0) {
            return 0;
        }

        return (int) min(100, round(($valor / $base) * 100));
    }
}
