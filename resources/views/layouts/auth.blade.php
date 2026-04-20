<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title') | Mania de Preco</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|ibm-plex-mono:400,500" rel="stylesheet" />

        <style>
            :root {
                --bg:#f6f8fb;
                --surface:#fff;
                --surface-soft:#f8fafc;
                --line:#e8edf5;
                --text:#19202e;
                --muted:#687385;
                --primary:#5d87ff;
                --primary-soft:#edf3ff;
                --success:#13deb9;
                --success-soft:#e6fffa;
                --danger:#fa896b;
                --danger-soft:#fff1ed;
                --shadow:0 18px 44px rgba(31,42,68,.10);
                --radius-xl:28px;
                --radius-lg:18px;
                --font-sans:"Plus Jakarta Sans", sans-serif;
                --font-mono:"IBM Plex Mono", monospace;
            }

            * { box-sizing:border-box; }
            html { text-rendering:optimizeLegibility; -webkit-font-smoothing:antialiased; }
            body {
                margin:0;
                min-height:100vh;
                font-family:var(--font-sans);
                color:var(--text);
                background:
                    radial-gradient(circle at 12% 0%, rgba(93,135,255,.18), transparent 30%),
                    radial-gradient(circle at 86% 6%, rgba(19,222,185,.13), transparent 24%),
                    var(--bg);
            }
            a { color:inherit; text-decoration:none; }
            button, input { font:inherit; }
            .shell {
                display:grid;
                gap:22px;
                width:min(100% - 36px, 1180px);
                margin:0 auto;
                padding:28px 0;
            }
            .topbar {
                display:flex;
                align-items:center;
                justify-content:space-between;
                gap:16px;
            }
            .brand {
                display:inline-flex;
                align-items:center;
                gap:12px;
                font-weight:800;
            }
            .brand-badge {
                display:grid;
                place-items:center;
                width:46px;
                height:46px;
                border-radius:16px;
                color:#fff;
                background:linear-gradient(135deg,var(--primary),#7c5cff);
                box-shadow:0 14px 24px rgba(93,135,255,.28);
                font:800 .9rem var(--font-mono);
            }
            .link-chip, .button, .button-secondary {
                display:inline-flex;
                align-items:center;
                justify-content:center;
                min-height:44px;
                padding:12px 16px;
                border-radius:13px;
                border:1px solid var(--line);
                background:#fff;
                font-weight:800;
                cursor:pointer;
            }
            .layout {
                display:grid;
                grid-template-columns:minmax(0, 1.05fr) minmax(380px, .95fr);
                gap:20px;
                align-items:stretch;
            }
            .hero, .form-card {
                background:var(--surface);
                border:1px solid var(--line);
                border-radius:var(--radius-xl);
                box-shadow:var(--shadow);
            }
            .hero {
                position:relative;
                overflow:hidden;
                padding:34px;
                background:
                    linear-gradient(135deg, rgba(93,135,255,.12), transparent 42%),
                    var(--surface);
            }
            .hero::after {
                content:"";
                position:absolute;
                right:-110px;
                bottom:-120px;
                width:310px;
                height:310px;
                border-radius:50%;
                background:linear-gradient(135deg, rgba(93,135,255,.18), rgba(19,222,185,.18));
            }
            .eyebrow {
                display:inline-flex;
                align-items:center;
                gap:10px;
                padding:9px 12px;
                border-radius:999px;
                background:var(--primary-soft);
                color:var(--primary);
                font-size:.76rem;
                font-weight:800;
                letter-spacing:.12em;
                text-transform:uppercase;
            }
            .dot {
                width:9px;
                height:9px;
                border-radius:50%;
                background:var(--success);
                box-shadow:0 0 0 8px rgba(19,222,185,.12);
            }
            h1 {
                position:relative;
                margin:22px 0 14px;
                font-size:clamp(2.35rem,5vw,4.35rem);
                line-height:.94;
                letter-spacing:-.055em;
                max-width:10ch;
                text-wrap:balance;
            }
            .hero p, .feature-card span, .form-copy p, .field-help, .demo-box p, .helper {
                color:var(--muted);
                line-height:1.72;
            }
            .feature-grid {
                position:relative;
                display:grid;
                gap:12px;
                margin-top:28px;
            }
            .feature-card, .demo-box, .demo-account, .status-box, .error-list {
                padding:15px 16px;
                border-radius:var(--radius-lg);
                border:1px solid var(--line);
                background:var(--surface-soft);
            }
            .feature-card strong {
                display:block;
                margin-bottom:6px;
            }
            .form-card {
                padding:34px;
            }
            .form-copy h2 {
                margin:0;
                font-size:1.8rem;
                letter-spacing:-.04em;
            }
            .form-copy p { margin:10px 0 0; }
            form {
                display:grid;
                gap:17px;
                margin-top:24px;
            }
            label {
                display:grid;
                gap:8px;
                font-weight:800;
            }
            input {
                width:100%;
                min-height:48px;
                padding:13px 14px;
                border-radius:13px;
                border:1px solid var(--line);
                background:#fff;
                color:var(--text);
                outline:none;
            }
            input:focus {
                border-color:rgba(93,135,255,.55);
                box-shadow:0 0 0 4px rgba(93,135,255,.10);
            }
            .field-help, .error-list, .status-box, .remember-row, .demo-box code {
                font-size:.92rem;
            }
            .remember-row, .actions {
                display:flex;
                align-items:center;
                justify-content:space-between;
                gap:14px;
                flex-wrap:wrap;
            }
            .remember-toggle {
                display:inline-flex;
                align-items:center;
                gap:10px;
                color:var(--muted);
                font-weight:700;
            }
            .remember-toggle input {
                width:auto;
                min-height:auto;
                accent-color:var(--primary);
            }
            .terms-check {
                display:flex;
                align-items:flex-start;
                gap:11px;
                padding:14px 15px;
                border:1px solid var(--line);
                border-radius:15px;
                background:var(--surface-soft);
                color:var(--muted);
                font-size:.92rem;
                line-height:1.55;
                font-weight:700;
            }
            .terms-check input {
                width:18px;
                min-width:18px;
                height:18px;
                min-height:18px;
                margin-top:3px;
                accent-color:var(--primary);
            }
            .terms-check a {
                color:var(--primary);
                font-weight:800;
            }
            .button {
                width:100%;
                color:#fff;
                border-color:transparent;
                background:linear-gradient(135deg,var(--primary),#7c5cff);
                box-shadow:0 14px 24px rgba(93,135,255,.24);
            }
            .button-secondary { background:var(--surface-soft); }
            .status-box {
                margin-top:18px;
                background:var(--success-soft);
                border-color:#c8f7ed;
                color:#0f8f78;
            }
            .error-list {
                margin-top:18px;
                background:var(--danger-soft);
                border-color:#ffd9cf;
                color:#a54631;
            }
            .demo-box {
                margin-top:18px;
                background:#fff;
            }
            .demo-grid {
                display:grid;
                gap:10px;
                margin-top:12px;
            }
            .demo-account {
                background:var(--surface-soft);
            }
            .demo-account strong {
                display:flex;
                justify-content:space-between;
                gap:12px;
                font-size:.94rem;
            }
            .demo-account span {
                color:var(--muted);
                font-weight:700;
            }
            .demo-box code {
                display:block;
                margin-top:8px;
                padding:10px 12px;
                border-radius:12px;
                background:#172033;
                color:#f7fbff;
                font-family:var(--font-mono);
            }
            @media (max-width:980px) {
                .layout { grid-template-columns:1fr; }
            }
            @media (max-width:720px) {
                .shell { width:min(100% - 22px, 1180px); padding:18px 0; }
                .topbar, .remember-row, .actions { flex-direction:column; align-items:stretch; }
                .hero, .form-card { padding:24px; }
                .button-secondary, .link-chip { width:100%; }
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
                    <span class="eyebrow"><span class="dot"></span>@yield('eyebrow')</span>
                    <h1>@yield('heading')</h1>
                    <p>@yield('description')</p>

                    <div class="feature-grid">
                        @yield('features')
                    </div>
                </section>

                <section class="form-card">
                    <div class="form-copy">
                        <h2>@yield('form_title')</h2>
                        <p>@yield('form_description')</p>
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

                    @yield('form')
                </section>
            </main>
        </div>
    </body>
</html>
