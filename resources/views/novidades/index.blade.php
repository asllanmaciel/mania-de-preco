<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Lançamentos | Mania de Preço</title>
        <meta name="description" content="Acompanhe os lançamentos mais recentes do Mania de Preço e veja o que está deixando a experiência melhor para comprar e vender.">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|ibm-plex-mono:400,500" rel="stylesheet" />

        <style>
            :root { --bg:#f7f0e6; --bg2:#eadac3; --surface:rgba(255,251,245,.84); --line:rgba(66,37,21,.12); --text:#21140f; --muted:#6d5247; --accent:#ff6b2c; --accent2:#0f9f8f; --shadow:0 28px 80px rgba(60,28,14,.12); --r1:30px; --r2:22px; --container:1220px; }
            * { box-sizing:border-box; }
            body { margin:0; min-height:100vh; font-family:"Space Grotesk",sans-serif; color:var(--text); background:radial-gradient(circle at top left, rgba(255,107,44,.18), transparent 28%), radial-gradient(circle at 86% 0%, rgba(15,159,143,.16), transparent 24%), linear-gradient(180deg,#fff7ec 0%, var(--bg) 42%, var(--bg2) 100%); }
            a { color:inherit; text-decoration:none; }
            .container { width:min(calc(100% - 32px), var(--container)); margin:0 auto; }
            .topbar, .section-head, .footer, .hero { display:flex; gap:14px; }
            .topbar, .section-head, .footer { justify-content:space-between; align-items:flex-start; }
            .topbar { padding:22px 0; align-items:center; }
            .brand { display:inline-flex; align-items:center; gap:14px; font-weight:700; letter-spacing:-.03em; }
            .brand-badge { display:grid; place-items:center; width:44px; height:44px; border-radius:14px; color:#fff7ef; background:linear-gradient(135deg,#ff6b2c 0%,#cf4e1b 100%); box-shadow:0 14px 30px rgba(207,78,27,.28); }
            .top-links { display:flex; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
            .chip, .badge, .eyebrow { display:inline-flex; align-items:center; justify-content:center; padding:8px 12px; border-radius:999px; }
            .chip { border:1px solid var(--line); background:rgba(255,255,255,.58); color:var(--muted); font-size:.92rem; }
            .badge { background:rgba(15,159,143,.12); color:#0e6e64; font-size:.82rem; }
            .eyebrow { background:rgba(255,255,255,.76); border:1px solid rgba(255,107,44,.16); color:#744329; font:400 .82rem "IBM Plex Mono", monospace; text-transform:uppercase; letter-spacing:.08em; }
            .hero { display:grid; grid-template-columns:1.06fr .94fr; padding:12px 0 24px; }
            .hero-card, .card { background:var(--surface); border:1px solid rgba(255,255,255,.62); box-shadow:var(--shadow); backdrop-filter:blur(16px); }
            .hero-card { padding:38px; border-radius:var(--r1); }
            .card { padding:24px; border-radius:var(--r2); }
            .timeline, .summary-grid { display:grid; gap:16px; }
            .summary-grid { grid-template-columns:repeat(3, minmax(0, 1fr)); }
            h1 { margin:18px 0 14px; font-size:clamp(2.8rem,5vw,5rem); line-height:.92; letter-spacing:-.08em; max-width:11ch; }
            h2 { margin:0; font-size:clamp(1.8rem,3vw,2.6rem); letter-spacing:-.06em; }
            h3 { margin:0; font-size:1.16rem; letter-spacing:-.04em; }
            p, .muted, .small { color:var(--muted); line-height:1.72; }
            .hero p { margin:0; max-width:60ch; font-size:1.04rem; }
            .button, .button-secondary { display:inline-flex; align-items:center; justify-content:center; padding:14px 18px; border-radius:16px; font-weight:700; border:1px solid transparent; }
            .button { color:#fff7ef; background:linear-gradient(135deg,#ff6b2c 0%,#d4511d 100%); box-shadow:0 18px 36px rgba(212,81,29,.28); }
            .button-secondary { background:rgba(255,255,255,.72); border-color:var(--line); }
            .buttons { display:flex; gap:12px; flex-wrap:wrap; margin-top:26px; }
            .summary, .entry-card { padding:18px; border-radius:18px; background:rgba(255,255,255,.74); border:1px solid rgba(255,255,255,.72); }
            .summary strong, .entry-card strong { display:block; margin-bottom:6px; }
            .section { padding:18px 0; }
            .section-head { margin-bottom:16px; }
            .entry-meta { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:12px; }
            .footer { padding:30px 0 48px; color:var(--muted); font-size:.92rem; }
            .footer code { padding:4px 8px; border-radius:999px; background:rgba(255,255,255,.76); border:1px solid var(--line); font:400 .82rem "IBM Plex Mono", monospace; }
            @media (max-width:1100px) { .hero, .summary-grid { grid-template-columns:1fr; } .section-head, .footer { flex-direction:column; align-items:flex-start; } }
            @media (max-width:720px) { .topbar, .top-links { flex-direction:column; align-items:stretch; } .hero-card, .card { padding:20px; } }
        </style>
    </head>
    <body>
        <div class="container">
            <header class="topbar">
                <a class="brand" href="{{ route('home') }}">
                    <span class="brand-badge">MP</span>
                    <span>Mania de Preço</span>
                </a>

                <nav class="top-links">
                    <a class="chip" href="{{ route('home') }}">Ofertas</a>
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
                        <span class="eyebrow">o que acabou de entrar no ar</span>
                        <h1>Lançamentos que deixam a experiência melhor para comprar e vender.</h1>
                        <p>Esta página reúne as melhorias mais recentes do Mania de Preço de um jeito fácil de entender, com foco no que mudou e no valor que isso entrega para quem usa a plataforma.</p>

                        <div class="buttons">
                            <a class="button" href="{{ route('home') }}">Ver ofertas</a>
                            <a class="button-secondary" href="{{ route('projeto') }}">Conhecer recursos para lojas</a>
                        </div>
                    </article>

                    <aside class="card">
                        <div class="section-head">
                            <div>
                                <h3>Leitura rapida</h3>
                                <p class="muted" style="margin:8px 0 0;">Um resumo do ritmo de evolucao para quem esta conhecendo o produto agora.</p>
                            </div>
                        </div>

                        <div class="summary-grid">
                            <div class="summary">
                                <strong>{{ $entries->count() }}</strong>
                                <span class="small">lancamentos publicados</span>
                            </div>
                            <div class="summary">
                                <strong>{{ $entries->pluck('modulo')->filter()->unique()->count() }}</strong>
                                <span class="small">frentes evoluidas</span>
                            </div>
                            <div class="summary">
                                <strong>{{ $latest['data_label'] ?? 'agora' }}</strong>
                                <span class="small">ultima atualizacao registrada</span>
                            </div>
                        </div>
                    </aside>
                </section>

                <section class="section">
                    <div class="section-head">
                        <div>
                                    <h2>O que mudou recentemente</h2>
                                    <p class="muted">Cada entrada abaixo mostra uma melhoria concreta que reforca a experiencia de compra, exposicao e operacao.</p>
                        </div>
                    </div>

                    <div class="timeline">
                        @foreach ($entries as $entry)
                            <a class="entry-card" href="{{ route('novidades.show', $entry['slug']) }}">
                                <div class="entry-meta">
                                    <span class="badge">{{ $entry['tipo'] ?: 'feature' }}</span>
                                    @if ($entry['modulo'])
                                        <span class="chip">{{ $entry['modulo'] }}</span>
                                    @endif
                                    @if ($entry['impacto'])
                                        <span class="chip">ganho {{ $entry['impacto'] }}</span>
                                    @endif
                                </div>
                                <h3>{{ $entry['title'] }}</h3>
                                <p class="small" style="margin:10px 0 0;">{{ $entry['resumo'] ?: $entry['resultado'] }}</p>
                                <span class="small" style="display:block; margin-top:12px;">{{ $entry['data_label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </section>
            </main>

            <footer class="footer">
                <span>Uma vitrine de lancamentos para mostrar que a plataforma segue evoluindo com ritmo, clareza e foco em valor real.</span>
                <span>
                    <a href="{{ route('termos') }}">Termos</a>
                    · <a href="{{ route('privacidade') }}">Privacidade</a>
                    · <a href="{{ route('suporte') }}">Suporte</a>
                </span>
            </footer>
        </div>
    </body>
</html>
