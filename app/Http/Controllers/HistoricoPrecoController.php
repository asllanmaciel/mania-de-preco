<?php

namespace App\Http\Controllers;

use App\Models\HistoricoPreco;
use App\Models\Produto;
use Illuminate\Http\JsonResponse;

class HistoricoPrecoController extends Controller
{
    public function produto(int $id): JsonResponse
    {
        $produto = Produto::findOrFail($id);

        $historico = HistoricoPreco::query()
            ->where('produto_id', $produto->id)
            ->orderBy('created_at')
            ->get([
                'id',
                'produto_id',
                'produto_nome',
                'loja_id',
                'loja_nome',
                'tipo_preco',
                'evento',
                'preco_anterior',
                'preco_atual',
                'variacao_valor',
                'variacao_percentual',
                'created_at',
            ]);

        return response()->json([
            'produto' => [
                'id' => $produto->id,
                'nome' => $produto->nome,
                'slug' => $produto->slug,
            ],
            'resumo' => [
                'total_registros' => $historico->count(),
                'menor_preco' => $historico->whereNotNull('preco_atual')->min('preco_atual'),
                'maior_preco' => $historico->whereNotNull('preco_atual')->max('preco_atual'),
                'ultimo_preco' => optional($historico->whereNotNull('preco_atual')->last())->preco_atual,
                'primeiro_registro_em' => optional($historico->first()?->created_at)?->toISOString(),
                'ultimo_registro_em' => optional($historico->last()?->created_at)?->toISOString(),
            ],
            'timeline' => $historico,
        ]);
    }
}
