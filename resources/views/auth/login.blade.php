<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Entrar | Mania de Preco</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|ibm-plex-mono:400,500" rel="stylesheet" />

        <style>
            :root {
                --bg: #f5efe3;
                --surface: rgba(255, 251, 245, 0.86);
                --line: rgba(70, 39, 22, 0.12);
                --text: #21140f;
                --muted: #70534b;
                --accent: #ff6b2c;
                --accent-2: #0f9f8f;
                --shadow: 0 24px 70px rgba(65, 27, 10, 0.12);
                --radius-xl: 30px;
                --radius-lg: 22px;
                --radius-md: 16px;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                font-family: "Space Grotesk", sans-serif;
                color: var(--text);
                background:
                    radial-gradient(circle at top left, rgba(255, 170, 95, 0.3), transparent 32%),
                    radial-gradient(circle at 85% 10%, rgba(15, 159, 143, 0.18), transparent 24%),
                    linear-gradient(180deg, #fff6e8 0%, #f5efe3 44%, #efe5d5 100%);
            }

            a {
                color: inherit;
                text-decoration: none;
            }

            .shell {
                max-width: 1180px;
                margin: 0 auto;
                padding: 28px;
            }

            .topbar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 16px;
                margin-bottom: 28px;
            }

            .brand {
                display: inline-flex;
                align-items: center;
                gap: 12px;
                font-weight: 700;
                letter-spacing: 0.03em;
            }

            .brand-badge {
                display: inline-grid;
                place-items: center;
                width: 42px;
                height: 42px;
                border-radius: 14px;
                background: linear-gradient(135deg, var(--accent), #ff9f52);
                color: #27140e;
                font-family: "IBM Plex Mono", monospace;
                font-size: 0.95rem;
            }

            .link-chip {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 12px 16px;
                border-radius: 999px;
                border: 1px solid var(--line);
                background: rgba(255, 255, 255, 0.6);
            }

            .layout {
                display: grid;
                grid-template-columns: minmax(0, 1.05fr) minmax(360px, 0.95fr);
                gap: 20px;
                align-items: stretch;
            }

            .hero,
            .form-card {
                background: var(--surface);
                border: 1px solid rgba(255, 255, 255, 0.58);
                box-shadow: var(--shadow);
                backdrop-filter: blur(14px);
                border-radius: var(--radius-xl);
            }

            .hero {
                position: relative;
                overflow: hidden;
                padding: 34px;
            }

            .hero::after {
                content: "";
                position: absolute;
                width: 260px;
                height: 260px;
                right: -50px;
                bottom: -60px;
                border-radius: 50%;
                background: rgba(255, 107, 44, 0.14);
                filter: blur(16px);
            }

            .eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                padding: 10px 14px;
                border-radius: 999px;
                background: rgba(255, 107, 44, 0.12);
                color: #9a431a;
                text-transform: uppercase;
                font-size: 0.82rem;
                letter-spacing: 0.12em;
            }

            .dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background: var(--accent-2);
                box-shadow: 0 0 0 10px rgba(15, 159, 143, 0.12);
            }

            h1 {
                margin: 22px 0 14px;
                font-size: clamp(2.3rem, 4vw, 4.2rem);
                line-height: 0.95;
                max-width: 10ch;
            }

            .hero p,
            .list li,
            .form-copy p,
            .field-help,
            .demo-box p {
                color: var(--muted);
                line-height: 1.75;
            }

            .list {
                list-style: none;
                padding: 0;
                margin: 24px 0 0;
                display: grid;
                gap: 14px;
            }

            .list li {
                padding: 16px 18px;
                border-radius: var(--radius-md);
                background: rgba(255, 255, 255, 0.62);
                border: 1px solid rgba(76, 42, 22, 0.08);
            }

            .list strong {
                display: block;
                margin-bottom: 6px;
                color: var(--text);
            }

            .form-card {
                padding: 34px;
            }

            .form-copy h2 {
                margin: 0;
                font-size: 1.9rem;
            }

            .form-copy p {
                margin: 10px 0 0;
            }

            form {
                margin-top: 24px;
                display: grid;
                gap: 18px;
            }

            label {
                display: grid;
                gap: 8px;
                font-weight: 600;
            }

            input {
                width: 100%;
                padding: 15px 16px;
                border-radius: 14px;
                border: 1px solid rgba(76, 42, 22, 0.12);
                background: rgba(255, 255, 255, 0.92);
                font: inherit;
                color: var(--text);
            }

            input:focus {
                outline: 2px solid rgba(255, 107, 44, 0.2);
                border-color: rgba(255, 107, 44, 0.36);
            }

            .field-help,
            .error-list,
            .remember-row,
            .demo-box code {
                font-size: 0.92rem;
            }

            .remember-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                color: var(--muted);
            }

            .remember-toggle {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                font-weight: 500;
            }

            .remember-toggle input {
                width: auto;
                accent-color: var(--accent);
            }

            .button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 100%;
                padding: 16px 20px;
                border: 0;
                border-radius: 16px;
                background: linear-gradient(135deg, var(--accent), #ff9f52);
                color: #2d150d;
                font: inherit;
                font-weight: 700;
                cursor: pointer;
                box-shadow: 0 18px 36px rgba(255, 107, 44, 0.2);
            }

            .error-list,
            .status-box,
            .demo-box {
                padding: 14px 16px;
                border-radius: 16px;
                border: 1px solid transparent;
            }

            .error-list {
                background: rgba(182, 72, 51, 0.08);
                border-color: rgba(182, 72, 51, 0.12);
                color: #8c3525;
            }

            .status-box {
                background: rgba(15, 159, 143, 0.08);
                border-color: rgba(15, 159, 143, 0.12);
                color: #0a7167;
            }

            .demo-box {
                margin-top: 18px;
                background: rgba(255, 255, 255, 0.62);
                border-color: rgba(76, 42, 22, 0.08);
            }

            .demo-box code {
                display: block;
                margin-top: 8px;
                padding: 10px 12px;
                border-radius: 12px;
                background: #24140f;
                color: #fff3e8;
                font-family: "IBM Plex Mono", monospace;
            }

            @media (max-width: 980px) {
                .layout {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 720px) {
                .shell {
                    padding: 18px;
                }

                .topbar,
                .remember-row {
                    flex-direction: column;
                    align-items: stretch;
                }

                .hero,
                .form-card {
                    padding: 24px;
                }
            }
        </style>
    </head>
    <body>
        <div class="shell">
            <header class="topbar">
                <a class="brand" href="{{ url('/') }}">
                    <span class="brand-badge">MP</span>
                    <span>Mania de Preco</span>
                </a>

                <a class="link-chip" href="{{ url('/') }}">Voltar para a home</a>
            </header>

            <main class="layout">
                <section class="hero">
                    <span class="eyebrow">
                        <span class="dot"></span>
                        acesso web do saas
                    </span>

                    <h1>Entre para operar a area admin.</h1>

                    <p>
                        Esse fluxo abre o caminho para o painel do lojista no navegador, com autenticacao por sessao
                        e uma base pronta para evoluir em cadastro, financeiro, catalogo e precificacao.
                    </p>

                    <ul class="list">
                        <li>
                            <strong>Painel protegido</strong>
                            Rotas internas ficam disponiveis apenas para usuarios autenticados.
                        </li>
                        <li>
                            <strong>Conta e assinatura</strong>
                            O dashboard ja reconhece a conta ativa e os primeiros indicadores do negocio.
                        </li>
                        <li>
                            <strong>Base para evolucao</strong>
                            A partir daqui a gente consegue crescer com onboarding, CRUDs e modulos operacionais.
                        </li>
                    </ul>
                </section>

                <section class="form-card">
                    <div class="form-copy">
                        <h2>Login administrativo</h2>
                        <p>Use o acesso da sua conta para entrar no painel web.</p>
                    </div>

                    @if (session('status'))
                        <div class="status-box">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="error-list">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.store') }}">
                        @csrf

                        <label for="email">
                            <span>E-mail</span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                            <span class="field-help">Informe o e-mail vinculado ao usuario da conta.</span>
                        </label>

                        <label for="password">
                            <span>Senha</span>
                            <input id="password" type="password" name="password" autocomplete="current-password" required>
                            <span class="field-help">A autenticacao web usa a mesma base de usuarios da API.</span>
                        </label>

                        <div class="remember-row">
                            <label class="remember-toggle" for="remember">
                                <input id="remember" type="checkbox" name="remember" value="1">
                                <span>Manter sessao ativa</span>
                            </label>

                            <span>Depois do login, o acesso segue para <code>/admin</code>.</span>
                        </div>

                        <button class="button" type="submit">Entrar no painel</button>
                    </form>

                    @if (app()->environment('local'))
                        <div class="demo-box">
                            <p>Credenciais da base demo local:</p>
                            <code>test@example.com / password</code>
                        </div>
                    @endif
                </section>
            </main>
        </div>
    </body>
</html>
