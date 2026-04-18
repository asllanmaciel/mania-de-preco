<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Loja;
use App\Models\MovimentacaoFinanceira;
use App\Models\Preco;
use App\Models\Produto;
use App\Support\Content\ChangelogRepository;
use Illuminate\View\View;

class PublicProjectController extends Controller
{
    public function __invoke(ChangelogRepository $changelogs): View
    {
        $latest = $changelogs->all()->take(4);

        return view('projeto', [
            'latest' => $latest,
            'metricas' => [
                'lojas' => Loja::where('status', 'ativo')->count(),
                'produtos' => Produto::where('status', 'ativo')->count(),
                'ofertas' => Preco::count(),
                'movimentacoes' => MovimentacaoFinanceira::where('status', 'realizada')->count(),
            ],
        ]);
    }
}
