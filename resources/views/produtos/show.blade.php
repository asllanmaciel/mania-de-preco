<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $produto->nome }} | Mania de Preco</title>
        <meta name="description" content="Compare ofertas de {{ $produto->nome }} em lojas reais e encontre o melhor preco com contexto.">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|ibm-plex-mono:400,500" rel="stylesheet" />

        <style>
            :root { --bg:#f7f0e6; --bg2:#eadbc7; --surface:rgba(255,251,245,.84); --line:rgba(66,37,21,.12); --text:#21140f; --muted:#6d5247; --accent:#ff6b2c; --accent2:#0f9f8f; --shadow:0 28px 80px rgba(60,28,14,.12); --r1:30px; --r2:22px; --container:1180px; }
            * { box-sizing:border-box; }
            body { margin:0; min-height:100vh; font-family:"Space Grotesk",sans-serif; color:var(--text); background:radial-gradient(circle at top left, rgba(255,107,44,.18), transparent 28%), radial-gradient(circle at 85% 0%, rgba(15,159,143,.15), transparent 24%), linear-gradient(180deg,#fff7ec 0%, var(--bg) 44%, var(--bg2) 100%); }
            a { color:inherit; text-decoration:none; }
            .container { width:min(calc(100% - 32px), var(--container)); margin:0 auto; }
            .topbar, .hero, .stats, .section-head, .bar-meta, .offer-head, .footer { display:flex; gap:14px; }
            .topbar, .section-head, .bar-meta, .offer-head, .footer { justify-content:space-between; align-items:flex-start; }
            .topbar { padding:22px 0; align-items:center; }
            .brand { display:inline-flex; align-items:center; gap:14px; font-weight:700; letter-spacing:-.03em; }
            .brand-badge { display:grid; place-items:center; width:44px; height:44px; border-radius:14px; color:#fff7ef; background:linear-gradient(135deg,#ff6b2c 0%,#cf4e1b 100%); box-shadow:0 14px 30px rgba(207,78,27,.28); }
            .chip, .badge { display:inline-flex; align-items:center; justify-content:center; padding:8px 12px; border-radius:999px; }
            .chip { border:1px solid var(--line); background:rgba(255,255,255,.58); color:var(--muted); font-size:.92rem; }
            .hero { display:grid; grid-template-columns:1.05fr .95fr; padding:14px 0 22px; }
            .grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
            .card, .hero-card { background:var(--surface); border:1px solid rgba(255,255,255,.62); box-shadow:var(--shadow); backdrop-filter:blur(16px); }
            .hero-card { padding:38px; border-radius:var(--r1); }
            .card { padding:24px; border-radius:var(--r2); }
            h1 { margin:14px 0 12px; font-size:clamp(2.8rem,5vw,4.8rem); line-height:.92; letter-spacing:-.08em; }
            h2 { margin:0; font-size:clamp(1.8rem,3vw,2.6rem); letter-spacing:-.06em; }
            h3 { margin:0; font-size:1.16rem; letter-spacing:-.04em; }
            p, .muted, .small { color:var(--muted); line-height:1.72; }
            .hero p { margin:0; max-width:58ch; font-size:1.04rem; }
            .button, .button-secondary { display:inline-flex; align-items:center; justify-content:center; padding:14px 18px; border-radius:16px; font-weight:700; border:1px solid transparent; }
            .button { color:#fff7ef; background:linear-gradient(135deg,#ff6b2c 0%,#d4511d 100%); box-shadow:0 18px 36px rgba(212,81,29,.28); }
            .button-secondary { background:rgba(255,255,255,.72); border-color:var(--line); }
            .stats, .bars, .offers, .related { display:grid; gap:16px; }
            .stats { grid-template-columns:repeat(4, minmax(0, 1fr)); margin-top:26px; }
            .stat, .mini, .offer, .related-card { padding:16px; border-radius:18px; background:rgba(255,255,255,.74); border:1px solid rgba(255,255,255,.72); }
            .stat strong, .mini strong, .offer strong, .related-card strong { display:block; margin-bottom:6px; }
            .stat strong { font-size:1.5rem; }
            .product-media { position:relative; overflow:hidden; border-radius:24px; background:linear-gradient(135deg, rgba(255,255,255,.88), rgba(255,239,225,.94)); border:1px solid rgba(66,37,21,.08); min-height:260px; }
            .product-media img { width:100%; height:100%; min-height:260px; object-fit:cover; display:block; }
            .related-card img { width:100%; height:140px; object-fit:cover; border-radius:16px; margin-bottom:14px; background:#fff; border:1px solid rgba(66,37,21,.08); }
            .section { padding:18px 0; }
            .section-head { margin-bottom:16px; }
            .bar-meta { font-size:.92rem; }
            .track { position:relative; height:12px; border-radius:999px; background:rgba(44,24,17,.08); overflow:hidden; }
            .fill { display:block; height:100%; border-radius:inherit; background:linear-gradient(90deg,var(--accent),#ffb06b); }
            .fill.teal { background:linear-gradient(90deg,var(--accent2),#61e7d9); }
            .offers { grid-template-columns:1fr; }
            .badge { background:rgba(15,159,143,.12); color:#0e6e64; font-size:.82rem; }
            .offer-head { align-items:center; }
            .related { grid-template-columns:repeat(4, minmax(0, 1fr)); }
            .footer { padding:30px 0 48px; color:var(--muted); font-size:.92rem; }
            .footer code { padding:4px 8px; border-radius:999px; background:rgba(255,255,255,.76); border:1px solid var(--line); font:400 .82rem "IBM Plex Mono", monospace; }
            @media (max-width:1100px) { .hero, .grid, .stats, .related { grid-template-columns:1fr; } .section-head, .footer { flex-direction:column; align-items:flex-start; } }
            @media (max-width:720px) { .topbar, .offer-head { flex-direction:column; align-items:stretch; } .hero-card, .card { padding:20px; } }
        </style>
    </head>
    <body>
        @php $chartMax = max(1, (float) $chart->max('preco')); @endphp
        <div class="container">
            <header class="topbar">
                <a class="brand" href="{{ route('home') }}">
                    <span class="brand-badge">MP</span>
                    <span>Mania de Preco</span>
                </a>

                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <a class="chip" href="{{ route('home') }}">Voltar para ofertas</a>
                    <a class="chip" href="{{ route('projeto') }}">Projeto</a>
                    <a class="chip" href="{{ route('novidades.index') }}">Novidades</a>
                    @auth
                        <a class="chip" href="{{ route('admin.dashboard') }}">Abrir painel</a>
                    @endif
                </div>
            </header>

            <main>
                <section class="hero">
                    <article class="hero-card">
                        <span class="badge">{{ $produto->categoria?->nome ?? 'Sem categoria' }}</span>
                        <h1>{{ $produto->nome }}</h1>
                        <p>{{ $produto->marca?->nome ?? 'Marca nao informada' }}</p>
                        <p style="margin-top:12px;">{{ $produto->descricao ?: 'Produto publicado na vitrine do Mania de Preco para comparacao entre lojas ativas.' }}</p>

                        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:24px;">
                            @if ($melhorOferta && $melhorOferta->loja)
                                <a class="button" href="{{ route('lojas.public.show', $melhorOferta->loja) }}">Ver melhor loja agora</a>
                            @endif
                            <a class="button-secondary" href="#ofertas">Comparar ofertas</a>
                        </div>

                        <div class="stats">
                            <div class="stat"><strong>R$ {{ number_format($menorPreco, 2, ',', '.') }}</strong><span>melhor preco</span></div>
                            <div class="stat"><strong>R$ {{ number_format($economia, 2, ',', '.') }}</strong><span>economia potencial</span></div>
                            <div class="stat"><strong>{{ number_format($ofertas->count(), 0, ',', '.') }}</strong><span>ofertas ativas</span></div>
                            <div class="stat"><strong>{{ number_format($cidades->count(), 0, ',', '.') }}</strong><span>cidades no comparativo</span></div>
                        </div>
                    </article>

                    <aside class="card">
                        <div class="product-media" style="margin-bottom:18px;">
                            <img src="{{ $produto->imagem_url }}" alt="{{ $produto->nome }}">
                        </div>

                        <div class="section-head">
                            <div>
                                <h3>Resumo de mercado do produto</h3>
                                <p class="muted" style="margin:8px 0 0;">Uma leitura direta para entender disponibilidade, canais e onde vale clicar primeiro.</p>
                            </div>
                        </div>

                        <div class="grid" style="grid-template-columns:1fr;">
                            <div class="mini">
                                <strong>Faixa de preco</strong>
                                <span>De R$ {{ number_format($menorPreco, 2, ',', '.') }} ate R$ {{ number_format($maiorPreco, 2, ',', '.') }}</span>
                            </div>
                            <div class="mini">
                                <strong>Tipos de pagamento</strong>
                                <span>{{ $tiposPreco->map(fn ($tipo) => ucfirst(str_replace('_', ' ', $tipo)))->implode(' - ') }}</span>
                            </div>
                            <div class="mini">
                                <strong>Cidades com oferta</strong>
                                <span>{{ $cidades->implode(' - ') ?: 'Sem cidade informada' }}</span>
                            </div>
                        </div>
                    </aside>
                </section>

                <section class="section">
                    <div class="grid">
                        <article class="card">
                            <div class="section-head">
                                <div>
                                    <h2>Ranking de lojas</h2>
                                    <p class="muted">Veja rapidamente como o preco se distribui entre as lojas participantes.</p>
                                </div>
                            </div>

                            <div class="bars">
                                @foreach ($chart as $item)
                                    <div>
                                        <div class="bar-meta"><span>{{ $item['loja'] }}</span><span>R$ {{ number_format($item['preco'], 2, ',', '.') }}</span></div>
                                        <div class="track"><span class="fill teal" style="width: {{ min(100, ($item['preco'] / $chartMax) * 100) }}%;"></span></div>
                                    </div>
                                @endforeach
                            </div>
                        </article>

                        <article class="card" id="ofertas">
                            <div class="section-head">
                                <div>
                                    <h2>Onde comprar agora</h2>
                                    <p class="muted">Ofertas ordenadas para reduzir atrito na decisao e acelerar o clique.</p>
                                </div>
                            </div>

                            <div class="offers">
                                @foreach ($chart as $item)
                                    <article class="offer">
                                        <div class="offer-head">
                                            <div>
                                                <strong>{{ $item['loja'] }}</strong>
                                                <span class="small">{{ $item['cidade'] }} - {{ ucfirst(str_replace('_', ' ', $item['tipo_preco'])) }}</span>
                                            </div>
                                            <span class="badge">R$ {{ number_format($item['preco'], 2, ',', '.') }}</span>
                                        </div>

                                        @if ($item['rota_loja'])
                                            <a class="button-secondary" href="{{ $item['rota_loja'] }}">Ver perfil da loja</a>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        </article>
                    </div>
                </section>

                @if ($categoriaRelacionados->isNotEmpty())
                    <section class="section">
                        <div class="section-head">
                            <div>
                                <h2>Mais produtos desta categoria</h2>
                                <p class="muted">Descobertas complementares para manter a navegacao viva e util.</p>
                            </div>
                        </div>

                        <div class="related">
                            @foreach ($categoriaRelacionados as $relacionado)
                                <a class="related-card" href="{{ route('produtos.public.show', $relacionado) }}">
                                    <img src="{{ $relacionado->imagem_url }}" alt="{{ $relacionado->nome }}">
                                    <strong>{{ $relacionado->nome }}</strong>
                                    <span class="small">{{ $relacionado->marca?->nome ?? 'Marca nao informada' }}</span>
                                    <span class="small">A partir de R$ {{ number_format((float) ($relacionado->menor_preco ?? 0), 2, ',', '.') }}</span>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif
            </main>

            <footer class="footer">
                <span>Pagina publica do produto pensada para conversao, comparacao e navegacao natural para a loja.</span>
                <code>{{ route('produtos.public.show', $produto) }}</code>
            </footer>
        </div>
    </body>
</html>
