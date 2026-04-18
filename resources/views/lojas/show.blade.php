<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $loja->nome }} | Mania de Preco</title>
        <meta name="description" content="Veja o perfil publico da loja {{ $loja->nome }}, com ofertas, categorias fortes e contexto de atendimento.">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|ibm-plex-mono:400,500" rel="stylesheet" />

        <style>
            :root { --bg:#f6efe4; --bg2:#eadac3; --surface:rgba(255,251,245,.84); --line:rgba(66,37,21,.12); --text:#21140f; --muted:#6d5247; --accent:#ff6b2c; --accent2:#0f9f8f; --shadow:0 28px 80px rgba(60,28,14,.12); --r1:30px; --r2:22px; --container:1180px; }
            * { box-sizing:border-box; }
            body { margin:0; min-height:100vh; font-family:"Space Grotesk",sans-serif; color:var(--text); background:radial-gradient(circle at top left, rgba(255,107,44,.18), transparent 28%), radial-gradient(circle at 85% 0%, rgba(15,159,143,.15), transparent 24%), linear-gradient(180deg,#fff7ec 0%, var(--bg) 44%, var(--bg2) 100%); }
            a { color:inherit; text-decoration:none; }
            .container { width:min(calc(100% - 32px), var(--container)); margin:0 auto; }
            .topbar, .hero, .stats, .section-head, .bar-meta, .review-head, .footer { display:flex; gap:14px; }
            .topbar, .section-head, .bar-meta, .review-head, .footer { justify-content:space-between; align-items:flex-start; }
            .topbar { padding:22px 0; align-items:center; }
            .brand { display:inline-flex; align-items:center; gap:14px; font-weight:700; letter-spacing:-.03em; }
            .brand-badge { display:grid; place-items:center; width:44px; height:44px; border-radius:14px; color:#fff7ef; background:linear-gradient(135deg,#ff6b2c 0%,#cf4e1b 100%); box-shadow:0 14px 30px rgba(207,78,27,.28); }
            .chip, .badge { display:inline-flex; align-items:center; justify-content:center; padding:8px 12px; border-radius:999px; }
            .chip { border:1px solid var(--line); background:rgba(255,255,255,.58); color:var(--muted); font-size:.92rem; }
            .hero { display:grid; grid-template-columns:1.05fr .95fr; padding:14px 0 22px; }
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
            .stats, .grid, .reviews { display:grid; gap:16px; }
            .stats { grid-template-columns:repeat(4, minmax(0, 1fr)); margin-top:26px; }
            .grid { grid-template-columns:1fr 1fr; }
            .stat, .mini, .offer, .review { padding:16px; border-radius:18px; background:rgba(255,255,255,.74); border:1px solid rgba(255,255,255,.72); }
            .stat strong, .mini strong, .offer strong { display:block; margin-bottom:6px; }
            .stat strong { font-size:1.5rem; }
            .section { padding:18px 0; }
            .section-head { margin-bottom:16px; }
            .bars, .offer-list { display:grid; gap:12px; }
            .bar-meta { font-size:.92rem; }
            .track { position:relative; height:12px; border-radius:999px; background:rgba(44,24,17,.08); overflow:hidden; }
            .fill { display:block; height:100%; border-radius:inherit; background:linear-gradient(90deg,var(--accent),#ffb06b); }
            .fill.teal { background:linear-gradient(90deg,var(--accent2),#61e7d9); }
            .offer-list { grid-template-columns:repeat(2, minmax(0, 1fr)); }
            .badge { background:rgba(15,159,143,.12); color:#0e6e64; font-size:.82rem; }
            .review { display:grid; gap:10px; }
            .footer { padding:30px 0 48px; color:var(--muted); font-size:.92rem; }
            .footer code { padding:4px 8px; border-radius:999px; background:rgba(255,255,255,.76); border:1px solid var(--line); font:400 .82rem "IBM Plex Mono", monospace; }
            @media (max-width:1100px) { .hero, .grid, .stats, .offer-list { grid-template-columns:1fr; } .section-head, .footer { flex-direction:column; align-items:flex-start; } }
            @media (max-width:720px) { .topbar, .stats, .review-head { flex-direction:column; align-items:stretch; } .hero-card, .card { padding:20px; } }
        </style>
    </head>
    <body>
        @php $categoriaMax = max(1, (float) $categoriaChart->max('total')); @endphp

        <div class="container">
            <header class="topbar">
                <a class="brand" href="{{ route('home') }}">
                    <span class="brand-badge">MP</span>
                    <span>Mania de Preco</span>
                </a>

                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <a class="chip" href="{{ route('home') }}">Voltar para ofertas</a>
                    @auth
                        <a class="chip" href="{{ route('admin.dashboard') }}">Abrir painel</a>
                    @endif
                </div>
            </header>

            <main>
                <section class="hero">
                    <article class="hero-card">
                        <span class="badge">{{ ucfirst($loja->tipo_loja ?? 'loja') }}</span>
                        <h1>{{ $loja->nome }}</h1>
                        <p>
                            {{ $loja->cidade ?? 'Cidade nao informada' }}
                            @if ($loja->uf)
                                - {{ $loja->uf }}
                            @endif
                            @if ($loja->bairro)
                                - {{ $loja->bairro }}
                            @endif
                        </p>
                        <p style="margin-top:12px;">Veja os produtos com melhor preco desta loja, entenda suas categorias mais fortes e confira sinais de confianca para decidir com mais seguranca.</p>

                        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:24px;">
                            <a class="button" href="#ofertas">Ver ofertas da loja</a>
                            @if ($loja->site)
                                <a class="button-secondary" href="{{ $loja->site }}" target="_blank" rel="noreferrer">Acessar site</a>
                            @endif
                        </div>

                        <div class="stats">
                            <div class="stat"><strong>{{ number_format($loja->precos_count, 0, ',', '.') }}</strong><span>ofertas ativas</span></div>
                            <div class="stat"><strong>{{ number_format($ofertas->count(), 0, ',', '.') }}</strong><span>produtos unicos</span></div>
                            <div class="stat"><strong>{{ number_format($avaliacaoMedia, 1, ',', '.') }}</strong><span>avaliacao media</span></div>
                            <div class="stat"><strong>R$ {{ number_format($precoMedio, 2, ',', '.') }}</strong><span>preco medio publicado</span></div>
                        </div>
                    </article>

                    <aside class="card">
                        <div class="section-head">
                            <div>
                                <h3>Resumo rapido da loja</h3>
                                <p class="muted" style="margin:8px 0 0;">Uma leitura objetiva do que mais aparece no catalogo e de como a loja se apresenta ao publico.</p>
                            </div>
                        </div>

                        <div class="grid" style="grid-template-columns:1fr;">
                            <div class="mini">
                                <strong>Contato</strong>
                                <span>{{ $loja->telefone ?: ($loja->whatsapp ?: 'Contato nao informado') }}</span>
                            </div>
                            <div class="mini">
                                <strong>Email</strong>
                                <span>{{ $loja->email ?: 'Nao informado' }}</span>
                            </div>
                            <div class="mini">
                                <strong>Status publico</strong>
                                <span>{{ $loja->status }}</span>
                            </div>
                        </div>
                    </aside>
                </section>

                <section class="section">
                    <div class="grid">
                        <article class="card">
                            <div class="section-head">
                                <div>
                                    <h2>Categorias em destaque</h2>
                                    <p class="muted">As familias de produto que mais ganham visibilidade dentro desta loja.</p>
                                </div>
                            </div>

                            @if ($categoriaChart->isEmpty())
                                <p class="muted">Ainda nao ha categorias suficientes para montar a leitura desta loja.</p>
                            @else
                                <div class="bars">
                                    @foreach ($categoriaChart as $item)
                                        <div>
                                            <div class="bar-meta"><span>{{ $item['nome'] }}</span><span>{{ $item['total'] }} itens</span></div>
                                            <div class="track"><span class="fill teal" style="width: {{ min(100, ($item['total'] / $categoriaMax) * 100) }}%;"></span></div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </article>

                        <article class="card">
                            <div class="section-head">
                                <div>
                                    <h2>Avaliacao recente</h2>
                                    <p class="muted">Sinais sociais para apoiar a decisao do visitante.</p>
                                </div>
                            </div>

                            @if ($avaliacoesRecentes->isEmpty())
                                <p class="muted">Essa loja ainda nao recebeu avaliacoes publicas.</p>
                            @else
                                <div class="reviews">
                                    @foreach ($avaliacoesRecentes as $avaliacao)
                                        <article class="review">
                                            <div class="review-head">
                                                <strong>{{ $avaliacao->user?->name ?? 'Cliente' }}</strong>
                                                <span class="badge">{{ number_format((float) $avaliacao->nota, 1, ',', '.') }}</span>
                                            </div>
                                            <span class="small">{{ $avaliacao->comentario ?: 'Sem comentario adicional.' }}</span>
                                        </article>
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    </div>
                </section>

                <section class="section" id="ofertas">
                    <div class="section-head">
                        <div>
                            <h2>Ofertas desta loja</h2>
                            <p class="muted">Os produtos mais relevantes para explorar agora, com tipos de pagamento e amplitude de preco publicada.</p>
                        </div>
                    </div>

                    <div class="offer-list">
                        @foreach ($produtosDestaque as $item)
                            <article class="offer">
                                <strong>{{ $item['produto']?->nome ?? 'Produto nao informado' }}</strong>
                                <span class="small">{{ $item['produto']?->marca?->nome ?? 'Marca nao informada' }}</span>
                                <span class="small">{{ $item['produto']?->categoria?->nome ?? 'Sem categoria' }}</span>
                                <span class="small">A partir de R$ {{ number_format($item['menor_preco'], 2, ',', '.') }}</span>
                                <span class="small">Variacao publicada de R$ {{ number_format($item['variacao'], 2, ',', '.') }}</span>
                                <span class="small">{{ $item['tipos']->map(fn ($tipo) => ucfirst(str_replace('_', ' ', $tipo)))->implode(' - ') }}</span>
                            </article>
                        @endforeach
                    </div>
                </section>
            </main>

            <footer class="footer">
                <span>Perfil publico da loja desenhado para parecer produto final, com contexto comercial e leitura rapida.</span>
                <code>{{ route('lojas.public.show', $loja) }}</code>
            </footer>
        </div>
    </body>
</html>
