<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Mania de Preco</title>
        <meta
            name="description"
            content="SaaS para pequenos lojistas organizarem o financeiro, exibirem catálogo e competirem por preço com mais inteligência."
        >
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|ibm-plex-mono:400,500" rel="stylesheet" />

        <style>
            :root {
                --bg: #f5efe3;
                --surface: rgba(255, 251, 245, 0.78);
                --surface-strong: #fffaf1;
                --surface-dark: #2a160f;
                --text: #21140f;
                --muted: #70534b;
                --line: rgba(70, 39, 22, 0.12);
                --accent: #ff6b2c;
                --accent-2: #0f9f8f;
                --accent-3: #ffcf7d;
                --shadow: 0 24px 70px rgba(65, 27, 10, 0.12);
                --radius-xl: 28px;
                --radius-lg: 20px;
                --radius-md: 14px;
                --container: 1180px;
            }

            * {
                box-sizing: border-box;
            }

            html {
                scroll-behavior: smooth;
            }

            body {
                margin: 0;
                min-height: 100vh;
                font-family: "Space Grotesk", sans-serif;
                color: var(--text);
                background:
                    radial-gradient(circle at top left, rgba(255, 170, 95, 0.32), transparent 32%),
                    radial-gradient(circle at 85% 10%, rgba(15, 159, 143, 0.18), transparent 22%),
                    linear-gradient(180deg, #fff6e8 0%, #f5efe3 44%, #efe5d5 100%);
            }

            a {
                color: inherit;
                text-decoration: none;
            }

            .page-shell {
                position: relative;
                overflow: hidden;
            }

            .page-shell::before,
            .page-shell::after {
                content: "";
                position: absolute;
                inset: auto;
                border-radius: 999px;
                filter: blur(20px);
                opacity: 0.6;
                pointer-events: none;
            }

            .page-shell::before {
                width: 320px;
                height: 320px;
                top: 120px;
                left: -60px;
                background: rgba(255, 107, 44, 0.18);
            }

            .page-shell::after {
                width: 280px;
                height: 280px;
                top: 360px;
                right: -40px;
                background: rgba(15, 159, 143, 0.14);
            }

            .container {
                width: min(calc(100% - 32px), var(--container));
                margin: 0 auto;
            }

            .topbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                padding: 22px 0;
            }

            .brand {
                display: inline-flex;
                align-items: center;
                gap: 14px;
                font-weight: 700;
                letter-spacing: -0.03em;
            }

            .brand-badge {
                width: 42px;
                height: 42px;
                display: grid;
                place-items: center;
                border-radius: 14px;
                color: #fff7ee;
                background: linear-gradient(135deg, #ff6b2c 0%, #d44c15 100%);
                box-shadow: 0 12px 30px rgba(212, 76, 21, 0.25);
            }

            .top-links {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: flex-end;
                gap: 10px;
            }

            .chip-link {
                padding: 10px 14px;
                border: 1px solid var(--line);
                border-radius: 999px;
                background: rgba(255, 255, 255, 0.55);
                font-size: 0.92rem;
                color: var(--muted);
                transition: transform 180ms ease, border-color 180ms ease, background 180ms ease;
            }

            .chip-link:hover {
                transform: translateY(-2px);
                border-color: rgba(255, 107, 44, 0.32);
                background: rgba(255, 249, 242, 0.92);
            }

            .hero {
                display: grid;
                grid-template-columns: 1.15fr 0.85fr;
                gap: 28px;
                padding: 18px 0 54px;
                align-items: stretch;
            }

            .hero-card,
            .panel,
            .feature-card,
            .timeline-card,
            .api-card,
            .summary-card {
                background: var(--surface);
                border: 1px solid rgba(255, 255, 255, 0.58);
                box-shadow: var(--shadow);
                backdrop-filter: blur(14px);
            }

            .hero-card {
                position: relative;
                overflow: hidden;
                padding: 38px;
                border-radius: var(--radius-xl);
            }

            .hero-card::before {
                content: "";
                position: absolute;
                inset: 0;
                background:
                    linear-gradient(120deg, rgba(255, 107, 44, 0.1), transparent 38%),
                    linear-gradient(160deg, transparent 60%, rgba(15, 159, 143, 0.12));
                pointer-events: none;
            }

            .eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                padding: 8px 12px;
                border-radius: 999px;
                background: rgba(255, 255, 255, 0.72);
                border: 1px solid rgba(255, 107, 44, 0.14);
                color: #6d3e22;
                font-size: 0.82rem;
                font-family: "IBM Plex Mono", monospace;
                text-transform: uppercase;
                letter-spacing: 0.08em;
            }

            .dot {
                width: 8px;
                height: 8px;
                border-radius: 999px;
                background: var(--accent);
                box-shadow: 0 0 0 8px rgba(255, 107, 44, 0.14);
                animation: pulse 2.4s ease-in-out infinite;
            }

            @keyframes pulse {
                0%, 100% {
                    transform: scale(1);
                    box-shadow: 0 0 0 8px rgba(255, 107, 44, 0.14);
                }
                50% {
                    transform: scale(1.1);
                    box-shadow: 0 0 0 14px rgba(255, 107, 44, 0.05);
                }
            }

            .hero h1 {
                margin: 22px 0 16px;
                font-size: clamp(2.6rem, 5vw, 5rem);
                line-height: 0.94;
                letter-spacing: -0.07em;
                max-width: 11ch;
            }

            .hero p {
                margin: 0;
                max-width: 56ch;
                color: var(--muted);
                font-size: 1.08rem;
                line-height: 1.7;
            }

            .hero-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 14px;
                margin-top: 28px;
            }

            .button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                padding: 14px 18px;
                border-radius: 16px;
                font-weight: 700;
                transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
            }

            .button-primary {
                color: #fff7f0;
                background: linear-gradient(135deg, #ff6b2c 0%, #d4511d 100%);
                box-shadow: 0 16px 36px rgba(212, 81, 29, 0.28);
            }

            .button-secondary {
                border: 1px solid var(--line);
                background: rgba(255, 255, 255, 0.7);
                color: var(--text);
            }

            .button:hover {
                transform: translateY(-2px);
            }

            .hero-stats {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 12px;
                margin-top: 34px;
            }

            .stat-box {
                padding: 16px;
                border-radius: 18px;
                background: rgba(255, 255, 255, 0.72);
                border: 1px solid rgba(255, 255, 255, 0.7);
            }

            .stat-box strong {
                display: block;
                font-size: 1.4rem;
                letter-spacing: -0.04em;
            }

            .stat-box span {
                color: var(--muted);
                font-size: 0.9rem;
            }

            .panel {
                display: grid;
                gap: 16px;
                padding: 26px;
                border-radius: var(--radius-xl);
                align-content: start;
            }

            .panel-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 12px;
            }

            .panel-header strong {
                font-size: 1.1rem;
            }

            .panel-badge {
                padding: 8px 12px;
                border-radius: 999px;
                background: rgba(15, 159, 143, 0.12);
                color: #0d6a5f;
                font-size: 0.82rem;
                font-family: "IBM Plex Mono", monospace;
            }

            .mini-grid {
                display: grid;
                gap: 12px;
            }

            .mini-card {
                padding: 18px;
                border-radius: 18px;
                background: rgba(255, 255, 255, 0.72);
                border: 1px solid rgba(76, 42, 22, 0.08);
            }

            .mini-card small,
            .section-intro p,
            .summary-card p,
            .timeline-card p,
            .api-list li,
            .feature-card p {
                color: var(--muted);
            }

            .mini-card strong {
                display: block;
                margin-bottom: 6px;
                font-size: 1.1rem;
            }

            .section {
                padding: 26px 0 18px;
            }

            .section-intro {
                display: flex;
                justify-content: space-between;
                gap: 20px;
                align-items: end;
                margin-bottom: 18px;
            }

            .section-intro h2 {
                margin: 0;
                font-size: clamp(1.8rem, 3vw, 2.8rem);
                letter-spacing: -0.06em;
            }

            .section-intro p {
                max-width: 56ch;
                margin: 0;
                line-height: 1.7;
            }

            .feature-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 16px;
            }

            .feature-card {
                padding: 24px;
                border-radius: var(--radius-lg);
                transition: transform 180ms ease;
            }

            .feature-card:hover {
                transform: translateY(-4px);
            }

            .feature-index {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 38px;
                height: 38px;
                border-radius: 12px;
                background: rgba(33, 20, 15, 0.08);
                font-family: "IBM Plex Mono", monospace;
                color: #6b4d43;
                margin-bottom: 18px;
            }

            .feature-card h3,
            .timeline-card h3,
            .summary-card h3 {
                margin: 0 0 10px;
                font-size: 1.22rem;
            }

            .feature-card p {
                margin: 0 0 18px;
                line-height: 1.7;
            }

            .list {
                display: grid;
                gap: 10px;
                padding: 0;
                margin: 0;
                list-style: none;
            }

            .list li {
                position: relative;
                padding-left: 18px;
                color: var(--text);
                font-size: 0.96rem;
            }

            .list li::before {
                content: "";
                position: absolute;
                left: 0;
                top: 0.62rem;
                width: 7px;
                height: 7px;
                border-radius: 999px;
                background: linear-gradient(135deg, var(--accent), var(--accent-2));
            }

            .timeline {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 16px;
            }

            .timeline-card {
                position: relative;
                padding: 24px;
                border-radius: var(--radius-lg);
                overflow: hidden;
            }

            .timeline-card::after {
                content: "";
                position: absolute;
                inset: auto auto 0 0;
                width: 100%;
                height: 4px;
                background: linear-gradient(90deg, var(--accent), var(--accent-2));
            }

            .timeline-step {
                display: inline-flex;
                margin-bottom: 16px;
                padding: 8px 12px;
                border-radius: 999px;
                background: rgba(255, 207, 125, 0.28);
                color: #764b13;
                font-family: "IBM Plex Mono", monospace;
                font-size: 0.8rem;
                text-transform: uppercase;
                letter-spacing: 0.08em;
            }

            .timeline-card p {
                margin: 0;
                line-height: 1.7;
            }

            .api-layout {
                display: grid;
                grid-template-columns: 0.95fr 1.05fr;
                gap: 16px;
            }

            .api-card,
            .summary-card {
                padding: 24px;
                border-radius: var(--radius-lg);
            }

            .api-list {
                list-style: none;
                padding: 0;
                margin: 18px 0 0;
                display: grid;
                gap: 12px;
            }

            .api-list li {
                display: flex;
                justify-content: space-between;
                gap: 18px;
                padding: 14px 16px;
                border-radius: 16px;
                background: rgba(255, 255, 255, 0.7);
                border: 1px solid rgba(76, 42, 22, 0.08);
                font-family: "IBM Plex Mono", monospace;
                font-size: 0.9rem;
            }

            .method {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 56px;
                padding: 6px 8px;
                border-radius: 10px;
                background: rgba(15, 159, 143, 0.12);
                color: #0d6a5f;
                font-size: 0.78rem;
                font-weight: 500;
            }

            .summary-stack {
                display: grid;
                gap: 14px;
                margin-top: 18px;
            }

            .summary-item {
                display: grid;
                gap: 4px;
                padding: 14px 16px;
                border-radius: 16px;
                background: rgba(255, 255, 255, 0.72);
                border: 1px solid rgba(76, 42, 22, 0.08);
            }

            .summary-item strong {
                font-size: 0.98rem;
            }

            .footer {
                display: flex;
                justify-content: space-between;
                gap: 14px;
                align-items: center;
                padding: 28px 0 44px;
                color: var(--muted);
                font-size: 0.92rem;
            }

            .footer code {
                padding: 4px 8px;
                border-radius: 999px;
                background: rgba(255, 255, 255, 0.75);
                border: 1px solid var(--line);
                font-family: "IBM Plex Mono", monospace;
                font-size: 0.82rem;
            }

            @media (max-width: 1080px) {
                .hero,
                .api-layout,
                .feature-grid,
                .timeline {
                    grid-template-columns: 1fr;
                }

                .section-intro,
                .footer {
                    flex-direction: column;
                    align-items: flex-start;
                }
            }

            @media (max-width: 720px) {
                .hero-card,
                .panel,
                .feature-card,
                .timeline-card,
                .api-card,
                .summary-card {
                    padding: 20px;
                }

                .hero h1 {
                    max-width: none;
                }

                .hero-stats {
                    grid-template-columns: 1fr;
                }

                .topbar {
                    align-items: flex-start;
                    flex-direction: column;
                }

                .top-links {
                    justify-content: flex-start;
                }
            }
        </style>
    </head>
    <body>
        <div class="page-shell">
            <div class="container">
                <header class="topbar">
                    <a class="brand" href="/">
                        <span class="brand-badge">MP</span>
                        <span>Mania de Preco</span>
                    </a>

                    <nav class="top-links">
                        <a class="chip-link" href="#modulos">Modulos</a>
                        <a class="chip-link" href="#roadmap">Roadmap</a>
                        <a class="chip-link" href="#api">API</a>
                        @auth
                            <a class="chip-link" href="{{ route('admin.dashboard') }}">Admin</a>
                        @else
                            <a class="chip-link" href="{{ route('login') }}">Entrar</a>
                        @endauth
                        <a class="chip-link" href="http://localhost:8025" target="_blank" rel="noreferrer">Mailpit</a>
                    </nav>
                </header>

                <main>
                    <section class="hero">
                        <article class="hero-card">
                            <div class="eyebrow">
                                <span class="dot"></span>
                                base inicial do saas ja online
                            </div>

                            <h1>Controle financeiro e guerra de preco no mesmo produto.</h1>

                            <p>
                                O Mania de Preco está sendo montado para pequenos lojistas que precisam organizar caixa,
                                contas e operação diária sem perder competitividade na vitrine pública. A mesma base
                                sustenta gestão interna e descoberta de melhores ofertas para o cliente final.
                            </p>

                            <div class="hero-actions">
                                <a class="button button-primary" href="{{ auth()->check() ? route('admin.dashboard') : route('login') }}">
                                    {{ auth()->check() ? 'Abrir area admin' : 'Entrar na area admin' }}
                                </a>
                                <a class="button button-secondary" href="#api">
                                    Ver fluxo de testes
                                </a>
                            </div>

                            <div class="hero-stats">
                                <div class="stat-box">
                                    <strong>2 frentes</strong>
                                    <span>financeiro e comparador no mesmo núcleo</span>
                                </div>

                                <div class="stat-box">
                                    <strong>SaaS B2B</strong>
                                    <span>conta, assinatura, lojas e usuarios</span>
                                </div>

                                <div class="stat-box">
                                    <strong>API pronta</strong>
                                    <span>rotas públicas e privadas já estruturadas</span>
                                </div>
                            </div>
                        </article>

                        <aside class="panel">
                            <div class="panel-header">
                                <strong>Agora no projeto</strong>
                                <span class="panel-badge">docker online</span>
                            </div>

                            <div class="mini-grid">
                                <div class="mini-card">
                                    <strong>Nucleo multiempresa</strong>
                                    <small>Contas, usuarios, lojas, planos e assinatura para sustentar a evolução comercial do produto.</small>
                                </div>

                                <div class="mini-card">
                                    <strong>Financeiro inicial</strong>
                                    <small>Categorias financeiras, contas, movimentações, contas a pagar e contas a receber por conta.</small>
                                </div>

                                <div class="mini-card">
                                    <strong>Comparador em crescimento</strong>
                                    <small>Produtos, preços, marcas, categorias, alertas e avaliações já fazem parte da fundação.</small>
                                </div>
                            </div>
                        </aside>
                    </section>

                    <section class="section" id="modulos">
                        <div class="section-intro">
                            <div>
                                <h2>O produto ja tem forma.</h2>
                            </div>
                            <p>
                                A home agora deixa claro o posicionamento do sistema: o lojista usa o painel para operar
                                melhor e o consumidor encontra oferta com mais contexto.
                            </p>
                        </div>

                        <div class="feature-grid">
                            <article class="feature-card">
                                <span class="feature-index">01</span>
                                <h3>Financeiro operacional</h3>
                                <p>Entradas, saídas, contas a pagar e contas a receber com visão por conta e possibilidade de segmentação por loja.</p>
                                <ul class="list">
                                    <li>categorias financeiras</li>
                                    <li>contas financeiras</li>
                                    <li>movimentações por origem</li>
                                </ul>
                            </article>

                            <article class="feature-card">
                                <span class="feature-index">02</span>
                                <h3>Catálogo competitivo</h3>
                                <p>Base compartilhada de produtos para permitir comparação entre lojas e expansão futura para histórico de preço.</p>
                                <ul class="list">
                                    <li>produtos e marcas</li>
                                    <li>lojas e preços</li>
                                    <li>alertas e avaliações</li>
                                </ul>
                            </article>

                            <article class="feature-card">
                                <span class="feature-index">03</span>
                                <h3>Monetização SaaS</h3>
                                <p>Estrutura pronta para trial, planos e assinatura, dando um caminho claro para produto recorrente.</p>
                                <ul class="list">
                                    <li>cadastro cria conta trial</li>
                                    <li>planos e assinaturas</li>
                                    <li>escopo por tenant</li>
                                </ul>
                            </article>
                        </div>
                    </section>

                    <section class="section" id="roadmap">
                        <div class="section-intro">
                            <div>
                                <h2>Proximas ondas do front.</h2>
                            </div>
                            <p>
                                O próximo salto natural é transformar esse começo em uma navegação real para o lojista,
                                com onboarding, painel financeiro e catálogo administrável.
                            </p>
                        </div>

                        <div class="timeline">
                            <article class="timeline-card">
                                <span class="timeline-step">fase 1</span>
                                <h3>Onboarding e acesso</h3>
                                <p>Seleção da conta ativa, login mais claro e primeiro painel com resumo do negócio.</p>
                            </article>

                            <article class="timeline-card">
                                <span class="timeline-step">fase 2</span>
                                <h3>Painel do lojista</h3>
                                <p>Visão de caixa, lançamentos recentes, contas a vencer e desempenho de preços por loja.</p>
                            </article>

                            <article class="timeline-card">
                                <span class="timeline-step">fase 3</span>
                                <h3>Vitrine inteligente</h3>
                                <p>Histórico de preço, descoberta por região e páginas públicas de loja com proposta comercial mais forte.</p>
                            </article>
                        </div>
                    </section>

                    <section class="section" id="api">
                        <div class="section-intro">
                            <div>
                                <h2>Como testar agora.</h2>
                            </div>
                            <p>
                                Enquanto o painel completo chega, você já consegue navegar e validar a base do sistema
                                pelo navegador, Postman, Insomnia ou terminal.
                            </p>
                        </div>

                        <div class="api-layout">
                            <article class="api-card">
                                <h3>Rotas públicas úteis</h3>
                                <ul class="api-list">
                                    <li><span class="method">GET</span><span>/api/categorias</span></li>
                                    <li><span class="method">GET</span><span>/api/produtos</span></li>
                                    <li><span class="method">GET</span><span>/api/lojas</span></li>
                                    <li><span class="method">GET</span><span>/api/precos</span></li>
                                    <li><span class="method">POST</span><span>/api/register</span></li>
                                    <li><span class="method">POST</span><span>/api/login</span></li>
                                </ul>
                            </article>

                            <article class="summary-card">
                                <h3>Ambiente atual</h3>
                                <p>
                                    O ambiente local está em execução via Docker, com aplicação, MySQL, Redis e Mailpit.
                                    A base já foi migrada e preparada para os próximos ciclos de interface.
                                </p>

                                <div class="summary-stack">
                                    <div class="summary-item">
                                        <strong>App</strong>
                                        <span>http://localhost:8000</span>
                                    </div>

                                    <div class="summary-item">
                                        <strong>Mailpit</strong>
                                        <span>http://localhost:8025</span>
                                    </div>

                                    <div class="summary-item">
                                        <strong>Fluxo recomendado</strong>
                                        <span>criar conta, autenticar, listar contas e começar a lançar a base financeira</span>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </section>
                </main>

                <footer class="footer">
                    <span>Mania de Preco em evolução para um SaaS visualmente navegável e comercialmente claro.</span>
                    <code>docker compose up -d</code>
                </footer>
            </div>
        </div>
    </body>
</html>
