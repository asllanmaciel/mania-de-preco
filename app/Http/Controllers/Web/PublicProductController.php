<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AlertaPreco;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicProductController extends Controller
{
    public function __invoke(Request $request, Produto $produto): View
    {
        abort_unless($produto->status === 'ativo', 404);

        $produto->load([
            'categoria',
            'marca',
            'precos' => fn ($query) => $query->with('loja')->whereHas('loja', fn ($lojaQuery) => $lojaQuery->where('status', 'ativo'))->orderBy('preco'),
        ]);

        $ofertas = $produto->precos->values();
        $melhorOferta = $ofertas->first();
        $menorPreco = (float) $ofertas->min('preco');
        $maiorPreco = (float) $ofertas->max('preco');
        $economia = max(0, $maiorPreco - $menorPreco);
        $cidades = $ofertas->pluck('loja.cidade')->filter()->unique()->values();
        $tiposPreco = $ofertas->pluck('tipo_preco')->unique()->values();
        $alertaDoUsuario = $request->user()
            ? AlertaPreco::query()
                ->where('user_id', $request->user()->id)
                ->where('produto_id', $produto->id)
                ->first()
            : null;
        $precoSugeridoAlerta = $menorPreco > 0 ? max(0.01, round($menorPreco * 0.95, 2)) : null;

        $chart = $ofertas->map(function ($oferta) {
            return [
                'loja' => $oferta->loja?->nome ?? 'Loja nao informada',
                'cidade' => $oferta->loja?->cidade ?? 'Sem cidade',
                'preco' => (float) $oferta->preco,
                'tipo_preco' => $oferta->tipo_preco,
                'rota_loja' => $oferta->loja ? route('lojas.public.show', $oferta->loja) : null,
            ];
        });

        $categoriaRelacionados = Produto::query()
            ->where('status', 'ativo')
            ->where('categoria_id', $produto->categoria_id)
            ->where('id', '!=', $produto->id)
            ->withMin('precos as menor_preco', 'preco')
            ->take(4)
            ->get();

        return view('produtos.show', [
            'produto' => $produto,
            'ofertas' => $ofertas,
            'melhorOferta' => $melhorOferta,
            'menorPreco' => $menorPreco,
            'maiorPreco' => $maiorPreco,
            'economia' => $economia,
            'cidades' => $cidades,
            'tiposPreco' => $tiposPreco,
            'chart' => $chart,
            'categoriaRelacionados' => $categoriaRelacionados,
            'alertaDoUsuario' => $alertaDoUsuario,
            'precoSugeridoAlerta' => $precoSugeridoAlerta,
        ]);
    }
}
