<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Painel') | Mania de Preco</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|ibm-plex-mono:400,500" rel="stylesheet" />

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
                --warning:#ffae1f;
                --warning-soft:#fff6e5;
                --danger:#fa896b;
                --danger-soft:#fff1ed;
                --shadow:0 14px 34px rgba(31,42,68,.07);
                --radius-xl:24px;
                --radius-lg:18px;
            }

            * { box-sizing:border-box; }
            body {
                margin:0;
                min-height:100vh;
                font-family:"Manrope", sans-serif;
                color:var(--text);
                background:
                    radial-gradient(circle at 10% 0%, rgba(93,135,255,.14), transparent 28%),
                    radial-gradient(circle at 90% 2%, rgba(19,222,185,.11), transparent 24%),
                    var(--bg);
            }
            a { color:inherit; text-decoration:none; }
            button, input, select, textarea { font:inherit; }
            .ui-icon {
                display:inline-block;
                flex:0 0 auto;
                width:1.15em;
                height:1.15em;
                stroke-width:2.1;
                vertical-align:-.18em;
            }

            .backoffice-shell { display:grid; grid-template-columns:300px minmax(0, 1fr); min-height:100vh; }
            .sidebar {
                position:sticky;
                top:0;
                align-self:start;
                min-height:100vh;
                padding:22px 18px;
                background:var(--surface);
                border-right:1px solid var(--line);
                box-shadow:10px 0 30px rgba(31,42,68,.03);
            }
            .brand {
                display:flex;
                align-items:center;
                gap:12px;
                margin-bottom:20px;
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
                font:800 .9rem "IBM Plex Mono", monospace;
            }
            .brand span:last-child { line-height:1.25; }
            .side-card {
                padding:16px;
                border-radius:20px;
                background:linear-gradient(135deg,#22304b,#2f4f9f);
                color:#fff;
                box-shadow:var(--shadow);
                margin-bottom:18px;
            }
            .side-card strong { display:block; margin-bottom:6px; font-size:1rem; }
            .side-card p { margin:0; color:rgba(255,255,255,.72); line-height:1.6; font-size:.88rem; }
            .nav-title {
                display:block;
                margin:18px 4px 8px;
                color:var(--muted);
                font-size:.72rem;
                font-weight:800;
                letter-spacing:.12em;
                text-transform:uppercase;
            }
            .nav {
                display:grid;
                gap:8px;
            }
            .chip {
                display:flex;
                align-items:center;
                justify-content:space-between;
                min-height:44px;
                padding:11px 13px;
                border-radius:14px;
                border:1px solid transparent;
                background:transparent;
                color:#5f6b7a;
                font-weight:800;
                transition:.18s ease;
            }
            .chip:hover {
                color:var(--primary);
                background:var(--primary-soft);
                border-color:#dce7ff;
            }
            .chip::after {
                content:"";
                width:8px;
                height:8px;
                border-radius:50%;
                background:#d8dee8;
            }
            .chip:hover::after { background:var(--primary); }

            .page-wrapper { min-width:0; }
            .shell {
                display:grid;
                gap:18px;
                width:min(100% - 36px, 1400px);
                margin:0 auto;
                padding:22px 0 52px;
            }
            .topbar {
                position:sticky;
                top:0;
                z-index:15;
                display:flex;
                align-items:center;
                justify-content:space-between;
                gap:18px;
                margin:0 -18px 4px;
                padding:18px;
                background:rgba(246,248,251,.84);
                backdrop-filter:blur(18px);
                border-bottom:1px solid rgba(232,237,245,.82);
            }
            .topbar h1 { margin:0; font-size:clamp(1.7rem,3vw,2.35rem); letter-spacing:-.06em; line-height:1.05; }
            .topbar p { margin:7px 0 0; color:var(--muted); line-height:1.6; max-width:780px; }
            .topbar-actions, .section-head, .toolbar {
                display:flex;
                justify-content:space-between;
                align-items:flex-start;
                gap:14px;
                flex-wrap:wrap;
            }
            .topbar-actions { align-items:center; justify-content:flex-end; }
            .topbar-tools {
                display:flex;
                align-items:center;
                gap:10px;
                flex-wrap:wrap;
                justify-content:flex-end;
            }
            .topbar-menu { position:relative; }
            .topbar-menu summary { list-style:none; }
            .topbar-menu summary::-webkit-details-marker { display:none; }
            .avatar, .icon-button {
                display:grid;
                place-items:center;
                flex:0 0 auto;
            }
            .avatar {
                width:44px;
                height:44px;
                border-radius:50%;
                background:#fff;
                border:1px solid var(--line);
                color:var(--primary);
                font-weight:900;
                box-shadow:0 8px 18px rgba(31,42,68,.07);
                overflow:hidden;
            }
            .avatar img {
                width:100%;
                height:100%;
                object-fit:cover;
                display:block;
            }
            .icon-button, .profile-trigger {
                min-height:44px;
                border-radius:14px;
                border:1px solid var(--line);
                background:#fff;
                color:var(--text);
                cursor:pointer;
                transition:.18s ease;
            }
            .icon-button {
                position:relative;
                width:44px;
                font-size:1.05rem;
            }
            .notification-dot {
                position:absolute;
                top:8px;
                right:8px;
                display:grid;
                place-items:center;
                min-width:17px;
                height:17px;
                padding:0 5px;
                border-radius:999px;
                background:var(--danger);
                color:#fff;
                font-size:.68rem;
                line-height:1;
            }
            .profile-trigger {
                display:flex;
                align-items:center;
                gap:10px;
                padding:6px 12px 6px 6px;
            }
            .profile-trigger strong { display:block; font-size:.9rem; line-height:1.1; }
            .profile-trigger small { display:block; margin-top:2px; color:var(--muted); font-size:.76rem; }
            .dropdown-panel {
                position:absolute;
                top:calc(100% + 12px);
                right:0;
                width:min(360px, calc(100vw - 28px));
                padding:14px;
                border-radius:20px;
                background:#fff;
                border:1px solid var(--line);
                box-shadow:var(--shadow);
                z-index:50;
            }
            .dropdown-panel h3 { margin:0 0 10px; font-size:1rem; letter-spacing:-.02em; }
            .dropdown-list, .profile-actions { display:grid; gap:10px; }
            .notification-item, .quick-link, .profile-row {
                display:grid;
                gap:4px;
                padding:12px;
                border-radius:14px;
                background:var(--surface-soft);
                border:1px solid var(--line);
            }
            .notification-item strong, .quick-link strong { display:block; font-size:.9rem; }
            .notification-item span, .quick-link span, .profile-row span { color:var(--muted); font-size:.82rem; line-height:1.5; }
            .profile-row { grid-template-columns:44px minmax(0,1fr); align-items:center; margin-bottom:10px; }
            .profile-actions form { margin:0; }
            .card {
                background:var(--surface);
                border:1px solid var(--line);
                border-radius:var(--radius-xl);
                box-shadow:var(--shadow);
            }
            .card-body { padding:22px; }
            .hero {
                padding:28px;
                border-radius:var(--radius-xl);
                background:
                    linear-gradient(135deg, rgba(93,135,255,.10), transparent 42%),
                    var(--surface);
            }
            .hero h1 { margin:0; font-size:clamp(1.9rem,4vw,2.8rem); line-height:1; letter-spacing:-.07em; }
            .hero p { margin:10px 0 0; color:var(--muted); line-height:1.7; max-width:820px; }
            .grid-4, .grid-3, .grid-2, .list {
                display:grid;
                gap:16px;
            }
            .grid-4 { grid-template-columns:repeat(4,minmax(0,1fr)); }
            .grid-3 { grid-template-columns:repeat(3,minmax(0,1fr)); }
            .grid-2 { grid-template-columns:repeat(2,minmax(0,1fr)); }
            .metric, .list-row, .mini-card {
                padding:18px;
                border-radius:var(--radius-lg);
                background:var(--surface);
                border:1px solid var(--line);
                box-shadow:0 5px 16px rgba(31,42,68,.04);
            }
            .metric { position:relative; overflow:hidden; }
            .metric::after {
                content:"";
                position:absolute;
                right:-34px;
                top:-34px;
                width:96px;
                height:96px;
                border-radius:50%;
                background:var(--primary-soft);
            }
            .metric strong, .mini-card strong {
                display:block;
                position:relative;
                margin-bottom:8px;
                font-size:1.72rem;
                letter-spacing:-.05em;
            }
            .metric span, .mini-card span, .list-row small {
                color:var(--muted);
                line-height:1.6;
            }
            .metric-head {
                position:relative;
                display:flex;
                align-items:center;
                justify-content:space-between;
                gap:12px;
                margin-bottom:10px;
            }
            .metric-icon {
                position:relative;
                display:inline-grid;
                place-items:center;
                width:42px;
                height:42px;
                border-radius:15px;
                color:var(--primary);
                background:var(--primary-soft);
                border:1px solid #dce7ff;
            }
            .metric-icon.is-teal {
                color:#0f8f78;
                background:var(--success-soft);
                border-color:#c8f7ed;
            }
            .metric-icon.is-warning {
                color:#b76d00;
                background:var(--warning-soft);
                border-color:#ffe5b8;
            }
            .metric-icon.is-danger {
                color:#be4f39;
                background:var(--danger-soft);
                border-color:#ffd9cf;
            }
            .list-row {
                display:grid;
                grid-template-columns:minmax(0,1fr) auto;
                gap:14px;
                align-items:center;
            }
            .list-row strong { display:block; margin-bottom:5px; }
            .flash-box {
                padding:15px 17px;
                border-radius:var(--radius-lg);
                background:var(--success-soft);
                border:1px solid #c8f7ed;
                color:#0f8f78;
                line-height:1.7;
            }
            .button, .button-secondary, .logout-button {
                display:inline-flex;
                align-items:center;
                justify-content:center;
                gap:8px;
                min-height:42px;
                padding:11px 15px;
                border-radius:12px;
                border:1px solid var(--line);
                background:#fff;
                color:var(--text);
                font-weight:800;
                font-family:inherit;
                cursor:pointer;
                transition:.18s ease;
            }
            .button {
                color:#fff;
                border-color:transparent;
                background:linear-gradient(135deg,var(--primary),#7c5cff);
                box-shadow:0 12px 22px rgba(93,135,255,.22);
            }
            .button-secondary { background:var(--surface-soft); }
            .logout-button { color:#fff; border-color:#172033; background:#172033; }
            .button:hover, .button-secondary:hover, .logout-button:hover { transform:translateY(-1px); box-shadow:var(--shadow); }
            input, select, textarea {
                width:100%;
                min-height:46px;
                padding:12px 14px;
                border-radius:12px;
                border:1px solid var(--line);
                background:#fff;
                color:var(--text);
                outline:none;
            }
            textarea { min-height:130px; resize:vertical; }
            input:focus, select:focus, textarea:focus {
                border-color:rgba(93,135,255,.55);
                box-shadow:0 0 0 4px rgba(93,135,255,.10);
            }

            @media (max-width:1100px) {
                .backoffice-shell { grid-template-columns:1fr; }
                .sidebar { position:static; min-height:auto; }
                .nav { grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); }
                .grid-4, .grid-3, .grid-2 { grid-template-columns:1fr; }
                .list-row { grid-template-columns:1fr; }
            }
            @media (max-width:720px) {
                .shell { width:min(100% - 20px, 1180px); padding-top:12px; }
                .topbar, .section-head, .toolbar, .topbar-actions, .topbar-tools { flex-direction:column; align-items:stretch; }
                .topbar-menu, .icon-button, .profile-trigger { width:100%; }
                .dropdown-panel { position:static; width:100%; margin-top:10px; }
                .button, .button-secondary, .chip, .logout-button { width:100%; }
                .sidebar { padding:16px 12px; }
            }
        </style>
    </head>
    <body>
        <div class="backoffice-shell">
            <aside class="sidebar">
                <a class="brand" href="@yield('brand_route', route('painel.redirect'))">
                    <span class="brand-badge">MP</span>
                    <span>@yield('brand_label', 'Mania de Preco')</span>
                </a>

                <section class="side-card">
                    <strong>Backoffice</strong>
                    <p>Governanca da plataforma com leitura clara de contas, planos, suporte e acesso.</p>
                </section>

                <span class="nav-title">Navegacao</span>
                <nav class="nav">
                    @yield('nav')
                    <a class="chip" href="{{ url('/') }}">Home publica</a>
                </nav>
            </aside>

            <div class="page-wrapper">
                <div class="shell">
                    @php
                        $usuarioBackoffice = auth()->user();
                        $nomeUsuarioBackoffice = $usuarioBackoffice?->name ?? 'Usuario';
                        $avatarUsuarioBackoffice = $usuarioBackoffice?->avatar_url;
                        $iniciaisBackoffice = collect(preg_split('/\s+/', trim($nomeUsuarioBackoffice)))
                            ->filter()
                            ->take(2)
                            ->map(fn ($parte) => mb_strtoupper(mb_substr($parte, 0, 1)))
                            ->implode('') ?: 'U';
                        $perfilBackoffice = $usuarioBackoffice?->perfilPainel() ?? 'usuario';

                        $centralNotificacoesBackoffice = app(\App\Support\Notificacoes\CentralNotificacoes::class);
                        $notificacoesBackoffice = request()->routeIs('super-admin.*') && $usuarioBackoffice?->ehSuperAdmin()
                            ? $centralNotificacoesBackoffice->superAdmin($usuarioBackoffice)
                            : $centralNotificacoesBackoffice->cliente($usuarioBackoffice);
                        $notificacoesPendentesBackoffice = $notificacoesBackoffice
                            ->reject(fn ($notificacao) => $notificacao['lida'] || $notificacao['dispensada'] || $notificacao['tipo'] === 'sucesso')
                            ->values();
                        $notificacoesTopbarBackoffice = $notificacoesPendentesBackoffice->isNotEmpty()
                            ? $notificacoesPendentesBackoffice
                            : $notificacoesBackoffice;
                        $rotaCentralNotificacoesBackoffice = request()->routeIs('super-admin.*') && $usuarioBackoffice?->ehSuperAdmin()
                            ? route('super-admin.suporte.index')
                            : route('cliente.notificacoes');

                        $atalhosBackoffice = collect([
                            ['titulo' => 'Super admin', 'descricao' => 'Governanca da plataforma.', 'rota' => route('super-admin.dashboard'), 'ativo' => $usuarioBackoffice?->ehSuperAdmin()],
                            ['titulo' => 'Contas', 'descricao' => 'Clientes e operacoes ativas.', 'rota' => route('super-admin.contas.index'), 'ativo' => $usuarioBackoffice?->ehSuperAdmin()],
                            ['titulo' => 'Suporte', 'descricao' => 'Fila de chamados e prioridades.', 'rota' => route('super-admin.suporte.index'), 'ativo' => $usuarioBackoffice?->ehSuperAdmin()],
                            ['titulo' => 'Painel lojista', 'descricao' => 'Operacao da conta vinculada.', 'rota' => route('admin.dashboard'), 'ativo' => $usuarioBackoffice?->possuiAcessoAdmin()],
                            ['titulo' => 'Area do cliente', 'descricao' => 'Visao do usuario final.', 'rota' => route('cliente.dashboard'), 'ativo' => true],
                        ])->filter(fn ($atalho) => $atalho['ativo'])->take(4);
                    @endphp

                    <header class="topbar">
                        <div>
                            <h1>@yield('title', 'Painel')</h1>
                            <p>Ambiente de gestao para acompanhar operacao, crescimento e qualidade da plataforma.</p>
                        </div>

                        <div class="topbar-actions">
                            <div class="topbar-tools">
                                <details class="topbar-menu">
                                    <summary class="icon-button" aria-label="Abrir notificacoes">
                                        <x-ui.icon name="bell" />
                                        <span class="notification-dot">{{ $notificacoesPendentesBackoffice->count() }}</span>
                                    </summary>
                                    <div class="dropdown-panel">
                                        <h3>Notificacoes</h3>
                                        <div class="dropdown-list">
                                            @foreach ($notificacoesTopbarBackoffice->take(5) as $notificacao)
                                                <a class="notification-item" href="{{ $notificacao['rota'] }}">
                                                    <span class="metric-icon {{ $notificacao['tipo'] === 'risco' ? 'is-danger' : ($notificacao['tipo'] === 'alerta' ? 'is-warning' : ($notificacao['tipo'] === 'sucesso' ? 'is-teal' : '')) }}" style="width:34px;height:34px;border-radius:12px;">
                                                        <x-ui.icon :name="$notificacao['icone']" />
                                                    </span>
                                                    <span>
                                                        <strong>{{ $notificacao['titulo'] }}</strong>
                                                        <span>{{ $notificacao['descricao'] }}</span>
                                                    </span>
                                                </a>
                                            @endforeach
                                            <a class="quick-link" href="{{ $rotaCentralNotificacoesBackoffice }}">
                                                <strong>Ver central</strong>
                                                <span>Acompanhe a fila completa de sinais e proximos passos.</span>
                                            </a>
                                        </div>
                                    </div>
                                </details>

                                <details class="topbar-menu">
                                    <summary class="icon-button" aria-label="Abrir atalhos rapidos">
                                        <x-ui.icon name="grid" />
                                    </summary>
                                    <div class="dropdown-panel">
                                        <h3>Atalhos rapidos</h3>
                                        <div class="dropdown-list">
                                            @foreach ($atalhosBackoffice as $atalho)
                                                <a class="quick-link" href="{{ $atalho['rota'] }}">
                                                    <strong>{{ $atalho['titulo'] }}</strong>
                                                    <span>{{ $atalho['descricao'] }}</span>
                                                </a>
                                            @endforeach
                                            <a class="quick-link" href="{{ url('/') }}">
                                                <strong>Home publica</strong>
                                                <span>Voltar para a experiencia publica.</span>
                                            </a>
                                        </div>
                                    </div>
                                </details>

                                <details class="topbar-menu">
                                    <summary class="profile-trigger" aria-label="Abrir menu do usuario">
                                        <span class="avatar">
                                            @if ($avatarUsuarioBackoffice)
                                                <img src="{{ $avatarUsuarioBackoffice }}" alt="Foto de {{ $nomeUsuarioBackoffice }}">
                                            @else
                                                {{ $iniciaisBackoffice }}
                                            @endif
                                        </span>
                                        <span>
                                            <strong>{{ $nomeUsuarioBackoffice }}</strong>
                                            <small>{{ $perfilBackoffice }}</small>
                                        </span>
                                    </summary>
                                    <div class="dropdown-panel">
                                        <h3>Minha conta</h3>
                                        <div class="profile-row">
                                            <span class="avatar">
                                                @if ($avatarUsuarioBackoffice)
                                                    <img src="{{ $avatarUsuarioBackoffice }}" alt="Foto de {{ $nomeUsuarioBackoffice }}">
                                                @else
                                                    {{ $iniciaisBackoffice }}
                                                @endif
                                            </span>
                                            <span>
                                                <strong>{{ $nomeUsuarioBackoffice }}</strong>
                                                <span>{{ $usuarioBackoffice?->email }}</span>
                                            </span>
                                        </div>
                                        <div class="profile-actions">
                                            <a class="button-secondary" href="{{ route('painel.redirect') }}">Meu painel</a>
                                            @if ($usuarioBackoffice?->possuiAcessoAdmin())
                                                <a class="button-secondary" href="{{ route('admin.perfil.edit') }}">Meu perfil</a>
                                            @endif
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button class="logout-button" type="submit">Sair</button>
                                            </form>
                                        </div>
                                    </div>
                                </details>
                            </div>
                        </div>
                    </header>

                    @if (session('status'))
                        <div class="flash-box">{{ session('status') }}</div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </body>
</html>
