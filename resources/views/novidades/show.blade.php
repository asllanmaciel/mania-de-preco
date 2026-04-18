<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $entry['title'] }} | Mania de Preco</title>
        <meta name="description" content="Veja os detalhes do lancamento {{ $entry['title'] }} e entenda como essa melhoria fortalece a experiencia no Mania de Preco.">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|ibm-plex-mono:400,500" rel="stylesheet" />

        <style>
            :root { --bg:#f5eee2; --bg2:#e5d6c0; --surface:rgba(255,251,245,.84); --line:rgba(66,37,21,.12); --text:#21140f; --muted:#6d5247; --accent:#ff6b2c; --accent2:#0f9f8f; --shadow:0 28px 80px rgba(60,28,14,.12); --r1:30px; --r2:22px; --container:1200px; }
            * { box-sizing:border-box; }
            body { margin:0; min-height:100vh; font-family:"Space Grotesk",sans-serif; color:var(--text); background:radial-gradient(circle at top left, rgba(255,107,44,.18), transparent 28%), radial-gradient(circle at 86% 0%, rgba(15,159,143,.16), transparent 24%), linear-gradient(180deg,#fff7ec 0%, var(--bg) 40%, var(--bg2) 100%); }
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
            .hero { display:grid; grid-template-columns:1.08fr .92fr; padding:12px 0 24px; }
            .grid { display:grid; grid-template-columns:1.25fr .75fr; gap:16px; }
            .hero-card, .card { background:var(--surface); border:1px solid rgba(255,255,255,.62); box-shadow:var(--shadow); backdrop-filter:blur(16px); }
            .hero-card { padding:38px; border-radius:var(--r1); }
            .card { padding:24px; border-radius:var(--r2); }
            .content, .side-list { display:grid; gap:16px; }
            h1 { margin:18px 0 14px; font-size:clamp(2.7rem,4.8vw,4.8rem); line-height:.95; letter-spacing:-.08em; }
            h2 { margin:0; font-size:clamp(1.7rem,2.8vw,2.4rem); letter-spacing:-.06em; }
            h3 { margin:0; font-size:1.16rem; letter-spacing:-.04em; }
            p, .muted, .small, li { color:var(--muted); line-height:1.72; }
            .hero p { margin:0; max-width:62ch; font-size:1.04rem; }
            .buttons { display:flex; gap:12px; flex-wrap:wrap; margin-top:26px; }
            .button, .button-secondary { display:inline-flex; align-items:center; justify-content:center; padding:14px 18px; border-radius:16px; font-weight:700; border:1px solid transparent; }
            .button { color:#fff7ef; background:linear-gradient(135deg,#ff6b2c 0%,#d4511d 100%); box-shadow:0 18px 36px rgba(212,81,29,.28); }
            .button-secondary { background:rgba(255,255,255,.72); border-color:var(--line); }
            .section { padding:18px 0; }
            .section-head { margin-bottom:16px; }
            .meta { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px; }
            .block, .side-card { padding:18px; border-radius:18px; background:rgba(255,255,255,.74); border:1px solid rgba(255,255,255,.72); }
            ul { margin:0; padding-left:18px; }
            .side-card strong { display:block; margin-bottom:8px; }
            .footer { padding:30px 0 48px; color:var(--muted); font-size:.92rem; }
            .footer code { padding:4px 8px; border-radius:999px; background:rgba(255,255,255,.76); border:1px solid var(--line); font:400 .82rem "IBM Plex Mono", monospace; }
            @media (max-width:1100px) { .hero, .grid { grid-template-columns:1fr; } .section-head, .footer { flex-direction:column; align-items:flex-start; } }
            @media (max-width:720px) { .topbar, .top-links { flex-direction:column; align-items:stretch; } .hero-card, .card { padding:20px; } }
        </style>
    </head>
    <body>
        <div class="container">
            <header class="topbar">
                <a class="brand" href="{{ route('home') }}">
                    <span class="brand-badge">MP</span>
                    <span>Mania de Preco</span>
                </a>

                <nav class="top-links">
                    <a class="chip" href="{{ route('home') }}">Ofertas</a>
                    <a class="chip" href="{{ route('projeto') }}">Para lojas</a>
                    <a class="chip" href="{{ route('novidades.index') }}">Lancamentos</a>
                    @auth
                        <a class="chip" href="{{ route('admin.dashboard') }}">Admin</a>
                    @endif
                </nav>
            </header>

            <main>
                <section class="hero">
                    <article class="hero-card">
                        <span class="eyebrow">lancamento recente</span>
                        <h1>{{ $entry['title'] }}</h1>
                        <div class="meta">
                            <span class="badge">{{ $entry['tipo'] ?: 'feature' }}</span>
                            @if ($entry['modulo'])
                                <span class="chip">{{ $entry['modulo'] }}</span>
                            @endif
                            @if ($entry['impacto'])
                                <span class="chip">impacto {{ $entry['impacto'] }}</span>
                            @endif
                            <span class="chip">{{ $entry['data_label'] }}</span>
                        </div>
                        <p>{{ $entry['resumo'] ?: 'Melhoria publicada para reforcar a experiencia e ampliar o valor percebido por quem usa a plataforma.' }}</p>

                        <div class="buttons">
                            <a class="button" href="{{ route('novidades.index') }}">Ver todos os lancamentos</a>
                            <a class="button-secondary" href="{{ route('home') }}">Ir para ofertas</a>
                        </div>
                    </article>

                    <aside class="card">
                        <div class="section-head">
                            <div>
                                <h3>O ganho para a experiencia</h3>
                                <p class="muted" style="margin:8px 0 0;">Cada melhoria desta pagina existe para traduzir evolucao tecnica em valor percebido por quem compra ou opera.</p>
                            </div>
                        </div>

                        <div class="side-list">
                            <div class="side-card">
                                <strong>Resumo rapido</strong>
                                <span class="small">{{ $entry['resultado'] ?: $entry['estrategia'] ?: 'A entrega fortalece a maturidade do produto e melhora a apresentacao publica.' }}</span>
                            </div>
                            <div class="side-card">
                                <strong>Area fortalecida</strong>
                                <span class="small">{{ $entry['modulo'] ?: 'produto' }}</span>
                            </div>
                        </div>
                    </aside>
                </section>

                <section class="section">
                    <div class="grid">
                        <div class="content">
                            <article class="block">
                                <div class="section-head">
                                    <div>
                                        <h2>Visao geral</h2>
                                    </div>
                                </div>
                                <p>{{ $entry['resumo'] ?: 'Sem resumo complementar registrado para esta entrega.' }}</p>
                            </article>

                            <article class="block">
                                <div class="section-head">
                                    <div>
                                        <h2>O que entrou</h2>
                                    </div>
                                </div>
                                @if ($entry['entregas']->isEmpty())
                                    <p>Esta entrega ainda nao tem uma lista publica de itens detalhados.</p>
                                @else
                                    <ul>
                                        @foreach ($entry['entregas'] as $item)
                                            <li>{{ $item }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </article>

                            <article class="block">
                                <div class="section-head">
                                    <div>
                                        <h2>Como isso melhora a experiencia</h2>
                                    </div>
                                </div>
                                <p>{{ $entry['estrategia'] ?: 'A estrategia desta entrega nao foi detalhada publicamente.' }}</p>
                            </article>

                            <article class="block">
                                <div class="section-head">
                                    <div>
                                        <h2>Resultado percebido</h2>
                                    </div>
                                </div>
                                <p>{{ $entry['resultado'] ?: 'O resultado sera detalhado em uma atualizacao futura.' }}</p>
                            </article>
                        </div>

                        <aside class="content">
                            <article class="card">
                                <div class="section-head">
                                    <div>
                                        <h2>Mais lancamentos</h2>
                                    </div>
                                </div>

                                <div class="side-list">
                                    @foreach ($entries as $related)
                                        <a class="side-card" href="{{ route('novidades.show', $related['slug']) }}">
                                            <strong>{{ $related['title'] }}</strong>
                                            <span class="small">{{ $related['data_label'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </article>
                        </aside>
                    </div>
                </section>
            </main>

            <footer class="footer">
                <span>Detalhes do lancamento apresentados de forma clara para mostrar o valor entregue e o ritmo de evolucao da plataforma.</span>
            </footer>
        </div>
    </body>
</html>
