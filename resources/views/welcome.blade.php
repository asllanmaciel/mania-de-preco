<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Mania de Preço</title>
        <meta name="description" content="Compare preços reais, descubra onde comprar melhor hoje e encontre ofertas publicadas por lojas ativas perto de você.">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|ibm-plex-mono:400,500" rel="stylesheet" />

        <style>
            :root { --bg:#f7f0e6; --bg2:#efdfcb; --surface:rgba(255,251,245,.82); --line:rgba(66,37,21,.12); --text:#21140f; --muted:#6d5247; --accent:#ff6b2c; --accent2:#0f9f8f; --gold:#d69a27; --shadow:0 28px 80px rgba(60,28,14,.12); --r1:30px; --r2:22px; --r3:16px; --container:1220px; }
            * { box-sizing:border-box; }
            body { margin:0; min-height:100vh; font-family:"Space Grotesk",sans-serif; color:var(--text); background:radial-gradient(circle at top left, rgba(255,107,44,.2), transparent 28%), radial-gradient(circle at 84% 0%, rgba(15,159,143,.18), transparent 24%), linear-gradient(180deg, #fff7eb 0%, var(--bg) 42%, var(--bg2) 100%); }
            a { color:inherit; text-decoration:none; }
            .container { width:min(calc(100% - 32px), var(--container)); margin:0 auto; }
            .topbar, .section-head, .hero-actions, .top-links, .grid-head, .offer-head, .pulse-meta { display:flex; gap:14px; }
            .topbar, .section-head, .grid-head, .offer-head { justify-content:space-between; align-items:flex-start; }
            .topbar { padding:22px 0; align-items:center; }
            .brand { display:inline-flex; align-items:center; gap:14px; font-weight:700; letter-spacing:-.03em; }
            .brand-badge { display:grid; place-items:center; width:44px; height:44px; border-radius:14px; color:#fff7ef; background:linear-gradient(135deg,#ff6b2c 0%,#cf4e1b 100%); box-shadow:0 14px 30px rgba(207,78,27,.28); }
            .top-links { flex-wrap:wrap; justify-content:flex-end; align-items:center; }
            .chip, .badge, .kicker { display:inline-flex; align-items:center; justify-content:center; padding:8px 12px; border-radius:999px; }
            .chip { border:1px solid var(--line); background:rgba(255,255,255,.58); color:var(--muted); font-size:.92rem; }
            .hero, .chart-grid, .offer-grid, .metrics-grid, .trust-grid, .cta-grid { display:grid; gap:16px; }
            .hero { grid-template-columns:1.08fr .92fr; padding:12px 0 22px; }
            .chart-grid { grid-template-columns:repeat(3, minmax(0, 1fr)); }
            .offer-grid { grid-template-columns:repeat(3, minmax(0, 1fr)); }
            .metrics-grid { grid-template-columns:repeat(4, minmax(0, 1fr)); }
            .trust-grid { grid-template-columns:repeat(3, minmax(0, 1fr)); }
            .cta-grid { grid-template-columns:1.08fr .92fr; }
            .card, .hero-card { background:var(--surface); border:1px solid rgba(255,255,255,.62); box-shadow:var(--shadow); backdrop-filter:blur(16px); }
            .hero-card { position:relative; overflow:hidden; padding:38px; border-radius:var(--r1); }
            .hero-card::before { content:""; position:absolute; inset:0; background:linear-gradient(120deg, rgba(255,107,44,.1), transparent 40%), linear-gradient(160deg, transparent 55%, rgba(15,159,143,.12)); pointer-events:none; }
            .card { padding:24px; border-radius:var(--r2); }
            .eyebrow, .kicker { font:400 .82rem "IBM Plex Mono", monospace; text-transform:uppercase; letter-spacing:.08em; }
            .eyebrow { display:inline-flex; align-items:center; gap:10px; padding:8px 12px; border-radius:999px; background:rgba(255,255,255,.76); border:1px solid rgba(255,107,44,.12); color:#6a3b24; }
            .dot { width:8px; height:8px; border-radius:50%; background:var(--accent); box-shadow:0 0 0 8px rgba(255,107,44,.12); }
            h1 { margin:22px 0 14px; font-size:clamp(2.8rem,5vw,5.2rem); line-height:.92; letter-spacing:-.08em; max-width:11ch; }
            h2 { margin:0; font-size:clamp(1.8rem,3vw,2.7rem); letter-spacing:-.06em; }
            h3 { margin:0; font-size:1.18rem; letter-spacing:-.04em; }
            p, .muted, .small { color:var(--muted); line-height:1.72; }
            .hero p { margin:0; max-width:58ch; font-size:1.04rem; }
            .button, .button-secondary { display:inline-flex; align-items:center; justify-content:center; padding:14px 18px; border-radius:16px; font-weight:700; border:1px solid transparent; }
            .button { color:#fff7ef; background:linear-gradient(135deg,#ff6b2c 0%,#d4511d 100%); box-shadow:0 18px 36px rgba(212,81,29,.28); }
            .button-secondary { background:rgba(255,255,255,.72); border-color:var(--line); }
            .hero-actions { margin-top:28px; flex-wrap:wrap; }
            .stats { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:12px; margin-top:30px; }
            .stat, .metric, .offer-price, .mini, .trust-card { padding:16px; border-radius:18px; background:rgba(255,255,255,.74); border:1px solid rgba(255,255,255,.72); }
            .stat strong, .metric strong, .offer-price strong, .mini strong { display:block; margin-bottom:6px; }
            .stat strong { font-size:1.4rem; }
            .metric strong { font-size:1.7rem; letter-spacing:-.05em; }
            .trust-card strong { display:block; margin-bottom:8px; font-size:1.08rem; }
            .pulse-wrap, .offer-list article { padding:16px; border-radius:18px; background:rgba(255,255,255,.72); border:1px solid rgba(76,42,22,.08); }
            .badge { background:rgba(15,159,143,.12); color:#0e6e64; font-size:.82rem; }
            .badge.warm { background:rgba(255,107,44,.12); color:#bf542e; }
            .kicker { background:rgba(255,208,137,.34); color:#704713; }
            .section { padding:18px 0; }
            .section-head { margin-bottom:16px; }
            form.filter { display:grid; grid-template-columns:1.1fr 1fr 1fr 1fr 1fr 1fr auto; gap:12px; align-items:end; }
            label { display:grid; gap:8px; font-size:.92rem; font-weight:600; }
            input, select { width:100%; padding:14px 16px; border-radius:14px; border:1px solid rgba(76,42,22,.12); background:rgba(255,255,255,.94); font:inherit; color:var(--text); }
            .bars, .ranges, .offer-list { display:grid; gap:12px; }
            .bar-meta, .range-meta { display:flex; justify-content:space-between; gap:12px; font-size:.92rem; }
            .track { position:relative; height:12px; border-radius:999px; background:rgba(44,24,17,.08); overflow:hidden; }
            .fill { display:block; height:100%; border-radius:inherit; background:linear-gradient(90deg,var(--accent),#ffb06b); }
            .fill.teal { background:linear-gradient(90deg,var(--accent2),#61e7d9); }
            .fill.gold { background:linear-gradient(90deg,var(--gold),#ffd089); }
            .range-track { position:relative; height:18px; border-radius:999px; background:rgba(44,24,17,.08); }
            .range-line { position:absolute; top:50%; height:8px; transform:translateY(-50%); border-radius:999px; background:linear-gradient(90deg,rgba(255,107,44,.82), rgba(15,159,143,.82)); }
            .range-line::before, .range-line::after { content:""; position:absolute; top:50%; width:12px; height:12px; border-radius:50%; transform:translateY(-50%); background:#fff9f1; }
            .range-line::before { left:-6px; border:3px solid var(--accent); }
            .range-line::after { right:-6px; border:3px solid var(--accent2); }
            .offer-card { display:grid; gap:16px; }
            .offer-media { position:relative; overflow:hidden; min-height:220px; border-radius:20px; background:linear-gradient(135deg, rgba(255,255,255,.86), rgba(255,239,225,.92)); border:1px solid rgba(76,42,22,.08); }
            .offer-media img { width:100%; height:220px; object-fit:cover; display:block; }
            .offer-media .badge { position:absolute; top:14px; left:14px; }
            .offer-top { display:grid; gap:10px; }
            .offer-price-grid { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:12px; }
            .offer-list strong { display:flex; justify-content:space-between; gap:12px; font-size:.98rem; }
            .empty { padding:26px; border-radius:20px; background:rgba(255,255,255,.72); border:1px dashed rgba(76,42,22,.18); color:var(--muted); line-height:1.8; }
            .pagination-wrap nav { display:flex; justify-content:center; }
            .pagination-wrap svg { width:18px; height:18px; }
            .footer { display:flex; justify-content:space-between; gap:14px; align-items:center; padding:30px 0 48px; color:var(--muted); font-size:.92rem; }
            .footer code { padding:4px 8px; border-radius:999px; background:rgba(255,255,255,.76); border:1px solid var(--line); font:400 .82rem "IBM Plex Mono", monospace; }
            @media (max-width:1100px) { .hero, .chart-grid, .offer-grid, .metrics-grid, .trust-grid, .cta-grid, form.filter { grid-template-columns:1fr; } .section-head, .footer { flex-direction:column; align-items:flex-start; } }
            @media (max-width:720px) { .topbar, .top-links, .hero-actions, .section-head, .grid-head, .pulse-meta, .footer { flex-direction:column; align-items:stretch; } .hero-card, .card { padding:20px; } .stats, .offer-price-grid { grid-template-columns:1fr; } .button, .button-secondary, .chip { width:100%; justify-content:center; } .offer-media img { height:190px; } }
        </style>
    </head>
    <body>
        @php
            $categoriaMax = max(1, (float) $categoriaChart->max('total'));
            $cidadeMax = max(1, (float) $cidadeChart->max('total'));
            $spreadMax = max(1, (float) $spreadChart->max('maior'));
        @endphp

        <div class="container">
            <header class="topbar">
                <a class="brand" href="{{ route('home') }}">
                    <span class="brand-badge">MP</span>
                    <span>Mania de Preço</span>
                </a>

                <nav class="top-links">
                    <a class="chip" href="#descoberta">Buscar ofertas</a>
                    <a class="chip" href="#inteligencia">Como economizar</a>
                    <a class="chip" href="#ofertas">Mais procurados</a>
                    <a class="chip" href="{{ route('projeto') }}">Para lojas</a>
                    <a class="chip" href="{{ route('novidades.index') }}">Lançamentos</a>
                    <a class="chip" href="{{ route('suporte') }}">Suporte</a>
                    @auth
                        <a class="chip" href="{{ route('admin.dashboard') }}">Admin</a>
                    @else
                        <a class="chip" href="{{ route('login') }}">Entrar</a>
                    @endauth
                </nav>
            </header>

            <main>
                <section class="hero">
                    <article class="hero-card">
                        <div class="eyebrow"><span class="dot"></span> ofertas reais publicadas por lojas ativas</div>
                        <h1>Compare preços reais e descubra onde vale comprar hoje.</h1>
                        <p>Busque produtos do dia a dia, veja ofertas publicadas por lojas ativas e escolha com mais segurança sem perder tempo abrindo várias abas.</p>

                        <div class="hero-actions">
                            <a class="button" href="#ofertas">Explorar ofertas</a>
                            <a class="button-secondary" href="{{ auth()->check() ? route('admin.dashboard') : route('login') }}">{{ auth()->check() ? 'Abrir painel' : 'Quero anunciar minha loja' }}</a>
                        </div>

                        <div class="stats">
                            <div class="stat"><strong>{{ number_format($totalOfertas, 0, ',', '.') }}</strong><span>ofertas públicas monitoradas</span></div>
                            <div class="stat"><strong>{{ number_format($totalResultados, 0, ',', '.') }}</strong><span>produtos comparáveis nesta busca</span></div>
                            <div class="stat"><strong>R$ {{ number_format($faixaMedia, 2, ',', '.') }}</strong><span>faixa média entre melhor e pior preço</span></div>
                        </div>
                    </article>

                    <aside class="card">
                        <div class="grid-head">
                            <div>
                                <h3>Radar de preços</h3>
                                <p class="muted" style="margin:8px 0 0;">Uma leitura rápida da faixa publicada agora para mostrar onde está a melhor chance de economizar.</p>
                            </div>
                            <span class="badge">ao vivo</span>
                        </div>

                        <div class="pulse-wrap">
                            @if ($pulse['path'] !== '')
                                <svg viewBox="0 0 320 100" width="100%" height="150" preserveAspectRatio="none">
                                    <defs>
                                        <linearGradient id="pulseGradient" x1="0%" x2="100%" y1="0%" y2="0%">
                                            <stop offset="0%" stop-color="#ff6b2c" />
                                            <stop offset="100%" stop-color="#0f9f8f" />
                                        </linearGradient>
                                    </defs>
                                    <path d="{{ $pulse['path'] }}" fill="none" stroke="url(#pulseGradient)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"></path>
                                    @foreach ($pulse['pontos'] as $ponto)
                                        <circle cx="{{ $ponto['x'] }}" cy="{{ $ponto['y'] }}" r="4.5" fill="#fff7f0" stroke="#ff6b2c" stroke-width="2"></circle>
                                    @endforeach
                                </svg>
                            @else
                                <div class="empty">Ainda não há ofertas suficientes para desenhar o pulso do mercado.</div>
                            @endif
                            <div class="pulse-meta">
                                <span>menor oferta: R$ {{ number_format($pulse['menor'], 2, ',', '.') }}</span>
                                <span>maior oferta: R$ {{ number_format($pulse['maior'], 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="metrics-grid">
                            <div class="metric"><strong>{{ number_format($lojasAtivas, 0, ',', '.') }}</strong><span>lojas ativas</span></div>
                            <div class="metric"><strong>{{ $cidade !== '' ? $cidade : 'todas' }}</strong><span>cidade selecionada</span></div>
                            <div class="metric"><strong>{{ $categoriaSlug !== '' ? 'filtrada' : 'aberta' }}</strong><span>leitura por categoria</span></div>
                            <div class="metric"><strong>{{ $ordenar === 'maior_economia' ? 'economia' : ($ordenar === 'mais_ofertas' ? 'volume' : 'preço') }}</strong><span>ranking atual</span></div>
                        </div>
                    </aside>
                </section>

                <section class="section">
                    <div class="section-head">
                        <div>
                            <span class="kicker">confianca</span>
                            <h2>Mais clareza para decidir, menos dúvida antes do clique.</h2>
                        </div>
                        <p class="muted">A experiência foi desenhada para fazer a comparação parecer simples, confiável e rápida já no primeiro contato.</p>
                    </div>

                    <div class="trust-grid">
                        <article class="trust-card">
                            <strong>Preços publicados por lojas ativas</strong>
                            <span class="small">Você não cai em uma lista genérica. Cada oferta aparece ligada a uma loja real, com contexto de cidade e condições de pagamento.</span>
                        </article>
                        <article class="trust-card">
                            <strong>Comparação feita para celular</strong>
                            <span class="small">O caminho até a melhor oferta foi pensado para toque, leitura rápida e menos rolagem, especialmente no mobile.</span>
                        </article>
                        <article class="trust-card">
                            <strong>Economia visível sem esforço</strong>
                            <span class="small">Melhor valor, diferença entre ofertas e atalhos para lojas ficam próximos para reduzir atrito e acelerar a escolha.</span>
                        </article>
                    </div>
                </section>

                <section class="section" id="descoberta">
                    <div class="section-head">
                        <div>
                            <span class="kicker">descoberta</span>
                            <h2>Encontre mais rápido o que cabe no seu bolso.</h2>
                        </div>
                        <p class="muted">Filtros simples, comparação direta e menos fricção para sair da busca e chegar na decisão.</p>
                    </div>

                    <article class="card">
                        <form class="filter" method="GET" action="{{ route('home') }}">
                            <label>Buscar produto
                                <input type="text" name="busca" value="{{ $busca }}" placeholder="Cafe, arroz, shampoo...">
                            </label>
                            <label>Categoria
                                <select name="categoria">
                                    <option value="">Todas</option>
                                    @foreach ($categorias as $categoria)
                                        <option value="{{ $categoria->slug }}" @selected($categoriaSlug === $categoria->slug)>{{ $categoria->nome }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label>Cidade
                                <select name="cidade">
                                    <option value="">Todas</option>
                                    @foreach ($cidades as $cidadeOption)
                                        <option value="{{ $cidadeOption }}" @selected($cidade === $cidadeOption)>{{ $cidadeOption }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label>Pagamento
                                <select name="tipo_preco">
                                    <option value="">Todos</option>
                                    @foreach ($tiposPreco as $tipoOption)
                                        <option value="{{ $tipoOption }}" @selected($tipoPreco === $tipoOption)>{{ ucfirst(str_replace('_', ' ', $tipoOption)) }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label>Preço máximo
                                <input type="number" step="0.01" min="0" name="preco_ate" value="{{ $precoAte ?? '' }}" placeholder="Ex: 25.90">
                            </label>
                            <label>Ordenar por
                                <select name="ordenar">
                                    <option value="menor_preco" @selected($ordenar === 'menor_preco')>Menor preço</option>
                                    <option value="maior_economia" @selected($ordenar === 'maior_economia')>Maior economia</option>
                                    <option value="mais_ofertas" @selected($ordenar === 'mais_ofertas')>Mais ofertas</option>
                                    <option value="alfabetica" @selected($ordenar === 'alfabetica')>Ordem alfabética</option>
                                </select>
                            </label>
                            <div class="hero-actions" style="margin:0;">
                                <button class="button" type="submit">Aplicar</button>
                                @if ($busca !== '' || $categoriaSlug !== '' || $cidade !== '' || $tipoPreco !== '' || $precoAte !== null || $ordenar !== 'menor_preco')
                                    <a class="button-secondary" href="{{ route('home') }}">Limpar</a>
                                @endif
                            </div>
                        </form>
                    </article>
                </section>

                <section class="section" id="inteligencia">
                    <div class="section-head">
                        <div>
                            <span class="kicker">inteligencia</span>
                            <h2>Menos rolagem, mais decisão.</h2>
                        </div>
                        <p class="muted">Os gráficos ajudam você a enxergar concentração de ofertas, cidades mais ativas e o espaço real para economizar.</p>
                    </div>

                    <div class="chart-grid">
                        <article class="card">
                            <div class="grid-head"><h3>Concentração por categoria</h3></div>
                            @if ($categoriaChart->isEmpty())
                                <div class="empty">Ainda não há dados suficientes para a leitura por categoria.</div>
                            @else
                                <div class="bars">
                                    @foreach ($categoriaChart as $item)
                                        <div>
                                            <div class="bar-meta"><span>{{ $item['nome'] }}</span><span>{{ $item['total'] }} ofertas</span></div>
                                            <div class="track"><span class="fill" style="width: {{ min(100, ($item['total'] / $categoriaMax) * 100) }}%;"></span></div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </article>

                        <article class="card">
                            <div class="grid-head"><h3>Densidade por cidade</h3></div>
                            @if ($cidadeChart->isEmpty())
                                <div class="empty">Nenhuma cidade encontrada para o recorte atual.</div>
                            @else
                                <div class="bars">
                                    @foreach ($cidadeChart as $item)
                                        <div>
                                            <div class="bar-meta"><span>{{ $item['nome'] }}</span><span>{{ $item['total'] }} ofertas</span></div>
                                            <div class="track"><span class="fill teal" style="width: {{ min(100, ($item['total'] / $cidadeMax) * 100) }}%;"></span></div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </article>

                        <article class="card">
                            <div class="grid-head"><h3>Amplitude de preço</h3></div>
                            @if ($spreadChart->isEmpty())
                                <div class="empty">Não há comparação suficiente para mostrar amplitude de preço.</div>
                            @else
                                <div class="ranges">
                                    @foreach ($spreadChart as $item)
                                        @php
                                            $left = ($item['menor'] / $spreadMax) * 100;
                                            $width = max(3, (($item['maior'] - $item['menor']) / $spreadMax) * 100);
                                        @endphp
                                        <div>
                                            <div class="range-meta"><span>{{ $item['nome'] }}</span><span>R$ {{ number_format($item['economia'], 2, ',', '.') }}</span></div>
                                            <div class="range-track"><span class="range-line" style="left: {{ $left }}%; width: {{ min(100 - $left, $width) }}%;"></span></div>
                                            <div class="bar-meta"><span>R$ {{ number_format($item['menor'], 2, ',', '.') }}</span><span>R$ {{ number_format($item['maior'], 2, ',', '.') }}</span></div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    </div>
                </section>

                <section class="section" id="ofertas">
                    <div class="section-head">
                        <div>
                            <span class="kicker">ofertas</span>
                            <h2>Ofertas que já merecem seu clique.</h2>
                        </div>
                        <p class="muted">Veja o melhor valor, a economia possível e a loja que está puxando o preço para baixo em cada item.</p>
                    </div>

                    @if ($produtos->isEmpty())
                        <div class="empty">Nenhuma oferta encontrada para os filtros atuais. Vale limpar a busca e abrir mais o recorte para descobrir novas oportunidades.</div>
                    @else
                        <div class="offer-grid">
                            @foreach ($produtos as $produto)
                                @php
                                    $menorPreco = (float) ($produto->menor_preco ?? 0);
                                    $maiorPreco = (float) ($produto->maior_preco ?? 0);
                                    $economia = max(0, $maiorPreco - $menorPreco);
                                @endphp
                                <article class="card offer-card">
                                    <div class="offer-media">
                                        <img src="{{ $produto->imagem_url }}" alt="{{ $produto->nome }}">
                                        <span class="badge warm">{{ $produto->categoria?->nome ?? 'Sem categoria' }}</span>
                                    </div>

                                    <div class="offer-head">
                                        <div class="offer-top">
                                            <h3><a href="{{ route('produtos.public.show', $produto) }}">{{ $produto->nome }}</a></h3>
                                        <span class="small">{{ $produto->marca?->nome ?? 'Marca não informada' }}</span>
                                    </div>
                                    <span class="badge">{{ $produto->precos_count }} ofertas</span>
                                </div>

                                    <div class="offer-price-grid">
                                        <div class="offer-price"><span class="small">melhor preço</span><strong>R$ {{ number_format($menorPreco, 2, ',', '.') }}</strong></div>
                                        <div class="offer-price"><span class="small">economia possível</span><strong>R$ {{ number_format($economia, 2, ',', '.') }}</strong></div>
                                    </div>

                                    <div class="offer-list">
                                        @foreach ($produto->melhores_ofertas as $oferta)
                                            <article>
                                                <strong>
                                                    <a href="{{ route('lojas.public.show', $oferta->loja) }}">{{ $oferta->loja?->nome ?? 'Loja não informada' }}</a>
                                                    <span>R$ {{ number_format((float) $oferta->preco, 2, ',', '.') }}</span>
                                                </strong>
                                                <small>{{ $oferta->loja?->cidade ?? 'Sem cidade' }} @if ($oferta->loja?->uf) / {{ $oferta->loja->uf }} @endif - {{ ucfirst(str_replace('_', ' ', $oferta->tipo_preco)) }}</small>
                                            </article>
                                        @endforeach
                                    </div>

                                    @if ($produto->precos_count > 1)
                                        <div class="track"><span class="fill gold" style="width: {{ min(100, ($economia / max(0.01, $maiorPreco)) * 100) }}%;"></span></div>
                                    @endif

                                    <a class="button-secondary" href="{{ route('produtos.public.show', $produto) }}">Abrir comparativo completo</a>
                                </article>
                            @endforeach
                        </div>

                        <div class="pagination-wrap" style="margin-top:20px;">{{ $produtos->links() }}</div>
                    @endif
                </section>

                <section class="section">
                    <article class="hero-card">
                        <div class="cta-grid">
                            <div>
                                <span class="kicker">para lojas</span>
                                <h2 style="margin-top:14px;">Quer colocar sua loja na frente de quem já está comparando?</h2>
                                <p>Publique ofertas, ganhe presença nas buscas e organize sua operação em uma experiência que já nasce pensada para celular e para conversão.</p>
                            </div>
                            <div class="card" style="background:rgba(255,255,255,.58);">
                                <div class="mini" style="background:rgba(255,255,255,.82);">
                                    <strong>Mais visibilidade</strong>
                                    <span>Suas ofertas entram em páginas de produto e comparativos prontos para converter interesse em visita.</span>
                                </div>
                                <div class="hero-actions" style="margin-top:16px;">
                                    <a class="button" href="{{ auth()->check() ? route('admin.dashboard') : route('login') }}">{{ auth()->check() ? 'Abrir meu painel' : 'Começar com minha loja' }}</a>
                                    <a class="button-secondary" href="{{ route('projeto') }}">Ver recursos para lojas</a>
                                </div>
                            </div>
                        </div>
                    </article>
                </section>
            </main>

            <footer class="footer">
                <span>Ofertas reais, comparação clara e descoberta rápida para quem quer comprar melhor.</span>
                <span>
                    <a href="{{ route('termos') }}">Termos</a>
                    · <a href="{{ route('privacidade') }}">Privacidade</a>
                    · <a href="{{ route('suporte') }}">Suporte</a>
                </span>
            </footer>
        </div>
    </body>
</html>
