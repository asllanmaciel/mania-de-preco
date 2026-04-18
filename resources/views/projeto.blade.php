<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Para Lojas | Mania de Preco</title>
        <meta name="description" content="Descubra como sua loja pode ganhar mais visibilidade, publicar ofertas com agilidade e operar com mais clareza no Mania de Preco.">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|ibm-plex-mono:400,500" rel="stylesheet" />

        <style>
            :root { --bg:#f5eee2; --bg2:#e7d8bf; --surface:rgba(255,251,245,.84); --line:rgba(66,37,21,.12); --text:#21140f; --muted:#6d5247; --accent:#ff6b2c; --accent2:#0f9f8f; --gold:#d69a27; --shadow:0 28px 80px rgba(60,28,14,.12); --r1:30px; --r2:22px; --container:1220px; }
            * { box-sizing:border-box; }
            body { margin:0; min-height:100vh; font-family:"Space Grotesk",sans-serif; color:var(--text); background:radial-gradient(circle at top left, rgba(255,107,44,.18), transparent 28%), radial-gradient(circle at 86% 0%, rgba(15,159,143,.16), transparent 24%), linear-gradient(180deg,#fff7ec 0%, var(--bg) 40%, var(--bg2) 100%); }
            a { color:inherit; text-decoration:none; }
            .container { width:min(calc(100% - 32px), var(--container)); margin:0 auto; }
            .topbar, .hero, .section-head, .footer, .roadmap-item { display:flex; gap:14px; }
            .topbar, .section-head, .footer { justify-content:space-between; align-items:flex-start; }
            .topbar { padding:22px 0; align-items:center; }
            .brand { display:inline-flex; align-items:center; gap:14px; font-weight:700; letter-spacing:-.03em; }
            .brand-badge { display:grid; place-items:center; width:44px; height:44px; border-radius:14px; color:#fff7ef; background:linear-gradient(135deg,#ff6b2c 0%,#cf4e1b 100%); box-shadow:0 14px 30px rgba(207,78,27,.28); }
            .top-links { display:flex; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
            .chip, .badge, .eyebrow { display:inline-flex; align-items:center; justify-content:center; padding:8px 12px; border-radius:999px; }
            .chip { border:1px solid var(--line); background:rgba(255,255,255,.58); color:var(--muted); font-size:.92rem; }
            .badge { background:rgba(15,159,143,.12); color:#0e6e64; font-size:.82rem; }
            .eyebrow { background:rgba(255,255,255,.76); border:1px solid rgba(255,107,44,.16); color:#744329; font:400 .82rem "IBM Plex Mono", monospace; text-transform:uppercase; letter-spacing:.08em; }
            .hero { display:grid; grid-template-columns:1.08fr .92fr; padding:12px 0 22px; }
            .grid, .metrics, .pillars, .updates, .roadmap { display:grid; gap:16px; }
            .grid { grid-template-columns:1fr 1fr; }
            .metrics { grid-template-columns:repeat(4, minmax(0, 1fr)); }
            .pillars { grid-template-columns:repeat(3, minmax(0, 1fr)); }
            .updates { grid-template-columns:repeat(2, minmax(0, 1fr)); }
            .card, .hero-card { background:var(--surface); border:1px solid rgba(255,255,255,.62); box-shadow:var(--shadow); backdrop-filter:blur(16px); }
            .hero-card { padding:38px; border-radius:var(--r1); }
            .card { padding:24px; border-radius:var(--r2); }
            h1 { margin:18px 0 14px; font-size:clamp(2.9rem,5vw,5.1rem); line-height:.92; letter-spacing:-.08em; max-width:11ch; }
            h2 { margin:0; font-size:clamp(1.9rem,3vw,2.7rem); letter-spacing:-.06em; }
            h3 { margin:0; font-size:1.18rem; letter-spacing:-.04em; }
            p, .muted, .small { color:var(--muted); line-height:1.72; }
            .hero p { margin:0; max-width:60ch; font-size:1.04rem; }
            .buttons { display:flex; gap:12px; flex-wrap:wrap; margin-top:26px; }
            .button, .button-secondary { display:inline-flex; align-items:center; justify-content:center; padding:14px 18px; border-radius:16px; font-weight:700; border:1px solid transparent; }
            .button { color:#fff7ef; background:linear-gradient(135deg,#ff6b2c 0%,#d4511d 100%); box-shadow:0 18px 36px rgba(212,81,29,.28); }
            .button-secondary { background:rgba(255,255,255,.72); border-color:var(--line); }
            .metric, .pillar, .update-card, .roadmap-card, .mini { padding:18px; border-radius:18px; background:rgba(255,255,255,.74); border:1px solid rgba(255,255,255,.72); }
            .metric strong, .pillar strong, .mini strong { display:block; margin-bottom:6px; }
            .metric strong { font-size:1.7rem; letter-spacing:-.05em; }
            .section { padding:18px 0; }
            .section-head { margin-bottom:16px; }
            .roadmap-item { align-items:flex-start; }
            .roadmap-step { display:grid; place-items:center; min-width:36px; height:36px; border-radius:12px; background:rgba(255,107,44,.14); color:#b84e24; font:600 .94rem "IBM Plex Mono", monospace; }
            .footer { padding:30px 0 48px; color:var(--muted); font-size:.92rem; }
            .footer code { padding:4px 8px; border-radius:999px; background:rgba(255,255,255,.76); border:1px solid var(--line); font:400 .82rem "IBM Plex Mono", monospace; }
            @media (max-width:1100px) { .hero, .grid, .metrics, .pillars, .updates { grid-template-columns:1fr; } .section-head, .footer { flex-direction:column; align-items:flex-start; } }
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
                    @else
                        <a class="chip" href="{{ route('login') }}">Entrar</a>
                    @endauth
                </nav>
            </header>

            <main>
                <section class="hero">
                    <article class="hero-card">
                        <span class="eyebrow">para lojas que querem vender melhor</span>
                        <h1>Sua loja aparece melhor quando preco, vitrine e operacao falam a mesma lingua.</h1>
                        <p>O Mania de Preco ajuda sua loja a publicar ofertas, ganhar descoberta no digital e organizar a rotina comercial com muito menos atrito no dia a dia.</p>

                        <div class="buttons">
                            <a class="button" href="{{ route('home') }}">Ver como as ofertas aparecem</a>
                            <a class="button-secondary" href="{{ route('novidades.index') }}">Acompanhar lancamentos</a>
                        </div>

                        <div class="metrics" style="margin-top:28px;">
                            <div class="metric"><strong>{{ number_format($metricas['lojas'], 0, ',', '.') }}</strong><span>lojas com vitrine ativa</span></div>
                            <div class="metric"><strong>{{ number_format($metricas['produtos'], 0, ',', '.') }}</strong><span>itens em circulacao</span></div>
                            <div class="metric"><strong>{{ number_format($metricas['ofertas'], 0, ',', '.') }}</strong><span>ofertas ja publicadas</span></div>
                            <div class="metric"><strong>{{ number_format($metricas['movimentacoes'], 0, ',', '.') }}</strong><span>movimentacoes processadas</span></div>
                        </div>
                    </article>

                    <aside class="card">
                        <div class="section-head">
                            <div>
                                <h3>O que sua operacao ganha</h3>
                                <p class="muted" style="margin:8px 0 0;">Mais presenca para vender, mais clareza para decidir e uma rotina mais fluida para sua equipe.</p>
                            </div>
                            <span class="badge">para crescer</span>
                        </div>

                        <div class="grid" style="grid-template-columns:1fr;">
                            <div class="mini">
                                <strong>Mais descoberta</strong>
                                <span>Sua loja aparece em comparativos claros, com paginas de produto e de loja prontas para converter curiosidade em visita.</span>
                            </div>
                            <div class="mini">
                                <strong>Mais agilidade</strong>
                                <span>Catalogo, precos e operacao ficam no mesmo fluxo para reduzir retrabalho e acelerar publicacao.</span>
                            </div>
                            <div class="mini">
                                <strong>Mais clareza</strong>
                                <span>Financeiro, catalogo e leitura de performance avancam juntos para dar visao mais segura do negocio.</span>
                            </div>
                        </div>
                    </aside>
                </section>

                <section class="section">
                    <div class="section-head">
                        <div>
                            <h2>Por que as lojas entram</h2>
                            <p class="muted">Os pontos que tornam a presenca na plataforma mais valiosa para quem vende e mais util para quem compra.</p>
                        </div>
                    </div>

                    <div class="pillars">
                        <article class="pillar">
                            <strong>Apareca para quem ja esta comparando</strong>
                            <span class="small">Sua oferta entra em um ambiente onde o cliente ja chega com intencao de compra e criterio de preco.</span>
                        </article>
                        <article class="pillar">
                            <strong>Atualize sem complicar sua rotina</strong>
                            <span class="small">Catalogo, precos e exibicao da loja caminham juntos para reduzir etapas e acelerar a operacao.</span>
                        </article>
                        <article class="pillar">
                            <strong>Tenha leitura melhor do seu caixa</strong>
                            <span class="small">Contas, lancamentos e indicadores ajudam a loja a crescer sem perder visao financeira.</span>
                        </article>
                    </div>
                </section>

                <section class="section">
                    <div class="grid">
                        <article class="card">
                            <div class="section-head">
                                <div>
                                    <h2>O que esta chegando</h2>
                                    <p class="muted">As proximas entregas foram pensadas para aumentar conversao, profundidade de leitura e valor operacional.</p>
                                </div>
                            </div>

                            <div class="roadmap">
                                <div class="roadmap-card roadmap-item">
                                    <span class="roadmap-step">01</span>
                                    <div>
                                        <strong>Mais contexto para decidir</strong>
                                        <p class="small">Historico de preco, reputacao mais forte da loja e paginas publicas ainda mais completas para conversao.</p>
                                    </div>
                                </div>
                                <div class="roadmap-card roadmap-item">
                                    <span class="roadmap-step">02</span>
                                    <div>
                                        <strong>Operacao mais premium</strong>
                                        <p class="small">Dashboards mais fortes, alertas mais inteligentes e automacoes que reduzem atrito no dia a dia.</p>
                                    </div>
                                </div>
                                <div class="roadmap-card roadmap-item">
                                    <span class="roadmap-step">03</span>
                                    <div>
                                        <strong>Entrada mais rapida para novas lojas</strong>
                                        <p class="small">Onboarding mais guiado, materiais mais claros e uma apresentacao publica pronta para demonstracao comercial.</p>
                                    </div>
                                </div>
                            </div>
                        </article>

                        <article class="card">
                            <div class="section-head">
                                <div>
                                    <h2>Recursos ja no ar</h2>
                                    <p class="muted">Um recorte do que ja entrou para mostrar como a experiencia esta ficando mais forte a cada rodada.</p>
                                </div>
                            </div>

                            <div class="updates">
                                @foreach ($latest as $entry)
                                    <a class="update-card" href="{{ route('novidades.show', $entry['slug']) }}">
                                        <span class="badge">{{ $entry['tipo'] ?: 'feature' }}</span>
                                        <h3 style="margin-top:12px;">{{ $entry['title'] }}</h3>
                                        <p class="small" style="margin:10px 0 0;">{{ $entry['resumo'] ?: $entry['resultado'] }}</p>
                                        <span class="small" style="display:block; margin-top:12px;">{{ $entry['data_label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </article>
                    </div>
                </section>
            </main>

            <footer class="footer">
                <span>Uma apresentacao pensada para mostrar como a plataforma ajuda a loja a vender melhor e operar com mais clareza.</span>
            </footer>
        </div>
    </body>
</html>
