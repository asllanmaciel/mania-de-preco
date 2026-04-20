<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Mania de Preço</title>
        <meta name="description" content="Compare preços reais, descubra onde comprar melhor hoje e encontre ofertas publicadas por lojas ativas perto de você.">
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="apple-touch-icon" href="{{ asset('images/brand/mania-de-preco-mark.svg') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:700,800,900|plus-jakarta-sans:400,500,600,700,800|ibm-plex-mono:400,500" rel="stylesheet" />

        <style>
            :root { --bg:#f5ead9; --bg2:#e5cfb5; --paper:#fff8ed; --surface:rgba(255,250,241,.86); --surface-strong:rgba(255,255,255,.92); --line:rgba(58,35,24,.13); --text:#21140f; --muted:#71584b; --accent:#f45a24; --accent2:#0b8f80; --gold:#d69a27; --green:#19b789; --red:#ef5b35; --ink:#130b08; --display:"Fraunces", serif; --body:"Plus Jakarta Sans", sans-serif; --mono:"IBM Plex Mono", monospace; --shadow:0 28px 80px rgba(60,28,14,.13); --shadow-strong:0 38px 100px rgba(44,24,17,.2); --r1:34px; --r2:24px; --r3:16px; --container:1220px; }
            * { box-sizing:border-box; }
            body { position:relative; margin:0; min-height:100vh; font-family:var(--body); color:var(--text); background:radial-gradient(circle at 9% 8%, rgba(244,90,36,.22), transparent 28%), radial-gradient(circle at 88% 2%, rgba(11,143,128,.18), transparent 26%), linear-gradient(135deg, #fff7eb 0%, var(--bg) 44%, var(--bg2) 100%); -webkit-font-smoothing:antialiased; text-rendering:geometricPrecision; }
            body::before { content:""; position:fixed; inset:0; z-index:-1; pointer-events:none; background-image:linear-gradient(rgba(33,20,15,.045) 1px, transparent 1px), linear-gradient(90deg, rgba(33,20,15,.04) 1px, transparent 1px), radial-gradient(circle at 20% 18%, rgba(255,255,255,.65), transparent 28%); background-size:44px 44px, 44px 44px, auto; mask-image:linear-gradient(180deg, #000 0%, rgba(0,0,0,.7) 48%, transparent 100%); }
            a { color:inherit; text-decoration:none; }
            .container { width:min(calc(100% - 32px), var(--container)); margin:0 auto; }
            .topbar, .section-head, .hero-actions, .top-links, .grid-head, .offer-head, .pulse-meta, .nav-links, .top-actions, .market-status, .ticker-row, .signal-row { display:flex; gap:14px; }
            .topbar, .section-head, .grid-head, .offer-head { justify-content:space-between; align-items:flex-start; }
            .topbar { position:sticky; top:14px; z-index:20; margin:16px 0 22px; padding:10px; align-items:center; border:1px solid rgba(255,255,255,.76); border-radius:28px; background:linear-gradient(135deg,rgba(255,252,246,.82),rgba(255,245,232,.68)); box-shadow:0 18px 50px rgba(72,38,19,.1); backdrop-filter:blur(22px); }
            .brand { display:inline-flex; align-items:center; gap:14px; padding:6px 10px 6px 6px; border-radius:20px; font-weight:700; letter-spacing:-.03em; transition:background .2s ease, transform .2s ease; }
            .brand:hover { background:rgba(255,255,255,.5); transform:translateY(-1px); }
            .brand-badge { display:grid; place-items:center; width:44px; height:44px; border-radius:14px; color:#fff7ef; background:linear-gradient(135deg,#ff6b2c 0%,#cf4e1b 100%); box-shadow:0 14px 30px rgba(207,78,27,.28); }
            .brand-mark { width:44px; height:44px; border-radius:14px; box-shadow:0 14px 30px rgba(207,78,27,.22); }
            .brand-logo { width:182px; height:auto; display:block; filter:drop-shadow(0 10px 22px rgba(50,24,13,.1)); }
            .brand-copy { display:grid; gap:2px; }
            .brand-copy small { color:var(--muted); font-size:.78rem; font-weight:600; letter-spacing:.01em; }
            .top-links { flex-wrap:wrap; justify-content:flex-end; align-items:center; }
            .nav-links { align-items:center; justify-content:center; flex:1; padding:6px; border-radius:20px; background:rgba(255,255,255,.54); border:1px solid rgba(72,38,19,.08); }
            .nav-link, .chip, .badge, .kicker { display:inline-flex; align-items:center; justify-content:center; padding:8px 12px; border-radius:999px; }
            .nav-link { color:#5e463b; font-size:.9rem; font-weight:700; letter-spacing:-.01em; transition:background .2s ease, color .2s ease, transform .2s ease; }
            .nav-link:hover { color:var(--ink); background:rgba(255,255,255,.82); transform:translateY(-1px); }
            .top-actions { align-items:center; justify-content:flex-end; }
            .chip { border:1px solid var(--line); background:rgba(255,255,255,.64); color:var(--muted); font-size:.9rem; font-weight:700; }
            .chip.primary { color:#fff7ef; border-color:transparent; background:linear-gradient(135deg,#f45a24 0%,#ba3c16 100%); box-shadow:0 14px 28px rgba(212,81,29,.24); }
            .hero, .chart-grid, .offer-grid, .metrics-grid, .trust-grid, .cta-grid { display:grid; gap:16px; }
            .hero { grid-template-columns:1fr; padding:18px 0 18px; align-items:stretch; }
            .chart-grid { grid-template-columns:repeat(3, minmax(0, 1fr)); }
            .offer-grid { grid-template-columns:repeat(3, minmax(0, 1fr)); }
            .metrics-grid { grid-template-columns:repeat(4, minmax(0, 1fr)); }
            .trust-grid { grid-template-columns:repeat(3, minmax(0, 1fr)); }
            .cta-grid { grid-template-columns:1.08fr .92fr; }
            .card, .hero-card { background:var(--surface); border:1px solid rgba(255,255,255,.64); box-shadow:var(--shadow); backdrop-filter:blur(18px); }
            .hero-card { position:relative; overflow:hidden; padding:38px; border-radius:var(--r1); }
            .hero-card::before { content:""; position:absolute; inset:0; background:linear-gradient(120deg, rgba(244,90,36,.11), transparent 42%), linear-gradient(160deg, transparent 50%, rgba(11,143,128,.14)); pointer-events:none; }
            .hero-card.featured { min-height:610px; display:flex; flex-direction:column; justify-content:space-between; border-color:rgba(255,255,255,.82); background:linear-gradient(145deg,rgba(255,252,245,.95),rgba(255,244,230,.74)); box-shadow:var(--shadow-strong); }
            .hero-card.featured::after { content:""; position:absolute; width:380px; height:380px; right:-120px; bottom:-140px; border-radius:50%; background:radial-gradient(circle, rgba(11,143,128,.2), transparent 64%); pointer-events:none; }
            .hero-watermark { position:absolute; right:28px; top:28px; max-width:360px; color:rgba(33,20,15,.055); font:900 clamp(4rem,9vw,9.6rem)/.75 var(--display); letter-spacing:-.11em; text-align:right; pointer-events:none; }
            .hero-orbit { position:absolute; right:42px; bottom:42px; width:min(34vw,360px); aspect-ratio:1; border-radius:50%; border:1px solid rgba(33,20,15,.08); background:radial-gradient(circle, rgba(255,255,255,.56) 0 28%, rgba(255,255,255,.12) 29% 100%); opacity:.92; pointer-events:none; }
            .hero-orbit::before, .hero-orbit::after { content:""; position:absolute; border-radius:999px; background:linear-gradient(135deg,rgba(244,90,36,.92),rgba(255,176,107,.86)); box-shadow:0 18px 34px rgba(205,83,35,.2); }
            .hero-orbit::before { width:112px; height:112px; top:22px; left:18px; }
            .hero-orbit::after { width:78px; height:78px; right:28px; bottom:44px; background:linear-gradient(135deg,rgba(11,143,128,.9),rgba(97,231,217,.78)); }
            .hero-copy, .hero-card .stats, .hero-proof { position:relative; z-index:1; }
            .hero-copy { max-width:900px; }
            .hero-card.featured .stats { max-width:820px; }
            .hero-proof { display:flex; flex-wrap:wrap; gap:10px; margin-top:22px; }
            .hero-proof span { display:inline-flex; align-items:center; gap:8px; padding:9px 12px; border-radius:999px; color:#5b3827; background:rgba(255,255,255,.58); border:1px solid rgba(58,35,24,.1); font-size:.88rem; font-weight:800; }
            .hero-proof span::before { content:""; width:7px; height:7px; border-radius:50%; background:var(--accent2); box-shadow:0 0 0 5px rgba(11,143,128,.1); }
            .card { padding:24px; border-radius:var(--r2); }
            .eyebrow, .kicker { font:500 .78rem var(--mono); text-transform:uppercase; letter-spacing:.11em; }
            .eyebrow { display:inline-flex; align-items:center; gap:10px; padding:9px 13px; border-radius:999px; background:rgba(255,255,255,.78); border:1px solid rgba(244,90,36,.16); color:#6a3b24; box-shadow:0 10px 30px rgba(60,28,14,.06); }
            .dot { width:8px; height:8px; border-radius:50%; background:var(--accent); box-shadow:0 0 0 8px rgba(255,107,44,.12); }
            h1 { margin:22px 0 18px; font:900 clamp(3.55rem,8.2vw,8.8rem)/.82 var(--display); letter-spacing:-.105em; max-width:11.2ch; text-wrap:balance; }
            h2 { margin:0; font:800 clamp(2rem,3.4vw,3.25rem)/.92 var(--display); letter-spacing:-.075em; text-wrap:balance; }
            h3 { margin:0; font-size:1.18rem; letter-spacing:-.04em; }
            p, .muted, .small { color:var(--muted); line-height:1.72; }
            .hero p { margin:0; max-width:60ch; font-size:1.12rem; font-weight:500; }
            .button, .button-secondary { display:inline-flex; align-items:center; justify-content:center; padding:14px 18px; border-radius:16px; font-weight:800; border:1px solid transparent; transition:transform .2s ease, box-shadow .2s ease, background .2s ease; }
            .button { color:#fff7ef; background:linear-gradient(135deg,#f45a24 0%,#bd3c16 100%); box-shadow:0 18px 36px rgba(212,81,29,.3); }
            .button-secondary { background:rgba(255,255,255,.78); border-color:var(--line); box-shadow:0 10px 24px rgba(60,28,14,.06); }
            .button:hover, .button-secondary:hover { transform:translateY(-2px); }
            .button:hover { box-shadow:0 22px 44px rgba(212,81,29,.36); }
            .hero-actions { margin-top:28px; flex-wrap:wrap; }
            .stats { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:12px; margin-top:30px; }
            .stat, .metric, .offer-price, .mini, .trust-card { padding:16px; border-radius:20px; background:rgba(255,255,255,.74); border:1px solid rgba(255,255,255,.76); }
            .stat strong, .metric strong, .offer-price strong, .mini strong { display:block; margin-bottom:6px; }
            .stat strong { font:800 1.75rem/1 var(--display); letter-spacing:-.06em; }
            .metric strong { font-size:1.7rem; letter-spacing:-.05em; }
            .trust-card strong { display:block; margin-bottom:8px; font-size:1.08rem; }
            .pulse-wrap, .offer-list article { padding:16px; border-radius:18px; background:rgba(255,255,255,.72); border:1px solid rgba(76,42,22,.08); }
            .market-card { position:relative; overflow:hidden; min-height:628px; padding:24px; border-radius:var(--r1); background:linear-gradient(180deg,rgba(25,21,18,.96),rgba(43,29,20,.94)); color:#fff8ef; border:1px solid rgba(255,255,255,.16); box-shadow:0 28px 80px rgba(24,13,8,.22); }
            .market-section { padding-top:12px; }
            .market-card.wide { min-height:auto; display:grid; grid-template-columns:1.16fr .84fr; gap:18px; align-items:stretch; }
            .market-copy { display:flex; flex-direction:column; justify-content:space-between; gap:18px; }
            .market-side { display:flex; flex-direction:column; justify-content:space-between; gap:14px; }
            .market-card::before { content:""; position:absolute; inset:-1px; background:radial-gradient(circle at 84% 10%, rgba(25,183,137,.24), transparent 28%), radial-gradient(circle at 4% 0%, rgba(255,107,44,.2), transparent 22%); pointer-events:none; }
            .market-card > * { position:relative; z-index:1; }
            .market-card.is-refreshing { border-color:rgba(25,183,137,.34); }
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
            .section { padding:26px 0; }
            .section-head { margin-bottom:18px; }
            .section-head > p { max-width:48ch; margin:0; font-weight:500; }
            .filter-card { background:linear-gradient(135deg,rgba(255,255,255,.78),rgba(255,246,235,.72)); border-color:rgba(255,255,255,.82); box-shadow:0 24px 70px rgba(60,28,14,.1); }
            form.filter { display:grid; grid-template-columns:1.1fr 1fr 1fr 1fr 1fr 1fr auto; gap:12px; align-items:end; }
            label { display:grid; gap:8px; color:#50362b; font-size:.88rem; font-weight:800; letter-spacing:-.01em; }
            input, select { width:100%; padding:14px 16px; border-radius:16px; border:1px solid rgba(76,42,22,.12); background:rgba(255,255,255,.94); font:inherit; color:var(--text); box-shadow:inset 0 1px 0 rgba(255,255,255,.86); }
            input:focus, select:focus { outline:3px solid rgba(244,90,36,.16); border-color:rgba(244,90,36,.32); }
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
            .offer-card { position:relative; display:grid; gap:16px; transition:transform .22s ease, box-shadow .22s ease, border-color .22s ease; }
            .offer-card:hover { transform:translateY(-5px); border-color:rgba(244,90,36,.22); box-shadow:0 34px 90px rgba(60,28,14,.17); }
            .offer-card::before { content:""; position:absolute; inset:0; border-radius:inherit; background:linear-gradient(135deg,rgba(244,90,36,.12),transparent 34%,rgba(11,143,128,.1)); opacity:0; transition:opacity .22s ease; pointer-events:none; }
            .offer-card:hover::before { opacity:1; }
            .offer-media { position:relative; overflow:hidden; min-height:230px; border-radius:22px; background:linear-gradient(135deg, rgba(255,255,255,.86), rgba(255,239,225,.92)); border:1px solid rgba(76,42,22,.08); }
            .offer-media::after { content:""; position:absolute; inset:auto 0 0; height:44%; background:linear-gradient(180deg,transparent,rgba(20,12,8,.26)); pointer-events:none; }
            .offer-media img { width:100%; height:230px; object-fit:cover; display:block; transform:scale(1.01); transition:transform .28s ease; }
            .offer-card:hover .offer-media img { transform:scale(1.05); }
            .offer-media .badge { position:absolute; top:14px; left:14px; }
            .offer-top { display:grid; gap:10px; }
            .offer-price-grid { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:12px; }
            .offer-price strong { font:800 1.55rem/1 var(--display); letter-spacing:-.055em; color:var(--ink); }
            .offer-list strong { display:flex; justify-content:space-between; gap:12px; font-size:.98rem; }
            .trust-card { position:relative; overflow:hidden; min-height:170px; }
            .trust-card::after { content:""; position:absolute; width:96px; height:96px; right:-28px; bottom:-34px; border-radius:50%; background:rgba(244,90,36,.1); }
            .trust-card:nth-child(2)::after { background:rgba(11,143,128,.12); }
            .trust-card:nth-child(3)::after { background:rgba(214,154,39,.14); }
            .store-cta { background:linear-gradient(135deg,rgba(255,252,245,.92),rgba(237,255,249,.72)); }
            .store-panel { background:rgba(255,255,255,.62); border-color:rgba(255,255,255,.86); }
            .empty { padding:26px; border-radius:20px; background:rgba(255,255,255,.72); border:1px dashed rgba(76,42,22,.18); color:var(--muted); line-height:1.8; }
            .pagination-wrap nav { display:flex; justify-content:center; }
            .pagination-wrap svg { width:18px; height:18px; }
            .footer { display:flex; justify-content:space-between; gap:14px; align-items:center; margin-top:18px; padding:22px 24px 26px; color:var(--muted); font-size:.92rem; border:1px solid rgba(255,255,255,.58); border-radius:24px 24px 0 0; background:rgba(255,255,255,.36); }
            .footer code { padding:4px 8px; border-radius:999px; background:rgba(255,255,255,.76); border:1px solid var(--line); font:400 .82rem "IBM Plex Mono", monospace; }
            @keyframes livePulse { 70% { box-shadow:0 0 0 10px rgba(25,183,137,0); } 100% { box-shadow:0 0 0 0 rgba(25,183,137,0); } }
            @keyframes radarSweep { to { transform:translateX(100%); } }
            @keyframes drawLine { to { stroke-dashoffset:0; } }
            @keyframes floatLine { 50% { transform:translateY(-3px); } }
            @keyframes pointGlow { 50% { filter:drop-shadow(0 0 9px rgba(25,183,137,.85)); } }
            @media (max-width:1100px) { .hero, .chart-grid, .offer-grid, .metrics-grid, .trust-grid, .cta-grid, .market-card.wide, form.filter { grid-template-columns:1fr; } .section-head, .footer { flex-direction:column; align-items:flex-start; } .hero-card.featured, .market-card { min-height:auto; } .nav-links { order:3; width:100%; overflow-x:auto; justify-content:flex-start; } }
            @media (max-width:720px) { .topbar, .top-links, .hero-actions, .section-head, .grid-head, .pulse-meta, .footer, .top-actions { flex-direction:column; align-items:stretch; } .topbar { position:static; border-radius:20px; } .brand-logo { width:156px; } .brand-copy small { display:none; } .hero-card, .card, .market-card { padding:20px; } .hero-watermark, .hero-orbit { display:none; } h1 { font-size:clamp(3.1rem,18vw,5.4rem); } .stats, .offer-price-grid, .market-grid { grid-template-columns:1fr; } .button, .button-secondary, .chip { width:100%; justify-content:center; } .nav-link { white-space:nowrap; } .offer-media img { height:190px; } }
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
                    <img class="brand-logo" src="{{ asset('images/brand/mania-de-preco-logo.svg') }}" alt="Mania de Preço" width="182" height="45">
                </a>

                <nav class="nav-links" aria-label="Navegação principal">
                    <a class="nav-link" href="#descoberta">Buscar</a>
                    <a class="nav-link" href="#radar">Radar</a>
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
                        <div class="hero-watermark">preço<br>vivo</div>
                        <div class="hero-orbit" aria-hidden="true"></div>
                        <div class="hero-copy">
                            <div class="eyebrow"><span class="dot"></span> comparação clara para comprar melhor</div>
                            <h1>Preço bom aparece antes de você perder tempo procurando.</h1>
                            <p>O Mania de Preço mostra onde cada produto está valendo mais a pena, compara lojas ativas e transforma ofertas soltas em uma decisão simples para o seu bolso.</p>

                            <div class="hero-proof" aria-label="Diferenciais do Mania de Preço">
                                <span>ofertas reais</span>
                                <span>leitura rápida</span>
                                <span>feito para celular</span>
                            </div>

                            <div class="hero-actions">
                                <a class="button" href="#descoberta">Buscar ofertas agora</a>
                                <a class="button-secondary" href="#descoberta">Filtrar minha busca</a>
                            </div>
                        </div>

                        <div class="stats">
                            <div class="stat"><strong>{{ number_format($totalOfertas, 0, ',', '.') }}</strong><span>ofertas em leitura</span></div>
                            <div class="stat"><strong>{{ number_format($totalResultados, 0, ',', '.') }}</strong><span>produtos comparáveis</span></div>
                            <div class="stat"><strong>R$ {{ number_format($faixaMedia, 2, ',', '.') }}</strong><span>média de diferença encontrada</span></div>
                        </div>
                    </article>
                </section>

                <section class="section market-section" id="radar">
                    <aside class="market-card wide" data-radar-card data-radar-url="{{ route('radar.precos', request()->query()) }}">
                        <div class="market-copy">
                            <div class="market-status">
                                <div>
                                    <span class="kicker">Radar de preços</span>
                                    <h2 style="margin-top:14px;">O mercado se mexe. Sua decisão acompanha.</h2>
                                    <p class="muted" style="margin:10px 0 0;">Um painel vivo da vitrine publicada para enxergar quedas, faixas e oportunidades com leitura de mercado.</p>
                                </div>
                                <span class="live-badge" data-radar-status>atualizando</span>
                            </div>

                            <div class="market-terminal">
                                <div class="terminal-head">
                                    <span>índice de ofertas</span>
                                    <span><span data-radar-updated>{{ now()->format('H:i') }}</span> BRT</span>
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
                                            <path class="market-area" data-radar-area d="{{ $pulseArea }}" fill="url(#areaGradient)"></path>
                                            <path class="market-line" data-radar-line d="{{ $pulse['path'] }}" fill="none" stroke="url(#pulseGradient)" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <g data-radar-points>
                                                @foreach ($pulse['pontos'] as $ponto)
                                                    <circle class="chart-point" cx="{{ $ponto['x'] }}" cy="{{ $ponto['y'] }}" r="4.5" fill="#14100d" stroke="#bcfff0" stroke-width="2"></circle>
                                                @endforeach
                                            </g>
                                        </svg>
                                    </div>
                                @else
                                    <div class="empty">Ainda não há ofertas suficientes para desenhar o pulso do mercado.</div>
                                @endif
                                <div class="pulse-meta market">
                                    <span data-radar-min>mín. R$ {{ number_format($pulse['menor'], 2, ',', '.') }}</span>
                                    <span data-radar-max>máx. R$ {{ number_format($pulse['maior'], 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="market-side">
                            <div class="market-grid">
                                <div class="market-metric"><strong data-radar-stores>{{ number_format($lojasAtivas, 0, ',', '.') }}</strong><span class="small">lojas ativas no recorte</span></div>
                                <div class="market-metric"><strong data-radar-ranking>{{ $ordenar === 'maior_economia' ? 'economia' : ($ordenar === 'mais_ofertas' ? 'volume' : 'preço') }}</strong><span class="small">sinal que ordena o painel</span></div>
                            </div>

                            <div class="ticker-board" data-radar-ticker aria-label="Oportunidades em destaque">
                                @if ($radarMercado->isNotEmpty())
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
                                @endif
                            </div>

                            <div class="signal-row">
                                <span class="small">Janela de economia</span>
                                <strong data-radar-window>até R$ {{ number_format((float) $radarMercado->max('economia'), 2, ',', '.') }}</strong>
                            </div>
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

                    <article class="card filter-card">
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
                    <article class="hero-card store-cta">
                        <div class="cta-grid">
                            <div>
                                <span class="kicker">para lojas</span>
                                <h2 style="margin-top:14px;">Quer colocar sua loja na frente de quem já está comparando?</h2>
                                <p>Publique ofertas, ganhe presença nas buscas e organize sua operação em uma experiência que já nasce pensada para celular e para conversão.</p>
                            </div>
                            <div class="card store-panel">
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

        <script>
            (() => {
                const card = document.querySelector('[data-radar-card]');

                if (!card || !window.fetch) {
                    return;
                }

                const url = card.dataset.radarUrl;
                const currency = new Intl.NumberFormat('pt-BR', { currency: 'BRL', style: 'currency' });
                const percent = new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 1, minimumFractionDigits: 1 });
                const namespace = 'http://www.w3.org/2000/svg';

                const text = (selector, value) => {
                    const element = card.querySelector(selector);

                    if (element) {
                        element.textContent = value;
                    }
                };

                const escapeHtml = (value) => String(value ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');

                const renderPulse = (pulse) => {
                    const line = card.querySelector('[data-radar-line]');
                    const area = card.querySelector('[data-radar-area]');
                    const points = card.querySelector('[data-radar-points]');

                    if (!line || !area || !points || !pulse?.path) {
                        return;
                    }

                    const lastPoint = pulse.pontos?.at(-1);

                    line.setAttribute('d', pulse.path);
                    area.setAttribute('d', lastPoint ? `${pulse.path} L${lastPoint.x},100 L0,100 Z` : '');
                    points.innerHTML = '';

                    for (const point of pulse.pontos ?? []) {
                        const circle = document.createElementNS(namespace, 'circle');
                        circle.setAttribute('class', 'chart-point');
                        circle.setAttribute('cx', point.x);
                        circle.setAttribute('cy', point.y);
                        circle.setAttribute('r', '4.5');
                        circle.setAttribute('fill', '#14100d');
                        circle.setAttribute('stroke', '#bcfff0');
                        circle.setAttribute('stroke-width', '2');
                        points.append(circle);
                    }
                };

                const renderTicker = (items, maxEconomia) => {
                    const ticker = card.querySelector('[data-radar-ticker]');

                    if (!ticker) {
                        return;
                    }

                    const max = Math.max(0.01, Number(maxEconomia || 0));

                    ticker.innerHTML = (items ?? []).map((item) => {
                        const width = Math.min(100, (Number(item.economia || 0) / max) * 100);

                        return `
                            <div class="ticker-row" style="--ticker-width: ${width}%;">
                                <div class="ticker-name">
                                    <strong>${escapeHtml(item.produto)}</strong>
                                    <span class="small">${escapeHtml(item.loja)} · ${escapeHtml(item.cidade)}</span>
                                </div>
                                <div class="ticker-value">
                                    <span>${escapeHtml(item.sinal)}</span><br>
                                    <strong>-${percent.format(Number(item.variacao || 0))}%</strong>
                                </div>
                            </div>
                        `;
                    }).join('');
                };

                const renderSnapshot = (snapshot) => {
                    text('[data-radar-updated]', String(snapshot.atualizado_em ?? '').slice(0, 5));
                    text('[data-radar-min]', `mín. ${currency.format(Number(snapshot.pulse?.menor || 0))}`);
                    text('[data-radar-max]', `máx. ${currency.format(Number(snapshot.pulse?.maior || 0))}`);
                    text('[data-radar-stores]', new Intl.NumberFormat('pt-BR').format(Number(snapshot.lojas_ativas || 0)));
                    text('[data-radar-ranking]', snapshot.ranking ?? 'preço');
                    text('[data-radar-window]', `até ${currency.format(Number(snapshot.maior_economia || 0))}`);

                    renderPulse(snapshot.pulse);
                    renderTicker(snapshot.radar_mercado, snapshot.maior_economia);
                };

                const refreshRadar = async () => {
                    if (document.hidden) {
                        return;
                    }

                    card.classList.add('is-refreshing');
                    text('[data-radar-status]', 'sincronizando');

                    try {
                        const response = await fetch(url, { headers: { Accept: 'application/json' } });

                        if (!response.ok) {
                            throw new Error('Falha ao atualizar radar');
                        }

                        renderSnapshot(await response.json());
                        text('[data-radar-status]', 'atualizado');
                    } catch (error) {
                        text('[data-radar-status]', 'sem conexão');
                    } finally {
                        window.setTimeout(() => {
                            card.classList.remove('is-refreshing');
                            text('[data-radar-status]', 'atualizando');
                        }, 900);
                    }
                };

                window.setTimeout(refreshRadar, 3500);
                window.setInterval(refreshRadar, 15000);
                window.addEventListener('focus', refreshRadar);
            })();
        </script>
    </body>
</html>
