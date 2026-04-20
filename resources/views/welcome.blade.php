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
            :root { --bg:#f7f0e6; --bg2:#efdfcb; --surface:rgba(255,251,245,.82); --line:rgba(66,37,21,.12); --text:#21140f; --muted:#6d5247; --accent:#ff6b2c; --accent2:#0f9f8f; --gold:#d69a27; --green:#19b789; --red:#ef5b35; --ink:#130b08; --shadow:0 28px 80px rgba(60,28,14,.12); --r1:30px; --r2:22px; --r3:16px; --container:1220px; }
            * { box-sizing:border-box; }
            body { margin:0; min-height:100vh; font-family:"Space Grotesk",sans-serif; color:var(--text); background:radial-gradient(circle at top left, rgba(255,107,44,.2), transparent 28%), radial-gradient(circle at 84% 0%, rgba(15,159,143,.18), transparent 24%), linear-gradient(180deg, #fff7eb 0%, var(--bg) 42%, var(--bg2) 100%); }
            a { color:inherit; text-decoration:none; }
            .container { width:min(calc(100% - 32px), var(--container)); margin:0 auto; }
            .topbar, .section-head, .hero-actions, .top-links, .grid-head, .offer-head, .pulse-meta, .nav-links, .top-actions, .market-status, .ticker-row, .signal-row { display:flex; gap:14px; }
            .topbar, .section-head, .grid-head, .offer-head { justify-content:space-between; align-items:flex-start; }
            .topbar { position:sticky; top:14px; z-index:20; margin:16px 0 20px; padding:12px; align-items:center; border:1px solid rgba(255,255,255,.72); border-radius:24px; background:rgba(255,251,245,.72); box-shadow:0 18px 50px rgba(72,38,19,.1); backdrop-filter:blur(18px); }
            .brand { display:inline-flex; align-items:center; gap:14px; font-weight:700; letter-spacing:-.03em; }
            .brand-badge { display:grid; place-items:center; width:44px; height:44px; border-radius:14px; color:#fff7ef; background:linear-gradient(135deg,#ff6b2c 0%,#cf4e1b 100%); box-shadow:0 14px 30px rgba(207,78,27,.28); }
            .brand-copy { display:grid; gap:2px; }
            .brand-copy small { color:var(--muted); font-weight:500; letter-spacing:0; }
            .top-links { flex-wrap:wrap; justify-content:flex-end; align-items:center; }
            .nav-links { align-items:center; justify-content:center; flex:1; padding:6px; border-radius:18px; background:rgba(255,255,255,.48); border:1px solid rgba(72,38,19,.08); }
            .nav-link, .chip, .badge, .kicker { display:inline-flex; align-items:center; justify-content:center; padding:8px 12px; border-radius:999px; }
            .nav-link { color:#5e463b; font-size:.92rem; font-weight:600; transition:background .2s ease, color .2s ease, transform .2s ease; }
            .nav-link:hover { color:var(--ink); background:rgba(255,255,255,.82); transform:translateY(-1px); }
            .top-actions { align-items:center; justify-content:flex-end; }
            .chip { border:1px solid var(--line); background:rgba(255,255,255,.58); color:var(--muted); font-size:.92rem; }
            .chip.primary { color:#fff7ef; border-color:transparent; background:linear-gradient(135deg,#ff6b2c 0%,#d4511d 100%); box-shadow:0 14px 28px rgba(212,81,29,.22); }
            .hero, .chart-grid, .offer-grid, .metrics-grid, .trust-grid, .cta-grid { display:grid; gap:16px; }
            .hero { grid-template-columns:1.02fr .98fr; padding:10px 0 22px; align-items:stretch; }
            .chart-grid { grid-template-columns:repeat(3, minmax(0, 1fr)); }
            .offer-grid { grid-template-columns:repeat(3, minmax(0, 1fr)); }
            .metrics-grid { grid-template-columns:repeat(4, minmax(0, 1fr)); }
            .trust-grid { grid-template-columns:repeat(3, minmax(0, 1fr)); }
            .cta-grid { grid-template-columns:1.08fr .92fr; }
            .card, .hero-card { background:var(--surface); border:1px solid rgba(255,255,255,.62); box-shadow:var(--shadow); backdrop-filter:blur(16px); }
            .hero-card { position:relative; overflow:hidden; padding:38px; border-radius:var(--r1); }
            .hero-card::before { content:""; position:absolute; inset:0; background:linear-gradient(120deg, rgba(255,107,44,.1), transparent 40%), linear-gradient(160deg, transparent 55%, rgba(15,159,143,.12)); pointer-events:none; }
            .hero-card.featured { min-height:628px; display:flex; flex-direction:column; justify-content:space-between; border-color:rgba(255,255,255,.74); background:linear-gradient(145deg,rgba(255,251,245,.92),rgba(255,247,235,.78)); }
            .hero-card.featured::after { content:""; position:absolute; width:320px; height:320px; right:-110px; bottom:-120px; border-radius:50%; background:radial-gradient(circle, rgba(25,183,137,.18), transparent 66%); pointer-events:none; }
            .hero-copy, .hero-card .stats { position:relative; z-index:1; }
            .card { padding:24px; border-radius:var(--r2); }
            .eyebrow, .kicker { font:400 .82rem "IBM Plex Mono", monospace; text-transform:uppercase; letter-spacing:.08em; }
            .eyebrow { display:inline-flex; align-items:center; gap:10px; padding:8px 12px; border-radius:999px; background:rgba(255,255,255,.76); border:1px solid rgba(255,107,44,.12); color:#6a3b24; }
            .dot { width:8px; height:8px; border-radius:50%; background:var(--accent); box-shadow:0 0 0 8px rgba(255,107,44,.12); }
            h1 { margin:22px 0 14px; font-size:clamp(2.8rem,5vw,5.6rem); line-height:.9; letter-spacing:-.085em; max-width:10.8ch; }
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
            .market-card { position:relative; overflow:hidden; min-height:628px; padding:24px; border-radius:var(--r1); background:linear-gradient(180deg,rgba(25,21,18,.96),rgba(43,29,20,.94)); color:#fff8ef; border:1px solid rgba(255,255,255,.16); box-shadow:0 28px 80px rgba(24,13,8,.22); }
            .market-card::before { content:""; position:absolute; inset:-1px; background:radial-gradient(circle at 84% 10%, rgba(25,183,137,.24), transparent 28%), radial-gradient(circle at 4% 0%, rgba(255,107,44,.2), transparent 22%); pointer-events:none; }
            .market-card > * { position:relative; z-index:1; }
            .market-card h3 { color:#fffaf4; font-size:1.35rem; }
            .market-card .muted, .market-card .small { color:rgba(255,248,239,.68); }
            .market-status { align-items:center; justify-content:space-between; margin-bottom:18px; }
            .live-badge { display:inline-flex; align-items:center; gap:8px; padding:8px 11px; border-radius:999px; color:#d9fff5; background:rgba(25,183,137,.13); border:1px solid rgba(25,183,137,.26); font:500 .78rem "IBM Plex Mono", monospace; text-transform:uppercase; letter-spacing:.08em; }
            .live-badge::before { content:""; width:8px; height:8px; border-radius:50%; background:var(--green); box-shadow:0 0 0 0 rgba(25,183,137,.55); animation:livePulse 1.8s infinite; }
            .market-terminal { overflow:hidden; margin-top:18px; border-radius:24px; background:linear-gradient(180deg,rgba(255,255,255,.1),rgba(255,255,255,.04)); border:1px solid rgba(255,255,255,.12); }
            .terminal-head { display:flex; justify-content:space-between; gap:12px; padding:14px 16px; color:rgba(255,248,239,.62); border-bottom:1px solid rgba(255,255,255,.1); font:500 .78rem "IBM Plex Mono", monospace; text-transform:uppercase; letter-spacing:.08em; }
            .market-chart { position:relative; padding:12px 10px 0; min-height:230px; }
            .market-chart::after { content:""; position:absolute; inset:0; background:linear-gradient(90deg, transparent, rgba(255,255,255,.08), transparent); transform:translateX(-100%); animation:radarSweep 4.8s linear infinite; pointer-events:none; }
            .market-chart svg { display:block; width:100%; height:228px; overflow:visible; }
            .market-line { stroke-dasharray:660; stroke-dashoffset:660; animation:drawLine 2s ease forwards, floatLine 6s ease-in-out infinite 2s; }
            .market-area { opacity:.24; }
            .chart-point { animation:pointGlow 2.2s ease-in-out infinite; }
            .pulse-meta.market { justify-content:space-between; padding:0 16px 16px; color:rgba(255,248,239,.72); font:500 .82rem "IBM Plex Mono", monospace; }
            .market-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; margin-top:14px; }
            .market-metric { min-height:104px; padding:16px; border-radius:20px; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.1); }
            .market-metric strong { display:block; margin-bottom:8px; color:#fffaf4; font-size:1.62rem; letter-spacing:-.05em; }
            .ticker-board { display:grid; gap:10px; margin-top:14px; }
            .ticker-row { position:relative; align-items:center; justify-content:space-between; gap:12px; padding:12px 14px; border-radius:18px; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.1); overflow:hidden; }
            .ticker-row::before { content:""; position:absolute; inset:0; width:var(--ticker-width, 12%); background:linear-gradient(90deg,rgba(25,183,137,.18),transparent); pointer-events:none; }
            .ticker-name { display:grid; gap:3px; min-width:0; }
            .ticker-name strong { overflow:hidden; color:#fffaf4; white-space:nowrap; text-overflow:ellipsis; }
            .ticker-value { text-align:right; font:500 .84rem "IBM Plex Mono", monospace; color:#bcfff0; }
            .signal-row { align-items:center; justify-content:space-between; margin-top:14px; padding:12px 14px; border-radius:18px; color:#ffe8d9; background:rgba(255,107,44,.11); border:1px solid rgba(255,107,44,.18); }
            .signal-row strong { color:#fffaf4; }
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
            @keyframes livePulse { 70% { box-shadow:0 0 0 10px rgba(25,183,137,0); } 100% { box-shadow:0 0 0 0 rgba(25,183,137,0); } }
            @keyframes radarSweep { to { transform:translateX(100%); } }
            @keyframes drawLine { to { stroke-dashoffset:0; } }
            @keyframes floatLine { 50% { transform:translateY(-3px); } }
            @keyframes pointGlow { 50% { filter:drop-shadow(0 0 9px rgba(25,183,137,.85)); } }
            @media (max-width:1100px) { .hero, .chart-grid, .offer-grid, .metrics-grid, .trust-grid, .cta-grid, form.filter { grid-template-columns:1fr; } .section-head, .footer { flex-direction:column; align-items:flex-start; } .hero-card.featured, .market-card { min-height:auto; } .nav-links { order:3; width:100%; overflow-x:auto; justify-content:flex-start; } }
            @media (max-width:720px) { .topbar, .top-links, .hero-actions, .section-head, .grid-head, .pulse-meta, .footer, .top-actions { flex-direction:column; align-items:stretch; } .topbar { position:static; border-radius:20px; } .brand-copy small { display:none; } .hero-card, .card, .market-card { padding:20px; } .stats, .offer-price-grid, .market-grid { grid-template-columns:1fr; } .button, .button-secondary, .chip { width:100%; justify-content:center; } .nav-link { white-space:nowrap; } .offer-media img { height:190px; } }
        </style>
    </head>
    <body>
        @php
            $categoriaMax = max(1, (float) $categoriaChart->max('total'));
            $cidadeMax = max(1, (float) $cidadeChart->max('total'));
            $spreadMax = max(1, (float) $spreadChart->max('maior'));
            $radarMax = max(0.01, (float) $radarMercado->max('economia'));
            $ultimoPonto = collect($pulse['pontos'])->last();
            $pulseArea = $pulse['path'] !== '' && $ultimoPonto ? $pulse['path'] . ' L' . $ultimoPonto['x'] . ',100 L0,100 Z' : '';
        @endphp

        <div class="container">
            <header class="topbar">
                <a class="brand" href="{{ route('home') }}">
                    <span class="brand-badge">MP</span>
                    <span class="brand-copy">
                        <span>Mania de Preço</span>
                        <small>comparador local inteligente</small>
                    </span>
                </a>

                <nav class="nav-links" aria-label="Navegação principal">
                    <a class="nav-link" href="#descoberta">Buscar</a>
                    <a class="nav-link" href="#inteligencia">Radar</a>
                    <a class="nav-link" href="#ofertas">Ofertas</a>
                    <a class="nav-link" href="{{ route('projeto') }}">Para lojas</a>
                    <a class="nav-link" href="{{ route('novidades.index') }}">Novidades</a>
                    <a class="nav-link" href="{{ route('suporte') }}">Suporte</a>
                </nav>

                <div class="top-actions">
                    @auth
                        <a class="chip primary" href="{{ route('admin.dashboard') }}">Abrir painel</a>
                    @else
                        <a class="chip" href="{{ route('login') }}">Entrar</a>
                        <a class="chip primary" href="{{ route('login') }}">Anunciar loja</a>
                    @endauth
                </div>
            </header>

            <main>
                <section class="hero">
                    <article class="hero-card featured">
                        <div class="hero-copy">
                            <div class="eyebrow"><span class="dot"></span> ofertas reais publicadas por lojas ativas</div>
                            <h1>O jeito mais rápido de saber onde comprar melhor hoje.</h1>
                            <p>Compare preços reais, veja a loja que está puxando o valor para baixo e descubra oportunidades antes de perder tempo abrindo várias abas.</p>

                            <div class="hero-actions">
                                <a class="button" href="#ofertas">Ver melhores ofertas</a>
                                <a class="button-secondary" href="#descoberta">Filtrar minha busca</a>
                            </div>
                        </div>

                        <div class="stats">
                            <div class="stat"><strong>{{ number_format($totalOfertas, 0, ',', '.') }}</strong><span>ofertas em leitura</span></div>
                            <div class="stat"><strong>{{ number_format($totalResultados, 0, ',', '.') }}</strong><span>produtos comparáveis</span></div>
                            <div class="stat"><strong>R$ {{ number_format($faixaMedia, 2, ',', '.') }}</strong><span>média de diferença encontrada</span></div>
                        </div>
                    </article>

                    <aside class="market-card">
                        <div class="market-status">
                            <div>
                                <h3>Radar de preços</h3>
                                <p class="muted" style="margin:8px 0 0;">Um painel vivo da vitrine publicada para enxergar quedas, faixas e oportunidades com leitura de mercado.</p>
                            </div>
                            <span class="live-badge">atualizando</span>
                        </div>

                        <div class="market-terminal">
                            <div class="terminal-head">
                                <span>índice de ofertas</span>
                                <span>{{ now()->format('H:i') }} BRT</span>
                            </div>

                            @if ($pulse['path'] !== '')
                                <div class="market-chart">
                                    <svg viewBox="0 0 320 100" preserveAspectRatio="none" aria-label="Variação de preços publicados">
                                        <defs>
                                            <linearGradient id="pulseGradient" x1="0%" x2="100%" y1="0%" y2="0%">
                                                <stop offset="0%" stop-color="#ff6b2c" />
                                                <stop offset="52%" stop-color="#ffd089" />
                                                <stop offset="100%" stop-color="#19b789" />
                                            </linearGradient>
                                            <linearGradient id="areaGradient" x1="0%" x2="0%" y1="0%" y2="100%">
                                                <stop offset="0%" stop-color="#19b789" />
                                                <stop offset="100%" stop-color="#19b789" stop-opacity="0" />
                                            </linearGradient>
                                        </defs>
                                        <path d="M0,25 L320,25 M0,50 L320,50 M0,75 L320,75" fill="none" stroke="rgba(255,255,255,.12)" stroke-width="1"></path>
                                        <path class="market-area" d="{{ $pulseArea }}" fill="url(#areaGradient)"></path>
                                        <path class="market-line" d="{{ $pulse['path'] }}" fill="none" stroke="url(#pulseGradient)" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        @foreach ($pulse['pontos'] as $ponto)
                                            <circle class="chart-point" cx="{{ $ponto['x'] }}" cy="{{ $ponto['y'] }}" r="4.5" fill="#14100d" stroke="#bcfff0" stroke-width="2"></circle>
                                        @endforeach
                                    </svg>
                                </div>
                            @else
                                <div class="empty">Ainda não há ofertas suficientes para desenhar o pulso do mercado.</div>
                            @endif
                            <div class="pulse-meta market">
                                <span>mín. R$ {{ number_format($pulse['menor'], 2, ',', '.') }}</span>
                                <span>máx. R$ {{ number_format($pulse['maior'], 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="market-grid">
                            <div class="market-metric"><strong>{{ number_format($lojasAtivas, 0, ',', '.') }}</strong><span class="small">lojas ativas no recorte</span></div>
                            <div class="market-metric"><strong>{{ $ordenar === 'maior_economia' ? 'economia' : ($ordenar === 'mais_ofertas' ? 'volume' : 'preço') }}</strong><span class="small">sinal que ordena o painel</span></div>
                        </div>

                        @if ($radarMercado->isNotEmpty())
                            <div class="ticker-board" aria-label="Oportunidades em destaque">
                                @foreach ($radarMercado as $item)
                                    <div class="ticker-row" style="--ticker-width: {{ min(100, ($item['economia'] / $radarMax) * 100) }}%;">
                                        <div class="ticker-name">
                                            <strong>{{ $item['produto'] }}</strong>
                                            <span class="small">{{ $item['loja'] }} · {{ $item['cidade'] }}</span>
                                        </div>
                                        <div class="ticker-value">
                                            <span>{{ $item['sinal'] }}</span><br>
                                            <strong>-{{ number_format($item['variacao'], 1, ',', '.') }}%</strong>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="signal-row">
                            <span class="small">Janela de economia</span>
                            <strong>até R$ {{ number_format((float) $radarMercado->max('economia'), 2, ',', '.') }}</strong>
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
