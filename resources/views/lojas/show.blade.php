<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $loja->nome }} | Mania de Preço</title>
        <meta name="description" content="Veja o perfil público da loja {{ $loja->nome }}, com ofertas, categorias fortes e contexto de atendimento.">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|ibm-plex-mono:400,500" rel="stylesheet" />

        <style>
            :root { --bg:#f6efe4; --bg2:#eadac3; --surface:rgba(255,251,245,.84); --line:rgba(66,37,21,.12); --text:#21140f; --muted:#6d5247; --accent:#ff6b2c; --accent2:#0f9f8f; --shadow:0 28px 80px rgba(60,28,14,.12); --r1:30px; --r2:22px; --container:1180px; }
            * { box-sizing:border-box; }
            body { margin:0; min-height:100vh; font-family:"Space Grotesk",sans-serif; color:var(--text); background:radial-gradient(circle at top left, rgba(255,107,44,.18), transparent 28%), radial-gradient(circle at 85% 0%, rgba(15,159,143,.15), transparent 24%), linear-gradient(180deg,#fff7ec 0%, var(--bg) 44%, var(--bg2) 100%); }
            a { color:inherit; text-decoration:none; }
            .container { width:min(calc(100% - 32px), var(--container)); margin:0 auto; }
            .topbar, .hero, .stats, .section-head, .bar-meta, .review-head, .footer, .offer-head, .offer-price-row, .offer-actions { display:flex; gap:14px; }
            .topbar, .section-head, .bar-meta, .review-head, .footer, .offer-head, .offer-price-row, .offer-actions { justify-content:space-between; align-items:flex-start; }
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
            .stats, .grid, .reviews, .offer-tags, .offer-prices, .offer-metrics { display:grid; gap:16px; }
            .stats { grid-template-columns:repeat(4, minmax(0, 1fr)); margin-top:26px; }
            .grid { grid-template-columns:1fr 1fr; }
            .stat, .mini, .offer, .review, .price-pill, .metric-pill { padding:16px; border-radius:18px; background:rgba(255,255,255,.74); border:1px solid rgba(255,255,255,.72); }
            .stat strong, .mini strong, .offer strong, .price-pill strong, .metric-pill strong { display:block; margin-bottom:6px; }
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
            .offer { display:grid; gap:16px; padding:20px; background:linear-gradient(180deg, rgba(255,255,255,.82), rgba(255,247,238,.82)); }
            .offer-head { align-items:flex-start; }
            .offer-media { overflow:hidden; border-radius:20px; min-height:210px; background:linear-gradient(135deg, rgba(255,255,255,.86), rgba(255,239,225,.94)); border:1px solid rgba(66,37,21,.08); }
            .offer-media img { width:100%; height:210px; object-fit:cover; display:block; }
            .offer-title { display:grid; gap:8px; }
            .offer-title h3 { font-size:1.28rem; }
            .offer-tags { grid-template-columns:repeat(2, minmax(0, max-content)); gap:10px; }
            .offer-prices { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:12px; }
            .offer-metrics { grid-template-columns:repeat(3, minmax(0, 1fr)); gap:12px; }
            .price-pill, .metric-pill { padding:14px; border-radius:16px; background:rgba(255,255,255,.78); }
            .price-pill strong { font-size:1.46rem; letter-spacing:-.05em; }
            .metric-pill strong { font-size:1.02rem; }
            .offer-range { display:grid; gap:10px; }
            .offer-actions { align-items:center; flex-wrap:wrap; }
            .offer-actions .button-secondary { padding:12px 16px; }
            .tag-group { display:flex; flex-wrap:wrap; gap:8px; }
            .tag-soft { display:inline-flex; align-items:center; padding:7px 10px; border-radius:999px; background:rgba(255,255,255,.86); border:1px solid var(--line); color:var(--muted); font-size:.84rem; }
            .footer { padding:30px 0 48px; color:var(--muted); font-size:.92rem; }
            .footer code { padding:4px 8px; border-radius:999px; background:rgba(255,255,255,.76); border:1px solid var(--line); font:400 .82rem "IBM Plex Mono", monospace; }
            @media (max-width:1100px) { .hero, .grid, .stats, .offer-list, .offer-prices, .offer-metrics { grid-template-columns:1fr; } .section-head, .footer { flex-direction:column; align-items:flex-start; } }
            @media (max-width:720px) { .topbar, .stats, .review-head, .offer-head, .offer-price-row, .offer-actions, .footer { flex-direction:column; align-items:stretch; } .hero-card, .card { padding:20px; } .offer-tags { grid-template-columns:1fr; } .button, .button-secondary, .chip { width:100%; justify-content:center; } .offer-media img { height:180px; } }
        </style>
    </head>
    <body>
        @php $categoriaMax = max(1, (float) $categoriaChart->max('total')); @endphp

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
                        <p style="margin-top:12px;">
                            {{ $catalogoPublicado
                                ? 'Veja os produtos que mais chamam atencao nesta loja, entenda suas categorias fortes e compare com mais seguranca.'
                                : 'Esta loja ja esta visivel por aqui, mas ainda esta montando as primeiras ofertas para comparacao. Enquanto isso, voce pode explorar outras lojas que ja estao com vitrine ativa.' }}
                        </p>

                        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:24px;">
                            <a class="button" href="{{ $catalogoPublicado ? '#ofertas' : route('home') }}">{{ $catalogoPublicado ? 'Ver ofertas da loja' : 'Explorar ofertas ativas' }}</a>
                            @if ($loja->site)
                                <a class="button-secondary" href="{{ $loja->site }}" target="_blank" rel="noreferrer">Acessar site</a>
                            @endif
                        </div>

                        <div class="stats">
                            <div class="stat"><strong>{{ number_format($loja->precos_count, 0, ',', '.') }}</strong><span>{{ $catalogoPublicado ? 'ofertas ativas' : 'ofertas publicadas' }}</span></div>
                            <div class="stat"><strong>{{ number_format($ofertas->count(), 0, ',', '.') }}</strong><span>{{ $catalogoPublicado ? 'produtos unicos' : 'produtos comparaveis' }}</span></div>
                            <div class="stat"><strong>{{ number_format($avaliacaoMedia, 1, ',', '.') }}</strong><span>avaliacao media</span></div>
                            <div class="stat"><strong>{{ $catalogoPublicado ? 'R$ '.number_format($precoMedio, 2, ',', '.') : 'Em breve' }}</strong><span>{{ $catalogoPublicado ? 'preco medio publicado' : 'catalogo em preparacao' }}</span></div>
                        </div>
                    </article>

                    <aside class="card">
                        <div class="section-head">
                            <div>
                                <h3>O que esperar desta loja</h3>
                                <p class="muted" style="margin:8px 0 0;">Uma leitura objetiva do que ela publica, de como atende e de onde pode chamar mais a sua atencao.</p>
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
                                <strong>Presenca na vitrine</strong>
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
                                <p class="muted">{{ $catalogoPublicado ? 'Ainda nao ha categorias suficientes para montar a leitura desta loja.' : 'Assim que a loja publicar ofertas, esta area passa a mostrar as categorias mais fortes do catalogo.' }}</p>
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

                    @if ($produtosDestaque->isEmpty())
                        <div class="card">
                            <div class="muted" style="display:grid; gap:16px;">
                                <span>Esta loja ainda nao publicou ofertas para comparacao. Para sua busca continuar util, selecionamos outras lojas com vitrine ativa para voce seguir explorando.</span>

                                @if ($lojasRecomendadas->isNotEmpty())
                                    <div class="offer-list">
                                        @foreach ($lojasRecomendadas as $lojaRecomendada)
                                            <a class="offer" href="{{ route('lojas.public.show', $lojaRecomendada) }}">
                                                <strong>{{ $lojaRecomendada->nome }}</strong>
                                                <span class="small">{{ $lojaRecomendada->cidade ?? 'Sem cidade' }} @if ($lojaRecomendada->uf) - {{ $lojaRecomendada->uf }} @endif</span>
                                                <span class="small">{{ number_format($lojaRecomendada->precos_count, 0, ',', '.') }} ofertas publicadas</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="offer-list">
                            @foreach ($produtosDestaque as $item)
                                <article class="offer">
                                    <div class="offer-media">
                                        <img src="{{ $item['produto']?->imagem_url }}" alt="{{ $item['produto']?->nome ?? 'Produto' }}">
                                    </div>

                                    <div class="offer-head">
                                        <div class="offer-title">
                                            <div class="tag-group">
                                                <span class="badge">{{ $item['produto']?->categoria?->nome ?? 'Sem categoria' }}</span>
                                                @if ($item['quantidade_precos'] > 1)
                                                    <span class="tag-soft">{{ $item['quantidade_precos'] }} condicoes publicadas</span>
                                                @else
                                                    <span class="tag-soft">preco unico</span>
                                                @endif
                                            </div>
                                            <h3>{{ $item['produto']?->nome ?? 'Produto nao informado' }}</h3>
                                            <span class="small">{{ $item['produto']?->marca?->nome ?? 'Marca nao informada' }}</span>
                                        </div>

                                        <div class="price-pill" style="min-width:170px;">
                                            <span class="small">melhor valor nesta loja</span>
                                            <strong>R$ {{ number_format($item['menor_preco'], 2, ',', '.') }}</strong>
                                        </div>
                                    </div>

                                    <div class="offer-prices">
                                        <div class="price-pill">
                                            <span class="small">faixa interna</span>
                                            <strong>R$ {{ number_format($item['maior_preco'], 2, ',', '.') }}</strong>
                                            <span class="small">variacao de R$ {{ number_format($item['variacao'], 2, ',', '.') }}</span>
                                        </div>
                                        <div class="price-pill">
                                            <span class="small">elasticidade por pagamento</span>
                                            <strong>{{ number_format($item['faixa_percentual'], 1, ',', '.') }}%</strong>
                                            <span class="small">{{ $item['quantidade_precos'] > 1 ? 'diferenca entre as condicoes publicadas' : 'sem mudanca entre condicoes' }}</span>
                                        </div>
                                    </div>

                                    <div class="offer-range">
                                        <div class="bar-meta">
                                            <span>Leitura de amplitude</span>
                                            <span>R$ {{ number_format($item['menor_preco'], 2, ',', '.') }} ate R$ {{ number_format($item['maior_preco'], 2, ',', '.') }}</span>
                                        </div>
                                        @php
                                            $spreadPercent = $item['maior_preco'] > 0 ? min(100, max(6, ($item['variacao'] / $item['maior_preco']) * 100)) : 6;
                                        @endphp
                                        <div class="track"><span class="fill" style="width: {{ $spreadPercent }}%;"></span></div>
                                    </div>

                                    <div class="offer-metrics">
                                        <div class="metric-pill">
                                            <span class="small">pagamentos</span>
                                            <strong>{{ $item['tipos']->count() }}</strong>
                                            <span class="small">tipos disponiveis</span>
                                        </div>
                                        <div class="metric-pill">
                                            <span class="small">categoria</span>
                                            <strong>{{ $item['produto']?->categoria?->nome ?? 'Sem categoria' }}</strong>
                                            <span class="small">linha principal do item</span>
                                        </div>
                                        <div class="metric-pill">
                                            <span class="small">marca</span>
                                            <strong>{{ $item['produto']?->marca?->nome ?? 'Nao informada' }}</strong>
                                            <span class="small">referencia associada</span>
                                        </div>
                                    </div>

                                    <div class="tag-group">
                                        @foreach ($item['precos'] as $preco)
                                            <span class="tag-soft">{{ ucfirst(str_replace('_', ' ', $preco['tipo'])) }}: R$ {{ number_format($preco['valor'], 2, ',', '.') }}</span>
                                        @endforeach
                                    </div>

                                    <div class="offer-actions">
                                        <span class="small">Veja este item no comparativo completo para entender como esta loja se posiciona frente a outras opcoes do mercado.</span>
                                        @if ($item['produto'])
                                            <a class="button-secondary" href="{{ route('produtos.public.show', $item['produto']) }}">Abrir comparativo do produto</a>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>
            </main>

            <footer class="footer">
                <span>Perfil de loja pensado para inspirar confianca, facilitar comparacao e manter a descoberta viva ate o clique final.</span>
            </footer>
        </div>
    </body>
</html>
