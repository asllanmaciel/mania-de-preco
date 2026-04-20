<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\AlertaPreco;
use App\Models\Loja;
use App\Models\Produto;
use App\Services\Precos\AlertaPrecoEvaluator;
use App\Support\Analytics\ProductAnalytics;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AlertaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $alertas = AlertaPreco::query()
            ->with(['produto.categoria', 'produto.marca', 'lojaReferencia'])
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->get();

        return response()->json([
            'data' => $alertas->map(fn (AlertaPreco $alerta) => $this->alertaPayload($alerta))->values(),
        ]);
    }

    public function store(Request $request, AlertaPrecoEvaluator $evaluator, ProductAnalytics $analytics): JsonResponse
    {
        $dados = $request->validate([
            'produto_id' => [
                'required',
                Rule::exists('produtos', 'id')->where(fn ($query) => $query->where('status', 'ativo')),
            ],
            'preco_desejado' => ['required', 'numeric', 'min:0.01'],
            'status' => ['nullable', Rule::in(['ativo', 'inativo'])],
        ]);

        $alerta = AlertaPreco::create([
            'user_id' => $request->user()->id,
            'produto_id' => $dados['produto_id'],
            'preco_desejado' => $dados['preco_desejado'],
            'status' => $dados['status'] ?? 'ativo',
        ]);

        $alerta = $alerta->status === 'inativo'
            ? $alerta->fresh(['produto.categoria', 'produto.marca', 'lojaReferencia'])
            : $evaluator->avaliar($alerta);

        $analytics->track($request, 'mobile.price_alert.created', 'mobile', [
            'produto_id' => $alerta->produto_id,
            'preco_desejado' => (float) $alerta->preco_desejado,
            'status' => $alerta->status,
        ], $alerta);

        return response()->json([
            'data' => $this->alertaPayload($alerta),
        ], 201);
    }

    public function update(Request $request, AlertaPreco $alerta, AlertaPrecoEvaluator $evaluator, ProductAnalytics $analytics): JsonResponse
    {
        abort_unless($alerta->user_id === $request->user()->id, 403, 'Você não pode editar este alerta.');

        $dados = $request->validate([
            'preco_desejado' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'status' => ['sometimes', 'required', Rule::in(['ativo', 'inativo'])],
        ]);

        $alerta->update($dados);

        if ($alerta->status === 'inativo') {
            $alerta->forceFill(['ultima_avaliacao_em' => now()])->saveQuietly();
            $alerta = $alerta->fresh(['produto.categoria', 'produto.marca', 'lojaReferencia']);
        } else {
            $alerta = $evaluator->avaliar($alerta);
        }

        $analytics->track($request, 'mobile.price_alert.updated', 'mobile', [
            'produto_id' => $alerta->produto_id,
            'preco_desejado' => (float) $alerta->preco_desejado,
            'status' => $alerta->status,
        ], $alerta);

        return response()->json([
            'data' => $this->alertaPayload($alerta),
        ]);
    }

    public function destroy(Request $request, AlertaPreco $alerta, ProductAnalytics $analytics): JsonResponse
    {
        abort_unless($alerta->user_id === $request->user()->id, 403, 'Você não pode remover este alerta.');

        $analytics->track($request, 'mobile.price_alert.deleted', 'mobile', [
            'produto_id' => $alerta->produto_id,
            'status' => $alerta->status,
        ], $alerta);

        $alerta->delete();

        return response()->json(null, 204);
    }

    private function alertaPayload(AlertaPreco $alerta): array
    {
        $alerta->loadMissing(['produto.categoria', 'produto.marca', 'lojaReferencia']);

        return [
            'id' => $alerta->id,
            'status' => $alerta->status,
            'preco_desejado' => (float) $alerta->preco_desejado,
            'preco_base' => $this->valorDecimal($alerta->preco_base),
            'ultimo_preco_menor' => $this->valorDecimal($alerta->ultimo_preco_menor),
            'menor_preco_historico' => $this->valorDecimal($alerta->menor_preco_historico),
            'variacao_desde_ativacao' => $this->valorDecimal($alerta->variacao_desde_ativacao),
            'variacao_percentual_desde_ativacao' => $this->valorDecimal($alerta->variacao_percentual_desde_ativacao),
            'disparado_em' => $alerta->disparado_em?->toIso8601String(),
            'ultima_avaliacao_em' => $alerta->ultima_avaliacao_em?->toIso8601String(),
            'produto' => $this->produtoPayload($alerta->produto),
            'loja_referencia' => $this->lojaPayload($alerta->lojaReferencia),
        ];
    }

    private function produtoPayload(?Produto $produto): ?array
    {
        if (! $produto) {
            return null;
        }

        return [
            'id' => $produto->id,
            'nome' => $produto->nome,
            'slug' => $produto->slug,
            'imagem' => $produto->imagem_url,
            'categoria' => $produto->categoria ? [
                'id' => $produto->categoria->id,
                'nome' => $produto->categoria->nome,
                'slug' => $produto->categoria->slug,
            ] : null,
            'marca' => $produto->marca ? [
                'id' => $produto->marca->id,
                'nome' => $produto->marca->nome,
            ] : null,
        ];
    }

    private function lojaPayload(?Loja $loja): ?array
    {
        if (! $loja) {
            return null;
        }

        return [
            'id' => $loja->id,
            'nome' => $loja->nome,
            'cidade' => $loja->cidade,
            'uf' => $loja->uf,
            'tipo_loja' => $loja->tipo_loja,
        ];
    }

    private function valorDecimal(mixed $valor): ?float
    {
        return $valor === null ? null : (float) $valor;
    }
}
