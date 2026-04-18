<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Painel') | Mania de Preco</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|ibm-plex-mono:400,500" rel="stylesheet" />

        <style>
            :root {
                --bg: #f4ecdf;
                --bg-strong: #1f130e;
                --surface: rgba(255, 250, 242, 0.88);
                --surface-soft: rgba(255, 255, 255, 0.68);
                --line: rgba(78, 43, 25, 0.12);
                --text: #24140e;
                --muted: #715349;
                --accent: #ff6b2c;
                --accent-2: #0f9f8f;
                --accent-3: #ffd08a;
                --success: #177245;
                --danger: #b64833;
                --shadow: 0 24px 60px rgba(57, 29, 16, 0.12);
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
                    radial-gradient(circle at top left, rgba(255, 107, 44, 0.18), transparent 30%),
                    radial-gradient(circle at top right, rgba(15, 159, 143, 0.16), transparent 24%),
                    linear-gradient(180deg, #fff7ea 0%, #f4ecdf 48%, #efe3d0 100%);
            }

            a {
                color: inherit;
                text-decoration: none;
            }

            .admin-shell {
                display: grid;
                grid-template-columns: 280px minmax(0, 1fr);
                min-height: 100vh;
            }

            .sidebar {
                position: relative;
                padding: 28px 22px;
                background:
                    linear-gradient(180deg, rgba(42, 22, 15, 0.98) 0%, rgba(26, 14, 10, 0.98) 100%);
                color: #fff6ef;
                overflow: hidden;
            }

            .sidebar::after {
                content: "";
                position: absolute;
                inset: auto -40px -40px auto;
                width: 200px;
                height: 200px;
                background: rgba(255, 107, 44, 0.16);
                border-radius: 50%;
                filter: blur(20px);
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

            .account-panel,
            .menu-card,
            .support-card {
                position: relative;
                z-index: 1;
                margin-top: 24px;
                padding: 18px;
                border-radius: 20px;
                background: rgba(255, 255, 255, 0.07);
                border: 1px solid rgba(255, 255, 255, 0.08);
                backdrop-filter: blur(10px);
            }

            .account-panel p,
            .support-card p {
                margin: 8px 0 0;
                color: rgba(255, 244, 236, 0.74);
                line-height: 1.6;
                font-size: 0.95rem;
            }

            .account-chip {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 12px;
                border-radius: 999px;
                margin-top: 14px;
                background: rgba(255, 208, 138, 0.14);
                color: #ffd9a7;
                font-size: 0.85rem;
            }

            .menu-title,
            .support-card strong {
                display: block;
                font-size: 0.92rem;
                text-transform: uppercase;
                letter-spacing: 0.12em;
                color: rgba(255, 244, 236, 0.62);
            }

            .menu-links {
                display: grid;
                gap: 10px;
                margin-top: 14px;
            }

            .menu-link {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 14px 16px;
                border-radius: 16px;
                background: rgba(255, 255, 255, 0.04);
                border: 1px solid transparent;
                transition: 0.2s ease;
            }

            .menu-link:hover,
            .menu-link.is-active {
                transform: translateX(2px);
                border-color: rgba(255, 208, 138, 0.24);
                background: rgba(255, 255, 255, 0.1);
            }

            .menu-link small {
                color: rgba(255, 244, 236, 0.58);
            }

            .main {
                padding: 28px;
            }

            .topbar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 18px;
                margin-bottom: 24px;
            }

            .topbar h1 {
                margin: 0;
                font-size: clamp(2rem, 4vw, 3.1rem);
                line-height: 1;
            }

            .topbar p {
                margin: 8px 0 0;
                color: var(--muted);
                max-width: 760px;
                line-height: 1.7;
            }

            .topbar-actions {
                display: flex;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
            }

            .ghost-link,
            .logout-button,
            .button,
            .button-secondary,
            .button-danger {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 12px 16px;
                border-radius: 14px;
                border: 1px solid var(--line);
                background: rgba(255, 255, 255, 0.72);
                color: var(--text);
                font-weight: 600;
                font-family: inherit;
                cursor: pointer;
                transition: 0.2s ease;
            }

            .logout-button {
                background: #2a160f;
                border-color: #2a160f;
                color: #fff5ef;
            }

            .button {
                background: linear-gradient(135deg, var(--accent), #ff9f52);
                border-color: transparent;
                color: #2d150d;
                box-shadow: 0 18px 36px rgba(255, 107, 44, 0.18);
            }

            .button-secondary {
                background: rgba(255, 255, 255, 0.72);
            }

            .button-danger {
                background: rgba(182, 72, 51, 0.1);
                border-color: rgba(182, 72, 51, 0.12);
                color: var(--danger);
            }

            .ghost-link:hover,
            .logout-button:hover,
            .button:hover,
            .button-secondary:hover,
            .button-danger:hover {
                transform: translateY(-1px);
            }

            .content {
                display: grid;
                gap: 18px;
            }

            .card {
                background: var(--surface);
                border: 1px solid rgba(255, 255, 255, 0.7);
                border-radius: var(--radius-lg);
                box-shadow: var(--shadow);
                backdrop-filter: blur(14px);
            }

            .card-body {
                padding: 24px;
            }

            .grid-4,
            .grid-3,
            .grid-2 {
                display: grid;
                gap: 18px;
            }

            .grid-4 {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }

            .grid-3 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .grid-2 {
                grid-template-columns: minmax(0, 1.3fr) minmax(320px, 0.7fr);
            }

            .metric-card {
                padding: 22px;
            }

            .metric-label {
                display: block;
                margin-bottom: 12px;
                color: var(--muted);
                font-size: 0.95rem;
            }

            .metric-value {
                display: block;
                font-size: clamp(1.8rem, 3vw, 2.7rem);
                line-height: 1;
                font-weight: 700;
            }

            .metric-trend {
                display: inline-flex;
                align-items: center;
                margin-top: 12px;
                padding: 8px 10px;
                border-radius: 999px;
                background: rgba(15, 159, 143, 0.12);
                color: var(--accent-2);
                font-size: 0.86rem;
            }

            .metric-trend.is-danger {
                background: rgba(182, 72, 51, 0.12);
                color: var(--danger);
            }

            .section-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 18px;
            }

            .section-header h2,
            .section-header h3 {
                margin: 0;
                font-size: 1.2rem;
            }

            .section-header p {
                margin: 8px 0 0;
                color: var(--muted);
                line-height: 1.6;
            }

            .mini-grid {
                display: grid;
                gap: 12px;
            }

            .mini-card {
                padding: 16px 18px;
                border-radius: 18px;
                background: rgba(255, 255, 255, 0.7);
                border: 1px solid rgba(76, 42, 22, 0.08);
            }

            .mini-card strong {
                display: block;
                margin-bottom: 6px;
                font-size: 1.05rem;
            }

            .mini-card span,
            .mini-card small,
            .table-list small {
                color: var(--muted);
                line-height: 1.6;
            }

            .table-list {
                display: grid;
                gap: 12px;
            }

            .table-row {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                gap: 16px;
                padding: 16px 18px;
                border-radius: 18px;
                background: rgba(255, 255, 255, 0.66);
                border: 1px solid rgba(76, 42, 22, 0.08);
            }

            .table-row strong {
                display: block;
                margin-bottom: 4px;
            }

            .table-row code {
                font-family: "IBM Plex Mono", monospace;
                font-size: 0.82rem;
                color: var(--muted);
            }

            .badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                height: fit-content;
                padding: 8px 12px;
                border-radius: 999px;
                font-size: 0.82rem;
                background: rgba(15, 159, 143, 0.12);
                color: var(--accent-2);
            }

            .badge.is-warning {
                background: rgba(255, 107, 44, 0.12);
                color: var(--accent);
            }

            .badge.is-muted {
                background: rgba(60, 34, 24, 0.08);
                color: var(--muted);
            }

            .empty-state {
                padding: 26px;
                border-radius: 20px;
                background: rgba(255, 255, 255, 0.7);
                border: 1px dashed rgba(76, 42, 22, 0.18);
                color: var(--muted);
                line-height: 1.8;
            }

            .flash-box,
            .error-box {
                padding: 16px 18px;
                border-radius: 18px;
                line-height: 1.7;
            }

            .flash-box {
                background: rgba(15, 159, 143, 0.08);
                border: 1px solid rgba(15, 159, 143, 0.12);
                color: #0a7167;
            }

            .error-box {
                background: rgba(182, 72, 51, 0.08);
                border: 1px solid rgba(182, 72, 51, 0.12);
                color: #8c3525;
            }

            .toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                flex-wrap: wrap;
            }

            .toolbar-actions,
            .filter-row {
                display: flex;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
            }

            .stack {
                display: grid;
                gap: 18px;
            }

            .stats-grid {
                display: grid;
                gap: 14px;
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .stat-card-soft {
                padding: 18px;
                border-radius: 18px;
                background: rgba(255, 255, 255, 0.7);
                border: 1px solid rgba(76, 42, 22, 0.08);
            }

            .stat-card-soft strong {
                display: block;
                margin-bottom: 6px;
                font-size: 1.4rem;
            }

            .stat-card-soft span {
                color: var(--muted);
                line-height: 1.6;
            }

            .table-head,
            .list-grid {
                display: grid;
                gap: 12px;
            }

            .table-head {
                grid-template-columns: minmax(0, 1.2fr) minmax(160px, 0.5fr) minmax(160px, 0.5fr) auto;
                padding: 0 10px;
                color: var(--muted);
                font-size: 0.86rem;
                text-transform: uppercase;
                letter-spacing: 0.08em;
            }

            .list-row {
                display: grid;
                grid-template-columns: minmax(0, 1.2fr) minmax(160px, 0.5fr) minmax(160px, 0.5fr) auto;
                gap: 16px;
                align-items: center;
                padding: 18px;
                border-radius: 18px;
                background: rgba(255, 255, 255, 0.68);
                border: 1px solid rgba(76, 42, 22, 0.08);
            }

            .list-row strong {
                display: block;
                margin-bottom: 4px;
            }

            .list-row small,
            .list-row code {
                color: var(--muted);
                line-height: 1.6;
            }

            .list-row code {
                font-family: "IBM Plex Mono", monospace;
            }

            .list-actions {
                display: inline-flex;
                gap: 10px;
                flex-wrap: wrap;
                justify-content: flex-end;
            }

            .pill {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 8px 12px;
                border-radius: 999px;
                font-size: 0.82rem;
                background: rgba(255, 255, 255, 0.78);
                border: 1px solid rgba(76, 42, 22, 0.08);
                color: var(--muted);
            }

            .form-grid {
                display: grid;
                gap: 16px;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .field-group,
            .field-group-full {
                display: grid;
                gap: 8px;
            }

            .field-group-full {
                grid-column: 1 / -1;
            }

            .field-group label,
            .field-group-full label {
                font-size: 0.94rem;
                font-weight: 600;
            }

            .field-group input,
            .field-group select,
            .field-group textarea,
            .field-group-full input,
            .field-group-full select,
            .field-group-full textarea,
            .filter-row input,
            .filter-row select {
                width: 100%;
                padding: 14px 16px;
                border-radius: 14px;
                border: 1px solid rgba(76, 42, 22, 0.12);
                background: rgba(255, 255, 255, 0.92);
                font: inherit;
                color: var(--text);
            }

            .field-group textarea,
            .field-group-full textarea {
                min-height: 140px;
                resize: vertical;
            }

            .field-group small,
            .field-group-full small {
                color: var(--muted);
                line-height: 1.6;
            }

            .form-actions {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
                margin-top: 8px;
            }

            .inline-form {
                margin: 0;
            }

            .helper-text {
                color: var(--muted);
                line-height: 1.7;
            }

            .remember-toggle {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                font-weight: 500;
                color: var(--text);
            }

            .remember-toggle input {
                width: auto;
                accent-color: var(--accent);
            }

            .pagination-wrap nav {
                display: flex;
                justify-content: center;
            }

            .pagination-wrap svg {
                width: 18px;
                height: 18px;
            }

            .subnav {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }

            .subnav-link {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 10px 14px;
                border-radius: 999px;
                border: 1px solid rgba(76, 42, 22, 0.1);
                background: rgba(255, 255, 255, 0.68);
                color: var(--muted);
                font-size: 0.92rem;
                transition: 0.2s ease;
            }

            .subnav-link.is-active,
            .subnav-link:hover {
                color: var(--text);
                border-color: rgba(255, 107, 44, 0.22);
                background: rgba(255, 240, 230, 0.9);
            }

            .panel-grid {
                display: grid;
                grid-template-columns: minmax(0, 1.2fr) minmax(320px, 0.8fr);
                gap: 18px;
            }

            .month-grid {
                display: grid;
                grid-template-columns: repeat(6, minmax(0, 1fr));
                gap: 12px;
                align-items: end;
            }

            .month-card {
                padding: 16px 14px;
                border-radius: 18px;
                background: rgba(255, 255, 255, 0.72);
                border: 1px solid rgba(76, 42, 22, 0.08);
            }

            .month-card strong {
                display: block;
                font-size: 0.9rem;
                margin-bottom: 12px;
                color: var(--muted);
            }

            .month-bars {
                display: grid;
                gap: 8px;
            }

            .month-bar-group {
                display: grid;
                gap: 6px;
            }

            .month-bar {
                position: relative;
                height: 10px;
                border-radius: 999px;
                background: rgba(44, 24, 17, 0.08);
                overflow: hidden;
            }

            .month-bar > span {
                display: block;
                height: 100%;
                border-radius: inherit;
            }

            .month-bar.is-receita > span {
                background: linear-gradient(90deg, #0f9f8f, #34d399);
            }

            .month-bar.is-despesa > span {
                background: linear-gradient(90deg, #ff6b2c, #f59e0b);
            }

            .month-legend {
                display: grid;
                gap: 4px;
                margin-top: 10px;
                color: var(--muted);
                font-size: 0.82rem;
            }

            .progress-stack {
                display: grid;
                gap: 14px;
            }

            .progress-row {
                display: grid;
                gap: 8px;
            }

            .progress-meta {
                display: flex;
                justify-content: space-between;
                gap: 12px;
                color: var(--muted);
                font-size: 0.9rem;
            }

            .progress-track {
                height: 12px;
                border-radius: 999px;
                background: rgba(44, 24, 17, 0.08);
                overflow: hidden;
            }

            .progress-fill {
                display: block;
                height: 100%;
                border-radius: inherit;
                background: linear-gradient(90deg, var(--accent), #ffb16c);
            }

            .progress-fill.is-teal {
                background: linear-gradient(90deg, var(--accent-2), #5eead4);
            }

            .highlight-grid {
                display: grid;
                gap: 14px;
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .setup-banner {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 20px;
            }

            .setup-banner h2 {
                margin: 14px 0 8px;
                font-size: clamp(1.4rem, 3vw, 2rem);
            }

            .setup-banner p {
                margin: 0;
                color: var(--muted);
                line-height: 1.7;
                max-width: 760px;
            }

            .setup-banner-actions {
                display: grid;
                gap: 12px;
                min-width: min(360px, 100%);
            }

            .onboarding-groups,
            .checklist-stack {
                display: grid;
                gap: 14px;
            }

            .onboarding-group {
                display: grid;
                gap: 12px;
                padding: 18px;
                border-radius: 20px;
                background: rgba(255, 255, 255, 0.72);
                border: 1px solid rgba(76, 42, 22, 0.08);
            }

            .checklist-item {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                gap: 16px;
                align-items: center;
                padding: 16px 18px;
                border-radius: 18px;
                background: rgba(255, 255, 255, 0.82);
                border: 1px solid rgba(76, 42, 22, 0.08);
            }

            .checklist-item.is-done {
                background: rgba(226, 255, 249, 0.72);
                border-color: rgba(15, 159, 143, 0.12);
            }

            .checklist-item strong {
                display: block;
                margin-bottom: 6px;
            }

            .checklist-item span {
                color: var(--muted);
                line-height: 1.6;
            }

            .checklist-actions {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
                justify-content: flex-end;
            }

            .status-dot {
                width: 12px;
                height: 12px;
                border-radius: 50%;
                background: rgba(60, 34, 24, 0.16);
            }

            .status-dot.is-done {
                background: var(--accent-2);
                box-shadow: 0 0 0 6px rgba(15, 159, 143, 0.12);
            }

            .status-dot.is-pending {
                background: var(--accent);
                box-shadow: 0 0 0 6px rgba(255, 107, 44, 0.12);
            }

            .highlight-card {
                padding: 18px;
                border-radius: 20px;
                background: rgba(255, 255, 255, 0.72);
                border: 1px solid rgba(76, 42, 22, 0.08);
            }

            .highlight-card strong {
                display: block;
                font-size: 1.6rem;
                margin-bottom: 8px;
            }

            .highlight-card span,
            .highlight-card small {
                color: var(--muted);
                line-height: 1.6;
            }

            .signal-list {
                display: grid;
                gap: 12px;
            }

            .signal-item {
                display: grid;
                gap: 6px;
                padding: 16px 18px;
                border-radius: 18px;
                background: rgba(255, 255, 255, 0.72);
                border: 1px solid rgba(76, 42, 22, 0.08);
            }

            .signal-item strong {
                display: block;
                font-size: 1rem;
            }

            .signal-item small,
            .signal-item span {
                color: var(--muted);
                line-height: 1.6;
            }

            @media (max-width: 1180px) {
                .admin-shell {
                    grid-template-columns: 1fr;
                }

                .sidebar {
                    padding-bottom: 20px;
                }

                .grid-4,
                .grid-3,
                .grid-2,
                .stats-grid,
                .form-grid,
                .panel-grid,
                .highlight-grid,
                .month-grid {
                    grid-template-columns: 1fr;
                }

                .table-head,
                .list-row {
                    grid-template-columns: 1fr;
                    padding-left: 0;
                    padding-right: 0;
                }
            }

            @media (max-width: 720px) {
                .main {
                    padding: 18px;
                }

                .setup-banner,
                .topbar {
                    flex-direction: column;
                    align-items: stretch;
                }

                .topbar-actions,
                .section-header,
                .toolbar,
                .toolbar-actions,
                .filter-row,
                .form-actions,
                .list-actions,
                .subnav,
                .checklist-actions {
                    flex-direction: column;
                    align-items: stretch;
                }

                .checklist-item {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        <div class="admin-shell">
            <aside class="sidebar">
                <a class="brand" href="{{ route('admin.dashboard') }}">
                    <span class="brand-badge">MP</span>
                    <span>Mania de Preco</span>
                </a>

                <section class="account-panel">
                    <span class="menu-title">Conta ativa</span>
                    <h2 style="margin: 10px 0 0; font-size: 1.5rem;">{{ $conta->nome_fantasia }}</h2>
                    <p>Area administrativa para operar financeiro, catalogo e estrategia de precos em um unico lugar.</p>
                    <span class="account-chip">
                        {{ $assinaturaAtual?->status ?? 'sem assinatura' }}
                    </span>
                </section>

                <nav class="menu-card">
                    <span class="menu-title">Navegacao</span>
                    <div class="menu-links">
                        <a class="menu-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <span>Dashboard</span>
                            <small>visao geral</small>
                        </a>
                        <a class="menu-link {{ request()->routeIs('admin.onboarding') ? 'is-active' : '' }}" href="{{ route('admin.onboarding') }}">
                            <span>Onboarding</span>
                            <small>implantacao</small>
                        </a>
                        <a class="menu-link {{ request()->routeIs('admin.lojas.*') ? 'is-active' : '' }}" href="{{ route('admin.lojas.index') }}">
                            <span>Lojas</span>
                            <small>operacao</small>
                        </a>
                        <a class="menu-link {{ request()->routeIs('admin.produtos.*') ? 'is-active' : '' }}" href="{{ route('admin.produtos.index') }}">
                            <span>Produtos</span>
                            <small>catalogo</small>
                        </a>
                        <a class="menu-link {{ request()->routeIs('admin.precos.*') ? 'is-active' : '' }}" href="{{ route('admin.precos.index') }}">
                            <span>Precos</span>
                            <small>comparador</small>
                        </a>
                        <a class="menu-link {{ request()->routeIs('admin.financeiro.*') ? 'is-active' : '' }}" href="{{ route('admin.financeiro.index') }}">
                            <span>Financeiro</span>
                            <small>caixa e titulos</small>
                        </a>
                    </div>
                </nav>

                <section class="support-card">
                    <strong>Base de teste</strong>
                    <p>Essa primeira versao do painel ja conversa com a conta real do usuario logado e prepara o terreno para onboarding, catalogo e operacao financeira.</p>
                </section>
            </aside>

            <main class="main">
                <header class="topbar">
                    <div>
                        <h1>@yield('heading')</h1>
                        <p>@yield('subheading')</p>
                    </div>

                    <div class="topbar-actions">
                        <a class="ghost-link" href="{{ url('/') }}">Ver home publica</a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="logout-button" type="submit">Sair</button>
                        </form>
                    </div>
                </header>

                <section class="content">
                    @if (session('status'))
                        <div class="flash-box">{{ session('status') }}</div>
                    @endif

                    @yield('content')
                </section>
            </main>
        </div>
    </body>
</html>
