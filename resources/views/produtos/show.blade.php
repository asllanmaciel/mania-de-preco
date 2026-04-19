<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $produto->nome }} | Mania de Preço</title>
        <meta name="description" content="Compare ofertas de {{ $produto->nome }} em lojas reais e encontre o melhor preço com contexto.">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|ibm-plex-mono:400,500" rel="stylesheet" />

        <style>
            :root { --bg:#f7f0e6; --bg2:#eadbc7; --surface:rgba(255,251,245,.84); --line:rgba(66,37,21,.12); --text:#21140f; --muted:#6d5247; --accent:#ff6b2c; --accent2:#0f9f8f; --shadow:0 28px 80px rgba(60,28,14,.12); --r1:30px; --r2:22px; --container:1180px; }
            * { box-sizing:border-box; }
            body { margin:0; min-height:100vh; font-family:"Space Grotesk",sans-serif; color:var(--text); background:radial-gradient(circle at top left, rgba(255,107,44,.18), transparent 28%), radial-gradient(circle at 85% 0%, rgba(15,159,143,.15), transparent 24%), linear-gradient(180deg,#fff7ec 0%, var(--bg) 44%, var(--bg2) 100%); }
            a { color:inherit; text-decoration:none; }
            .container { width:min(calc(100% - 32px), var(--container)); margin:0 auto; }
            .topbar, .stats, .section-head, .bar-meta, .offer-head, .footer, .thumb-grid, .trust-list { display:flex; gap:14px; }
            .topbar, .section-head, .bar-meta, .offer-head, .footer { justify-content:space-between; align-items:flex-start; }
            .topbar { padding:22px 0; align-items:center; }
            .brand { display:inline-flex; align-items:center; gap:14px; font-weight:700; letter-spacing:-.03em; }
            .brand-badge { display:grid; place-items:center; width:44px; height:44px; border-radius:14px; color:#fff7ef; background:linear-gradient(135deg,#ff6b2c 0%,#cf4e1b 100%); box-shadow:0 14px 30px rgba(207,78,27,.28); }
            .chip, .badge { display:inline-flex; align-items:center; justify-content:center; padding:8px 12px; border-radius:999px; }
            .chip { border:1px solid var(--line); background:rgba(255,255,255,.58); color:var(--muted); font-size:.92rem; }
            .hero, .grid, .stats, .bars, .offers, .related, .hero-actions, .media-stack, .trust-list { display:grid; gap:16px; }
            .hero { grid-template-columns:1fr; padding:14px 0 22px; }
            .grid { grid-template-columns:1fr; }
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
            .hero-actions { margin-top:24px; }
            .stats { grid-template-columns:1fr; margin-top:26px; }
            .stat, .mini, .offer, .related-card { padding:16px; border-radius:18px; background:rgba(255,255,255,.74); border:1px solid rgba(255,255,255,.72); }
            .stat strong, .mini strong, .offer strong, .related-card strong { display:block; margin-bottom:6px; }
            .stat strong { font-size:1.5rem; }
            .product-media { position:relative; overflow:hidden; border-radius:24px; background:linear-gradient(135deg, rgba(255,255,255,.88), rgba(255,239,225,.94)); border:1px solid rgba(66,37,21,.08); min-height:300px; }
            .product-media img { width:100%; height:100%; min-height:300px; object-fit:cover; display:block; }
            .thumb-grid { flex-wrap:wrap; }
            .thumb-button { width:84px; height:84px; padding:0; border:none; border-radius:18px; overflow:hidden; background:rgba(255,255,255,.84); box-shadow:0 10px 24px rgba(60,28,14,.08); cursor:pointer; }
            .thumb-button img { width:100%; height:100%; object-fit:cover; display:block; }
            .thumb-button.is-active { outline:3px solid rgba(255,107,44,.28); }
            .trust-list { grid-template-columns:1fr; }
            .trust-item { padding:16px; border-radius:18px; background:rgba(255,255,255,.76); border:1px solid rgba(66,37,21,.08); }
            .trust-item strong { display:block; margin-bottom:6px; font-size:1.02rem; }
            .alert-card { display:grid; gap:12px; margin-top:16px; padding:18px; border-radius:20px; background:linear-gradient(135deg,rgba(15,159,143,.13),rgba(255,255,255,.82)); border:1px solid rgba(15,159,143,.16); }
            .alert-card form { display:grid; gap:10px; }
            .alert-card label { display:grid; gap:7px; font-weight:700; }
            .alert-card input { width:100%; min-height:46px; padding:12px 14px; border-radius:14px; border:1px solid var(--line); background:#fff; color:var(--text); outline:none; }
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
            .related { grid-template-columns:1fr; }
            .footer { padding:30px 0 48px; color:var(--muted); font-size:.92rem; }
            .footer code { padding:4px 8px; border-radius:999px; background:rgba(255,255,255,.76); border:1px solid var(--line); font:400 .82rem "IBM Plex Mono", monospace; }
            @media (min-width:721px) { .stats { grid-template-columns:repeat(2, minmax(0, 1fr)); } .related { grid-template-columns:repeat(2, minmax(0, 1fr)); } .hero-actions { grid-template-columns:repeat(2, minmax(0, max-content)); } }
            @media (min-width:1101px) { .hero { grid-template-columns:1.02fr .98fr; } .grid { grid-template-columns:1fr 1fr; } .stats { grid-template-columns:repeat(4, minmax(0, 1fr)); } .related { grid-template-columns:repeat(4, minmax(0, 1fr)); } }
            @media (max-width:720px) { .topbar, .offer-head, .thumb-grid, .footer { flex-direction:column; align-items:stretch; } .hero-card, .card { padding:20px; } .thumb-button { width:100%; height:72px; } .button, .button-secondary { width:100%; } }
        </style>
    </head>
    <body>
        @php
            $chartMax = max(1, (float) $chart->max('preco'));
            $galeria = $produto->galeria_urls;
            $galeriaPrincipal = $galeria[0] ?? $produto->imagem_url;
        @endphp
        <div class="container">
            <header class="topbar">
                <a class="brand" href="{{ route('home') }}">
                    <span class="brand-badge">MP</span>
                    <span>Mania de Preço</span>
                </a>

                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <a class="chip" href="{{ route('home') }}">Voltar para ofertas</a>
                    <a class="chip" href="{{ route('projeto') }}">Para lojas</a>
                    <a class="chip" href="{{ route('novidades.index') }}">Lançamentos</a>
                    @auth
                        <a class="chip" href="{{ route('painel.redirect') }}">Abrir painel</a>
                    @else
                        <a class="chip" href="{{ route('register') }}">Criar alerta</a>
                    @endauth
                </div>
            </header>

            <main>
                <section class="hero">
                    <article class="hero-card">
                        <span class="badge">{{ $produto->categoria?->nome ?? 'Sem categoria' }}</span>
                        <h1>{{ $produto->nome }}</h1>
                        <p>{{ $produto->marca?->nome ?? 'Marca nao informada' }}</p>
                        <p style="margin-top:12px;">{{ $produto->descricao ?: 'Compare este item entre lojas ativas e descubra rápido qual oferta faz mais sentido para você.' }}</p>

                        <div class="hero-actions">
                            @if ($melhorOferta && $melhorOferta->loja)
                                <a class="button" href="{{ route('lojas.public.show', $melhorOferta->loja) }}">Ver melhor loja agora</a>
                            @endif
                            <a class="button-secondary" href="#ofertas">Comparar ofertas</a>
                        </div>

                        <div class="stats">
                            <div class="stat"><strong>R$ {{ number_format($menorPreco, 2, ',', '.') }}</strong><span>melhor preço</span></div>
                            <div class="stat"><strong>R$ {{ number_format($economia, 2, ',', '.') }}</strong><span>economia potencial</span></div>
                            <div class="stat"><strong>{{ number_format($ofertas->count(), 0, ',', '.') }}</strong><span>ofertas ativas</span></div>
                            <div class="stat"><strong>{{ number_format($cidades->count(), 0, ',', '.') }}</strong><span>cidades no comparativo</span></div>
                        </div>
                    </article>

                    <aside class="card">
                        <div class="media-stack">
                            <div class="product-media">
                                <img id="gallery-main" src="{{ $galeriaPrincipal }}" alt="{{ $produto->nome }}">
                            </div>

                            @if (count($galeria) > 1)
                                <div class="thumb-grid" role="tablist" aria-label="Galeria do produto">
                                    @foreach ($galeria as $imagem)
                                        <button
                                            class="thumb-button {{ $loop->first ? 'is-active' : '' }}"
                                            type="button"
                                            data-gallery-src="{{ $imagem }}"
                                            aria-label="Ver imagem {{ $loop->iteration }} do produto"
                                        >
                                            <img src="{{ $imagem }}" alt="{{ $produto->nome }} imagem {{ $loop->iteration }}">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="section-head">
                            <div>
                                <h3>Resumo rápido para decidir</h3>
                                <p class="muted" style="margin:8px 0 0;">Uma leitura direta para entender disponibilidade, tipos de pagamento e onde vale clicar primeiro.</p>
                            </div>
                        </div>

                        <div class="grid" style="grid-template-columns:1fr;">
                            <div class="mini">
                                <strong>Faixa de preço</strong>
                                <span>De R$ {{ number_format($menorPreco, 2, ',', '.') }} até R$ {{ number_format($maiorPreco, 2, ',', '.') }}</span>
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

                        <div class="trust-list" style="margin-top:16px;">
                            <div class="trust-item">
                                <strong>Comparação feita para decidir rápido</strong>
                                <span class="small">Galeria, preço e chamada principal ficam próximos para reduzir rolagem e acelerar a escolha.</span>
                            </div>
                            <div class="trust-item">
                                <strong>Oferta mais competitiva em destaque</strong>
                                <span class="small">O bloco principal resume melhor valor, economia e variedade sem esconder o detalhe das lojas.</span>
                            </div>
                        </div>
                        <div class="alert-card">
                            <div>
                                <h3>Quer ser avisado quando baixar?</h3>
                                <p class="muted" style="margin:8px 0 0;">Defina uma meta de preco e acompanhe este produto pela sua area de cliente.</p>
                            </div>

                            @auth
                                @if ($alertaDoUsuario)
                                    <div class="trust-item">
                                        <strong>Voce ja monitora este produto</strong>
                                        <span class="small">
                                            meta R$ {{ number_format((float) $alertaDoUsuario->preco_desejado, 2, ',', '.') }}
                                            | status {{ $alertaDoUsuario->status }}
                                        </span>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('cliente.alertas.store') }}">
                                    @csrf
                                    <input type="hidden" name="produto_id" value="{{ $produto->id }}">

                                    <label>
                                        <span>Preco desejado</span>
                                        <input type="number" name="preco_desejado" min="0.01" step="0.01" value="{{ old('preco_desejado', $alertaDoUsuario?->preco_desejado ?? $precoSugeridoAlerta) }}" required>
                                    </label>

                                    <button class="button" type="submit">{{ $alertaDoUsuario ? 'Atualizar alerta' : 'Criar alerta de preco' }}</button>
                                    <a class="button-secondary" href="{{ route('cliente.dashboard') }}">Ver meus alertas</a>
                                </form>
                            @else
                                <a class="button" href="{{ route('register') }}">Criar conta e ativar alerta</a>
                                <a class="button-secondary" href="{{ route('login') }}">Ja tenho conta</a>
                            @endauth
                        </div>
                    </aside>
                </section>

                <section class="section">
                    <div class="grid">
                        <article class="card">
                            <div class="section-head">
                                <div>
                                    <h2>Ranking de lojas</h2>
                                    <p class="muted">Veja rapidamente como o preço se distribui entre as lojas participantes.</p>
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
                                    <p class="muted">Ofertas ordenadas para reduzir atrito na decisão e acelerar o clique.</p>
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
                <span>Comparação de produto pensada para reduzir atrito, destacar a melhor oferta e levar você mais rápido até a loja certa.</span>
            </footer>
        </div>

        <script>
            (() => {
                const main = document.getElementById('gallery-main');
                const thumbs = Array.from(document.querySelectorAll('[data-gallery-src]'));

                if (!main || thumbs.length === 0) {
                    return;
                }

                thumbs.forEach((thumb) => {
                    thumb.addEventListener('click', () => {
                        main.src = thumb.dataset.gallerySrc;

                        thumbs.forEach((button) => button.classList.remove('is-active'));
                        thumb.classList.add('is-active');
                    });
                });
            })();
        </script>
    </body>
</html>
