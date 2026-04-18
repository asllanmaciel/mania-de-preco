<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome', 'slug', 'categoria_id', 'marca_id',
        'descricao', 'especificacoes', 'imagem_principal', 'galeria_imagens', 'status'
    ];

    protected $casts = [
        'especificacoes' => 'array',
        'galeria_imagens' => 'array',
    ];

    public function getImagemUrlAttribute(): string
    {
        return $this->normalizarImagem($this->imagem_principal) ?? $this->placeholderSvgDataUri();
    }

    public function getGaleriaUrlsAttribute(): array
    {
        $galeria = collect($this->galeria_imagens ?? [])
            ->map(fn ($imagem) => $this->normalizarImagem($imagem))
            ->filter()
            ->values();

        if ($galeria->isEmpty()) {
            return [$this->imagem_url];
        }

        if ($galeria->doesntContain($this->imagem_url)) {
            $galeria->prepend($this->imagem_url);
        }

        return $galeria->unique()->values()->all();
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function precos()
    {
        return $this->hasMany(Preco::class);
    }

    public function alertasPreco()
    {
        return $this->hasMany(AlertaPreco::class);
    }

    private function placeholderSvgDataUri(): string
    {
        $categoria = $this->categoria?->nome ?? 'Catalogo';
        $marca = $this->marca?->nome ?? 'Produto';
        $nome = $this->nome ?: 'Produto';
        $sigla = Str::upper(Str::substr(Str::slug($nome, ''), 0, 2) ?: 'MP');
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 480" role="img" aria-label="{$nome}">
  <defs>
    <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#fff4e7" />
      <stop offset="100%" stop-color="#efd3b8" />
    </linearGradient>
    <linearGradient id="pack" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#ff7a3f" />
      <stop offset="100%" stop-color="#d4521c" />
    </linearGradient>
  </defs>
  <rect width="640" height="480" rx="40" fill="url(#bg)" />
  <circle cx="558" cy="82" r="70" fill="#0f9f8f" fill-opacity=".12" />
  <circle cx="114" cy="390" r="88" fill="#ff6b2c" fill-opacity=".10" />
  <rect x="160" y="76" width="320" height="328" rx="28" fill="url(#pack)" />
  <rect x="184" y="102" width="272" height="68" rx="18" fill="#fff4eb" fill-opacity=".92" />
  <text x="320" y="145" font-family="Arial, sans-serif" font-size="34" font-weight="700" text-anchor="middle" fill="#20140f">{$sigla}</text>
  <text x="320" y="228" font-family="Arial, sans-serif" font-size="28" font-weight="700" text-anchor="middle" fill="#fff7ef">{$categoria}</text>
  <text x="320" y="268" font-family="Arial, sans-serif" font-size="20" text-anchor="middle" fill="#ffe8dc">{$marca}</text>
  <rect x="214" y="308" width="212" height="12" rx="6" fill="#fff7ef" fill-opacity=".88" />
  <rect x="236" y="334" width="168" height="12" rx="6" fill="#fff7ef" fill-opacity=".62" />
</svg>
SVG;

        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    }

    private function normalizarImagem(mixed $imagem): ?string
    {
        $imagem = trim((string) $imagem);

        if ($imagem === '') {
            return null;
        }

        if (Str::startsWith($imagem, ['http://', 'https://', 'data:image'])) {
            return $imagem;
        }

        return Str::startsWith($imagem, ['/'])
            ? $imagem
            : asset($imagem);
    }
}
