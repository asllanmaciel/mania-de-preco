<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Loja;
use App\Models\Produto;
use App\Support\Content\ChangelogRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;

class PublicSeoController extends Controller
{
    public function robots(): Response
    {
        $conteudo = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /super-admin',
            'Disallow: /cliente',
            'Disallow: /painel',
            'Disallow: /login',
            'Disallow: /cadastro',
            'Disallow: /resetar-senha',
            'Disallow: /esqueci-minha-senha',
            'Disallow: /radar-precos',
            '',
            'Sitemap: ' . route('seo.sitemap'),
            '',
        ]);

        return response($conteudo, 200, [
            'Cache-Control' => 'public, max-age=3600',
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    public function sitemap(ChangelogRepository $changelogs): Response
    {
        $urls = collect([
            $this->url(route('home'), 'hourly', '1.0'),
            $this->url(route('ofertas'), 'hourly', '0.9'),
            $this->url(route('projeto'), 'weekly', '0.7'),
            $this->url(route('novidades.index'), 'daily', '0.7'),
            $this->url(route('termos'), 'monthly', '0.4'),
            $this->url(route('privacidade'), 'monthly', '0.4'),
            $this->url(route('suporte'), 'monthly', '0.5'),
        ]);

        Loja::query()
            ->where('status', 'ativo')
            ->whereHas('precos')
            ->latest('updated_at')
            ->take(500)
            ->get()
            ->each(fn (Loja $loja) => $urls->push($this->url(
                route('lojas.public.show', $loja),
                'daily',
                '0.8',
                $loja->updated_at?->toAtomString()
            )));

        Produto::query()
            ->where('status', 'ativo')
            ->whereHas('precos.loja', fn (Builder $query) => $query->where('status', 'ativo'))
            ->latest('updated_at')
            ->take(1000)
            ->get()
            ->each(fn (Produto $produto) => $urls->push($this->url(
                route('produtos.public.show', $produto),
                'daily',
                '0.9',
                $produto->updated_at?->toAtomString()
            )));

        $changelogs->all()
            ->take(80)
            ->each(fn (array $entry) => $urls->push($this->url(
                route('novidades.show', $entry['slug']),
                'weekly',
                '0.6'
            )));

        $xml = view('seo.sitemap', [
            'urls' => $urls,
        ])->render();

        return response($xml, 200, [
            'Cache-Control' => 'public, max-age=3600',
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    private function url(string $loc, string $changefreq, string $priority, ?string $lastmod = null): array
    {
        return compact('loc', 'changefreq', 'priority', 'lastmod');
    }
}
