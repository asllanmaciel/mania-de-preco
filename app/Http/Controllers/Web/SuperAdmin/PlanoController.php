<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plano;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlanoController extends Controller
{
    public function index(Request $request): View
    {
        $busca = trim((string) $request->string('busca'));
        $status = trim((string) $request->string('status'));

        $planos = Plano::query()
            ->withCount('assinaturas')
            ->when($busca !== '', function ($query) use ($busca) {
                $query->where(function ($inner) use ($busca) {
                    $inner->where('nome', 'like', "%{$busca}%")
                        ->orWhere('slug', 'like', "%{$busca}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderBy('valor_mensal')
            ->paginate(12)
            ->withQueryString();

        return view('super-admin.planos.index', [
            'user' => $request->user(),
            'planos' => $planos,
            'busca' => $busca,
            'statusSelecionado' => $status,
            'metricas' => [
                'ativos' => Plano::where('status', 'ativo')->count(),
                'assinaturas' => Plano::withCount('assinaturas')->get()->sum('assinaturas_count'),
            ],
        ]);
    }

    public function create(Request $request): View
    {
        return view('super-admin.planos.create', [
            'user' => $request->user(),
            'plano' => new Plano(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $dados = $this->validarPlano($request);

        Plano::create([
            ...$dados,
            'slug' => $this->resolverSlug($dados['slug'] ?? $dados['nome']),
            'recursos' => $this->parseRecursos($request->input('recursos_texto')),
        ]);

        return redirect()
            ->route('super-admin.planos.index')
            ->with('status', 'Plano criado com sucesso.');
    }

    public function edit(Request $request, Plano $plano): View
    {
        return view('super-admin.planos.edit', [
            'user' => $request->user(),
            'plano' => $plano,
        ]);
    }

    public function update(Request $request, Plano $plano): RedirectResponse
    {
        $dados = $this->validarPlano($request, $plano);

        $plano->update([
            ...$dados,
            'slug' => $this->resolverSlug($dados['slug'] ?? $dados['nome'], $plano->id),
            'recursos' => $this->parseRecursos($request->input('recursos_texto')),
        ]);

        return redirect()
            ->route('super-admin.planos.edit', $plano)
            ->with('status', 'Plano atualizado com sucesso.');
    }

    private function validarPlano(Request $request, ?Plano $plano = null): array
    {
        $planoId = $plano?->id;

        return $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:planos,slug,' . $planoId],
            'descricao' => ['nullable', 'string'],
            'valor_mensal' => ['required', 'numeric', 'min:0'],
            'valor_anual' => ['required', 'numeric', 'min:0'],
            'limite_usuarios' => ['nullable', 'integer', 'min:1'],
            'limite_lojas' => ['nullable', 'integer', 'min:1'],
            'limite_produtos' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'in:ativo,inativo'],
        ]);
    }

    private function parseRecursos(?string $texto): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $texto))
            ->map(fn ($linha) => trim($linha))
            ->filter()
            ->values()
            ->all();
    }

    private function resolverSlug(string $base, ?int $ignorarId = null): string
    {
        $slugBase = Str::slug($base);
        $slug = $slugBase !== '' ? $slugBase : 'plano';
        $contador = 1;

        while (
            Plano::query()
                ->when($ignorarId, fn ($query) => $query->whereKeyNot($ignorarId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = "{$slugBase}-{$contador}";
            $contador++;
        }

        return $slug;
    }
}
