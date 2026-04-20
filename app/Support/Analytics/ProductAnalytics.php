<?php

namespace App\Support\Analytics;

use App\Models\AnalyticsEvent;
use App\Models\Conta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ProductAnalytics
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function track(
        Request $request,
        string $evento,
        string $area = 'public',
        array $metadata = [],
        ?Model $sujeito = null,
        ?Conta $conta = null
    ): ?AnalyticsEvent {
        if (! config('analytics.enabled', true)) {
            return null;
        }

        $user = $request->user();
        $conta ??= $user?->contasAtivas()->first();

        return AnalyticsEvent::create([
            'user_id' => $user?->id,
            'conta_id' => $conta?->id,
            'evento' => Str::limit($evento, 120, ''),
            'area' => Str::limit($area, 40, ''),
            'sujeito_type' => $sujeito?->getMorphClass(),
            'sujeito_id' => $sujeito?->getKey(),
            'metadata' => $this->limparMetadata($metadata),
            'ip' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1024, ''),
            'ocorreu_em' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $metadata
     * @return array<string, mixed>
     */
    private function limparMetadata(array $metadata): array
    {
        $metadataPlana = collect(Arr::dot($metadata))
            ->map(function ($valor) {
                if (is_bool($valor) || is_numeric($valor) || $valor === null) {
                    return $valor;
                }

                return Str::limit((string) $valor, 255, '');
            })
            ->all();

        return Arr::undot($metadataPlana);
    }
}
