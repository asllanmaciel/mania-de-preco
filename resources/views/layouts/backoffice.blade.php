<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Painel') | Mania de Preco</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|ibm-plex-mono:400,500" rel="stylesheet" />

        <style>
            :root {
                --bg:#f7f2ea;
                --surface:#fff;
                --surface-soft:#fff8f0;
                --line:#ece0d4;
                --text:#19202e;
                --muted:#687385;
                --primary:#f45a24;
                --primary-soft:#fff0e8;
                --success:#0b8f80;
                --success-soft:#e6fbf7;
                --warning:#d69a27;
                --warning-soft:#fff7e4;
                --danger:#ef5b35;
                --danger-soft:#fff1ed;
                --shadow:0 14px 34px rgba(31,42,68,.07);
                --radius-xl:24px;
                --radius-lg:18px;
                --sidebar-width:318px;
                --rail:#fff4ea;
                --font-sans:"Plus Jakarta Sans", sans-serif;
                --font-mono:"IBM Plex Mono", monospace;
                --tracking-tight:-.045em;
            }

            * { box-sizing:border-box; }
            html { text-rendering:optimizeLegibility; -webkit-font-smoothing:antialiased; }
            body {
                margin:0;
                min-height:100vh;
                font-family:var(--font-sans);
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

            .backoffice-shell { display:grid; grid-template-columns:var(--sidebar-width) minmax(0, 1fr); min-height:100vh; }
            .sidebar {
                position:sticky;
                top:0;
                align-self:start;
                display:grid;
                grid-template-columns:74px minmax(0, 1fr);
                min-height:100vh;
                background:var(--surface);
                border-right:1px solid var(--line);
                box-shadow:10px 0 30px rgba(31,42,68,.03);
                z-index:20;
            }
            .sidebar-rail {
                display:flex;
                flex-direction:column;
                align-items:center;
                gap:12px;
                padding:18px 12px;
                background:var(--rail);
                border-right:1px solid var(--line);
            }
            .sidebar-panel {
                display:flex;
                flex-direction:column;
                gap:18px;
                min-height:100vh;
                padding:22px 18px;
                overflow-y:auto;
            }
            .brand {
                display:flex;
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
                background:#21140f;
                box-shadow:0 14px 24px rgba(244,90,36,.24);
                font:800 .9rem var(--font-mono);
                overflow:hidden;
            }
            .brand-badge img { width:100%; height:100%; object-fit:cover; display:block; }
            .brand span:last-child { line-height:1.25; }
            .rail-link {
                position:relative;
                display:inline-grid;
                place-items:center;
                width:50px;
                height:50px;
                padding:0;
                border-radius:16px;
                border:1px solid transparent;
                background:transparent;
                color:#7a869a;
                cursor:pointer;
                transition:.18s ease;
            }
            .rail-link:hover, .rail-link.is-active {
                color:var(--primary);
                background:#fff;
                border-color:var(--line);
                box-shadow:0 8px 22px rgba(31,42,68,.06);
            }
            .rail-link:focus-visible {
                outline:3px solid rgba(244,90,36,.18);
                outline-offset:3px;
            }
            .rail-link::after {
                content:attr(data-label);
                position:absolute;
                left:calc(100% + 12px);
                top:50%;
                z-index:60;
                min-width:max-content;
                max-width:180px;
                padding:8px 10px;
                border-radius:11px;
                color:#fff;
                background:#172033;
                box-shadow:0 8px 22px rgba(31,42,68,.10);
                font-size:.75rem;
                font-weight:800;
                line-height:1;
                opacity:0;
                pointer-events:none;
                transform:translate(4px,-50%);
                transition:.16s ease;
            }
            .rail-link::before {
                content:"";
                position:absolute;
                left:calc(100% + 6px);
                top:50%;
                z-index:61;
                width:8px;
                height:8px;
                border-radius:2px;
                background:#172033;
                opacity:0;
                pointer-events:none;
                transform:translate(4px,-50%) rotate(45deg);
                transition:.16s ease;
            }
            .rail-link:hover::after,
            .rail-link:hover::before,
            .rail-link:focus-visible::after,
            .rail-link:focus-visible::before {
                opacity:1;
                transform:translate(0,-50%);
            }
            .rail-stack {
                display:grid;
                gap:10px;
                width:100%;
                margin-top:14px;
            }
            .side-card {
                padding:16px;
                border-radius:20px;
                background:linear-gradient(135deg,#22304b,#2f4f9f);
                color:#fff;
                box-shadow:var(--shadow);
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
            .backoffice-module-panel { display:none; }
            .backoffice-module-panel.is-active { display:block; }
            .module-kicker {
                display:flex;
                align-items:center;
                justify-content:space-between;
                gap:12px;
                margin:6px 4px 12px;
            }
            .module-kicker strong {
                display:block;
                font-size:1rem;
                letter-spacing:-.03em;
            }
            .module-kicker span {
                display:block;
                margin-top:2px;
                color:var(--muted);
                font-size:.78rem;
                line-height:1.45;
            }
            .module-count {
                display:inline-grid;
                place-items:center;
                min-width:30px;
                height:30px;
                padding:0 9px;
                border-radius:999px;
                color:var(--primary);
                background:var(--primary-soft);
                border:1px solid var(--line);
                font:800 .74rem var(--font-mono);
            }
            .menu-links { display:grid; gap:6px; }
            .menu-link {
                display:grid;
                grid-template-columns:38px minmax(0, 1fr) auto;
                gap:12px;
                align-items:center;
                min-height:48px;
                padding:8px 10px;
                border-radius:14px;
                color:#5f6b7a;
                border:1px solid transparent;
                transition:.18s ease;
            }
            .menu-link:hover, .menu-link.is-active {
                color:var(--primary);
                background:var(--primary-soft);
                border-color:#dce7ff;
            }
            .menu-link span:not(.menu-icon) { display:block; color:inherit; font-weight:800; font-size:.92rem; }
            .menu-link small { display:block; margin-top:2px; color:var(--muted); font-size:.74rem; }
            .menu-icon {
                display:inline-grid;
                place-items:center;
                width:38px;
                height:38px;
                border-radius:13px;
                background:#fff;
                border:1px solid var(--line);
                color:currentColor;
                font-size:1.05rem;
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
                width:100%;
                margin:0;
                padding:0 18px 52px;
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
            .topbar h1 { margin:0; font-size:clamp(1.7rem,3vw,2.35rem); letter-spacing:var(--tracking-tight); line-height:1.05; text-wrap:balance; }
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
                border:1px solid transparent;
                background:transparent;
                color:var(--text);
                cursor:pointer;
                transition:.18s ease;
            }
            .icon-button:hover, .profile-trigger:hover, .topbar-menu[open] > summary {
                background:rgba(244,90,36,.08);
                color:var(--primary);
            }
            .icon-button {
                position:relative;
                width:44px;
                font-size:1.05rem;
            }
            .icon-button.has-caret {
                display:inline-grid;
                grid-template-columns:auto auto;
                gap:4px;
                width:auto;
                min-width:50px;
                padding:0 11px;
            }
            .dropdown-caret {
                width:14px;
                height:14px;
                color:var(--muted);
                transition:.18s ease;
            }
            .topbar-menu[open] .dropdown-caret { transform:rotate(180deg); }
            .notification-dot {
                position:absolute;
                top:3px;
                right:3px;
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
            .hero h1 { margin:0; font-size:clamp(1.9rem,4vw,2.8rem); line-height:1; letter-spacing:var(--tracking-tight); text-wrap:balance; }
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
            .badge {
                display:inline-flex;
                align-items:center;
                justify-content:center;
                min-height:30px;
                padding:6px 10px;
                border-radius:999px;
                color:#0f8f78;
                background:var(--success-soft);
                border:1px solid #c8f7ed;
                font-size:.78rem;
                font-weight:900;
            }
            .badge.is-warning {
                color:#b76d00;
                background:var(--warning-soft);
                border-color:#ffe5b8;
            }
            .badge.is-danger {
                color:#be4f39;
                background:var(--danger-soft);
                border-color:#ffd9cf;
            }
            .badge.is-muted {
                color:var(--muted);
                background:var(--surface-soft);
                border-color:var(--line);
            }
            .readiness-panel {
                display:grid;
                grid-template-columns:.82fr 1.18fr;
                gap:16px;
                align-items:stretch;
            }
            .readiness-score {
                display:grid;
                gap:14px;
                align-content:space-between;
                min-height:100%;
                padding:22px;
                border-radius:var(--radius-xl);
                background:
                    radial-gradient(circle at 84% 16%, rgba(19,222,185,.18), transparent 28%),
                    linear-gradient(135deg,#18233a,#263d78);
                color:#fff;
                overflow:hidden;
            }
            .readiness-score p { margin:0; color:rgba(255,255,255,.72); line-height:1.7; }
            .readiness-score strong { display:block; font-size:clamp(3rem,7vw,5.2rem); line-height:.9; letter-spacing:-.07em; }
            .readiness-groups, .readiness-actions, .readiness-group, .checklist-stack {
                display:grid;
                gap:12px;
            }
            .readiness-group {
                padding:16px;
                border-radius:var(--radius-lg);
                background:var(--surface-soft);
                border:1px solid var(--line);
            }
            .progress-meta {
                display:flex;
                align-items:center;
                justify-content:space-between;
                gap:12px;
                font-weight:900;
            }
            .progress-track {
                height:10px;
                border-radius:999px;
                background:#e9eef7;
                overflow:hidden;
            }
            .progress-fill {
                display:block;
                height:100%;
                border-radius:inherit;
                background:linear-gradient(90deg,var(--primary),#ba3c16);
            }
            .progress-fill.is-teal { background:linear-gradient(90deg,#0f8f78,var(--success)); }
            .checklist-item {
                display:grid;
                grid-template-columns:minmax(0,1fr) auto;
                gap:12px;
                align-items:center;
                padding:13px;
                border-radius:15px;
                background:#fff;
                border:1px solid var(--line);
            }
            .checklist-item strong { display:block; margin-bottom:4px; }
            .checklist-item span { color:var(--muted); line-height:1.5; font-size:.88rem; }
            .checklist-actions {
                display:flex;
                align-items:center;
                justify-content:flex-end;
                gap:8px;
                flex-wrap:wrap;
            }
            .helper-text {
                margin:4px 0 0;
                color:var(--muted);
                line-height:1.6;
                font-size:.9rem;
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
                background:linear-gradient(135deg,var(--primary),#ba3c16);
                box-shadow:0 12px 22px rgba(244,90,36,.22);
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
                .sidebar { position:static; display:block; min-height:auto; }
                .sidebar-rail { display:none; }
                .sidebar-panel { min-height:auto; padding:16px 12px; }
                .nav { grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); }
                .backoffice-module-panel { display:block; }
                .backoffice-module-panel:not(.is-active) { display:block; }
                .grid-4, .grid-3, .grid-2, .readiness-panel { grid-template-columns:1fr; }
                .list-row { grid-template-columns:1fr; }
            }
            @media (max-width:720px) {
                .shell { padding:12px 10px 52px; }
                .topbar { margin:-12px -10px 4px; padding:12px 10px; }
                .topbar, .section-head, .toolbar, .topbar-actions, .topbar-tools { flex-direction:column; align-items:stretch; }
                .topbar-menu, .icon-button, .profile-trigger { width:100%; }
                .dropdown-panel { position:static; width:100%; margin-top:10px; }
                .button, .button-secondary, .chip, .logout-button { width:100%; }
                .checklist-item { grid-template-columns:1fr; }
                .checklist-actions { justify-content:flex-start; }
            }
        </style>
    </head>
    <body>
        <div class="backoffice-shell">
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
                $isSuperAdminArea = request()->routeIs('super-admin.*') && $usuarioBackoffice?->ehSuperAdmin();

                $centralNotificacoesBackoffice = app(\App\Support\Notificacoes\CentralNotificacoes::class);
                $notificacoesBackoffice = $isSuperAdminArea
                    ? $centralNotificacoesBackoffice->superAdmin($usuarioBackoffice)
                    : $centralNotificacoesBackoffice->cliente($usuarioBackoffice);
                $notificacoesPendentesBackoffice = $notificacoesBackoffice
                    ->reject(fn ($notificacao) => $notificacao['lida'] || $notificacao['dispensada'] || $notificacao['tipo'] === 'sucesso')
                    ->values();
                $notificacoesTopbarBackoffice = $notificacoesPendentesBackoffice->isNotEmpty()
                    ? $notificacoesPendentesBackoffice
                    : $notificacoesBackoffice;
                $rotaCentralNotificacoesBackoffice = $isSuperAdminArea
                    ? route('super-admin.suporte.index')
                    : route('cliente.notificacoes');

                $atalhosBackoffice = collect([
                    ['titulo' => 'Super admin', 'descricao' => 'Governanca da plataforma.', 'rota' => route('super-admin.dashboard'), 'ativo' => $usuarioBackoffice?->ehSuperAdmin()],
                    ['titulo' => 'Contas', 'descricao' => 'Clientes e operacoes ativas.', 'rota' => route('super-admin.contas.index'), 'ativo' => $usuarioBackoffice?->ehSuperAdmin()],
                    ['titulo' => 'Suporte', 'descricao' => 'Fila de chamados e prioridades.', 'rota' => route('super-admin.suporte.index'), 'ativo' => $usuarioBackoffice?->ehSuperAdmin()],
                    ['titulo' => 'Painel lojista', 'descricao' => 'Operacao da conta vinculada.', 'rota' => route('admin.dashboard'), 'ativo' => $usuarioBackoffice?->possuiAcessoAdmin()],
                    ['titulo' => 'Area do cliente', 'descricao' => 'Visao do usuario final.', 'rota' => route('cliente.dashboard'), 'ativo' => true],
                ])->filter(fn ($atalho) => $atalho['ativo'])->take(4);

                $backofficeModules = $isSuperAdminArea
                    ? collect([
                        [
                            'id' => 'governanca',
                            'titulo' => 'Governanca',
                            'descricao' => 'Visao executiva da plataforma.',
                            'icone' => 'shield',
                            'active' => request()->routeIs('super-admin.dashboard') || request()->routeIs('super-admin.analytics'),
                            'items' => collect([
                                ['titulo' => 'Visao geral', 'descricao' => 'saude global', 'rota' => route('super-admin.dashboard'), 'icone' => 'home', 'active' => request()->routeIs('super-admin.dashboard')],
                                ['titulo' => 'Analytics', 'descricao' => 'sinais e funil', 'rota' => route('super-admin.analytics'), 'icone' => 'chart', 'active' => request()->routeIs('super-admin.analytics')],
                            ]),
                        ],
                        [
                            'id' => 'contas',
                            'titulo' => 'Contas',
                            'descricao' => 'Clientes, detalhes e assinaturas.',
                            'icone' => 'store',
                            'active' => request()->routeIs('super-admin.contas.*') || request()->routeIs('super-admin.assinaturas.*'),
                            'items' => collect([
                                ['titulo' => 'Contas', 'descricao' => 'clientes ativos', 'rota' => route('super-admin.contas.index'), 'icone' => 'store', 'active' => request()->routeIs('super-admin.contas.index')],
                                isset($conta) ? ['titulo' => 'Conta atual', 'descricao' => 'detalhe e consumo', 'rota' => route('super-admin.contas.show', $conta), 'icone' => 'grid', 'active' => request()->routeIs('super-admin.contas.show') || request()->routeIs('super-admin.contas.assinaturas.*')] : null,
                            ])->filter()->values(),
                        ],
                        [
                            'id' => 'receita',
                            'titulo' => 'Receita',
                            'descricao' => 'Planos e monetizacao.',
                            'icone' => 'credit-card',
                            'active' => request()->routeIs('super-admin.planos.*'),
                            'items' => collect([
                                ['titulo' => 'Planos', 'descricao' => 'catalogo comercial', 'rota' => route('super-admin.planos.index'), 'icone' => 'credit-card', 'active' => request()->routeIs('super-admin.planos.*')],
                            ]),
                        ],
                        [
                            'id' => 'suporte',
                            'titulo' => 'Suporte',
                            'descricao' => 'Fila, prioridade e riscos.',
                            'icone' => 'bell',
                            'active' => request()->routeIs('super-admin.suporte.*'),
                            'items' => collect([
                                ['titulo' => 'Chamados', 'descricao' => 'fila operacional', 'rota' => route('super-admin.suporte.index'), 'icone' => 'bell', 'active' => request()->routeIs('super-admin.suporte.*')],
                                ['titulo' => 'Pagina publica', 'descricao' => 'canal de abertura', 'rota' => route('suporte'), 'icone' => 'search', 'active' => false],
                            ]),
                        ],
                    ])
                    : collect([
                        [
                            'id' => 'cliente',
                            'titulo' => 'Cliente',
                            'descricao' => 'Radar pessoal e alertas.',
                            'icone' => 'spark',
                            'active' => request()->routeIs('cliente.dashboard') || request()->routeIs('cliente.notificacoes'),
                            'items' => collect([
                                ['titulo' => 'Meu radar', 'descricao' => 'ofertas e alertas', 'rota' => route('cliente.dashboard'), 'icone' => 'spark', 'active' => request()->routeIs('cliente.dashboard')],
                                ['titulo' => 'Notificacoes', 'descricao' => 'sinais recentes', 'rota' => route('cliente.notificacoes'), 'icone' => 'bell', 'active' => request()->routeIs('cliente.notificacoes')],
                            ]),
                        ],
                        [
                            'id' => 'atalhos',
                            'titulo' => 'Atalhos',
                            'descricao' => 'Voltar para areas principais.',
                            'icone' => 'grid',
                            'active' => false,
                            'items' => collect([
                                ['titulo' => 'Ofertas publicas', 'descricao' => 'comparar precos', 'rota' => route('home'), 'icone' => 'search', 'active' => false],
                                $usuarioBackoffice?->ehSuperAdmin() ? ['titulo' => 'Super admin', 'descricao' => 'governanca', 'rota' => route('super-admin.dashboard'), 'icone' => 'shield', 'active' => false] : null,
                                $usuarioBackoffice?->possuiAcessoAdmin() ? ['titulo' => 'Painel lojista', 'descricao' => 'operacao', 'rota' => route('admin.dashboard'), 'icone' => 'store', 'active' => false] : null,
                            ])->filter()->values(),
                        ],
                    ]);

                $backofficeModules = $backofficeModules->filter(fn (array $module) => $module['items']->isNotEmpty())->values();
                $activeBackofficeModule = $backofficeModules->firstWhere('active', true)['id'] ?? $backofficeModules->first()['id'];
            @endphp

            <aside class="sidebar">
                <div class="sidebar-rail">
                    <a class="brand-badge" href="@yield('brand_route', route('painel.redirect'))">
                        <img src="{{ asset('images/brand/mania-de-preco-mark.svg') }}" alt="Mania de Preco">
                    </a>
                    <nav class="rail-stack" aria-label="Modulos do backoffice">
                        @foreach ($backofficeModules as $module)
                            <button
                                class="rail-link {{ $module['id'] === $activeBackofficeModule ? 'is-active' : '' }}"
                                type="button"
                                aria-label="Abrir modulo {{ $module['titulo'] }}"
                                aria-controls="backoffice-module-{{ $module['id'] }}"
                                aria-pressed="{{ $module['id'] === $activeBackofficeModule ? 'true' : 'false' }}"
                                data-label="{{ $module['titulo'] }}"
                                data-backoffice-module-trigger="{{ $module['id'] }}"
                            >
                                <x-ui.icon :name="$module['icone']" />
                            </button>
                        @endforeach
                    </nav>
                </div>

                <div class="sidebar-panel">
                    <a class="brand" href="@yield('brand_route', route('painel.redirect'))">
                        <span>@yield('brand_label', 'Mania de Preco')</span>
                    </a>

                    <section class="side-card">
                        <strong>{{ $isSuperAdminArea ? 'Backoffice' : 'Area do cliente' }}</strong>
                        <p>{{ $isSuperAdminArea ? 'Governanca da plataforma com leitura clara de contas, planos, suporte e acesso.' : 'Radar pessoal para acompanhar ofertas, alertas e oportunidades de economia.' }}</p>
                    </section>

                    <nav class="nav" aria-label="Navegacao principal">
                        @foreach ($backofficeModules as $module)
                            <section
                                class="backoffice-module-panel {{ $module['id'] === $activeBackofficeModule ? 'is-active' : '' }}"
                                id="backoffice-module-{{ $module['id'] }}"
                                data-backoffice-module-panel="{{ $module['id'] }}"
                            >
                                <div class="module-kicker">
                                    <span>
                                        <strong>{{ $module['titulo'] }}</strong>
                                        <span>{{ $module['descricao'] }}</span>
                                    </span>
                                    <span class="module-count">{{ $module['items']->count() }}</span>
                                </div>

                                <span class="nav-title">Subitens</span>
                                <div class="menu-links">
                                    @foreach ($module['items'] as $item)
                                        <a class="menu-link {{ $item['active'] ? 'is-active' : '' }}" href="{{ $item['rota'] }}">
                                            <span class="menu-icon"><x-ui.icon :name="$item['icone']" /></span>
                                            <span>{{ $item['titulo'] }}<small>{{ $item['descricao'] }}</small></span>
                                        </a>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </nav>
                </div>
            </aside>

            <div class="page-wrapper">
                <div class="shell">
                    <header class="topbar">
                        <div>
                            <h1>@yield('title', 'Painel')</h1>
                            <p>Ambiente de gestao para acompanhar operacao, crescimento e qualidade da plataforma.</p>
                        </div>

                        <div class="topbar-actions">
                            <div class="topbar-tools">
                                <details class="topbar-menu">
                                    <summary class="icon-button has-caret" aria-label="Abrir notificacoes">
                                        <x-ui.icon name="bell" />
                                        <x-ui.icon name="chevron-down" class="dropdown-caret" />
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
                                    <summary class="icon-button has-caret" aria-label="Abrir atalhos rapidos">
                                        <x-ui.icon name="grid" />
                                        <x-ui.icon name="chevron-down" class="dropdown-caret" />
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
                                        <x-ui.icon name="chevron-down" class="dropdown-caret" />
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
        <script>
            (() => {
                const menus = Array.from(document.querySelectorAll('details.topbar-menu'));

                if (!menus.length) {
                    return;
                }

                menus.forEach((menu) => {
                    menu.addEventListener('toggle', () => {
                        if (!menu.open) {
                            return;
                        }

                        menus.forEach((otherMenu) => {
                            if (otherMenu !== menu) {
                                otherMenu.open = false;
                            }
                        });
                    });
                });

                document.addEventListener('click', (event) => {
                    if (event.target.closest('details.topbar-menu')) {
                        return;
                    }

                    menus.forEach((menu) => {
                        menu.open = false;
                    });
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key !== 'Escape') {
                        return;
                    }

                    menus.forEach((menu) => {
                        menu.open = false;
                    });
                });
            })();

            (() => {
                const triggers = Array.from(document.querySelectorAll('[data-backoffice-module-trigger]'));
                const panels = Array.from(document.querySelectorAll('[data-backoffice-module-panel]'));

                if (!triggers.length || !panels.length) {
                    return;
                }

                const activateModule = (moduleId) => {
                    triggers.forEach((trigger) => {
                        const isActive = trigger.dataset.backofficeModuleTrigger === moduleId;
                        trigger.classList.toggle('is-active', isActive);
                        trigger.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                    });

                    panels.forEach((panel) => {
                        panel.classList.toggle('is-active', panel.dataset.backofficeModulePanel === moduleId);
                    });
                };

                triggers.forEach((trigger) => {
                    trigger.addEventListener('click', () => activateModule(trigger.dataset.backofficeModuleTrigger));
                });
            })();
        </script>
    </body>
</html>
