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
                --bg: #f6f8fb;
                --surface: #ffffff;
                --surface-soft: #f8fafc;
                --line: #e8edf5;
                --line-strong: #d9e2ef;
                --text: #19202e;
                --muted: #687385;
                --primary: #5d87ff;
                --primary-soft: #edf3ff;
                --success: #13deb9;
                --success-soft: #e6fffa;
                --warning: #ffae1f;
                --warning-soft: #fff6e5;
                --danger: #fa896b;
                --danger-soft: #fff1ed;
                --sidebar: #ffffff;
                --rail: #f1f5fb;
                --shadow: 0 16px 34px rgba(31, 42, 68, 0.08);
                --shadow-soft: 0 8px 22px rgba(31, 42, 68, 0.06);
                --radius-xl: 24px;
                --radius-lg: 18px;
                --radius-md: 14px;
                --sidebar-width: 318px;
            }

            * { box-sizing: border-box; }
            body {
                margin: 0;
                min-height: 100vh;
                font-family: "Manrope", sans-serif;
                color: var(--text);
                background:
                    radial-gradient(circle at 12% 0%, rgba(93, 135, 255, 0.14), transparent 28%),
                    radial-gradient(circle at 86% 4%, rgba(19, 222, 185, 0.12), transparent 22%),
                    var(--bg);
            }
            a { color: inherit; text-decoration: none; }
            button, input, select, textarea { font: inherit; }

            .admin-shell { display: grid; grid-template-columns: var(--sidebar-width) minmax(0, 1fr); min-height: 100vh; }
            .sidebar {
                position: sticky;
                top: 0;
                align-self: start;
                display: grid;
                grid-template-columns: 74px minmax(0, 1fr);
                min-height: 100vh;
                background: var(--sidebar);
                border-right: 1px solid var(--line);
                box-shadow: 10px 0 30px rgba(31, 42, 68, 0.03);
                z-index: 20;
            }
            .sidebar-rail {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 12px;
                padding: 18px 12px;
                background: var(--rail);
                border-right: 1px solid var(--line);
            }
            .brand-mark, .rail-link, .menu-icon, .avatar {
                display: inline-grid;
                place-items: center;
                flex: 0 0 auto;
            }
            .brand-mark {
                width: 46px;
                height: 46px;
                border-radius: 16px;
                color: #fff;
                background: linear-gradient(135deg, var(--primary), #7c5cff);
                box-shadow: 0 14px 24px rgba(93, 135, 255, 0.28);
                font: 800 0.9rem "IBM Plex Mono", monospace;
            }
            .rail-stack { display: grid; gap: 10px; width: 100%; margin-top: 14px; }
            .rail-link {
                width: 50px;
                height: 50px;
                border-radius: 16px;
                color: #7a869a;
                background: transparent;
                border: 1px solid transparent;
                font: 700 0.72rem "IBM Plex Mono", monospace;
                transition: 0.18s ease;
            }
            .rail-link:hover, .rail-link.is-active {
                color: var(--primary);
                background: #fff;
                border-color: var(--line);
                box-shadow: var(--shadow-soft);
            }
            .sidebar-panel {
                display: flex;
                flex-direction: column;
                gap: 18px;
                min-height: 100vh;
                padding: 22px 18px;
                overflow-y: auto;
            }
            .brand { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
            .brand-text strong { display: block; font-size: 1.02rem; letter-spacing: -0.02em; }
            .brand-text span { display: block; margin-top: 2px; color: var(--muted); font-size: 0.82rem; }
            .account-panel {
                padding: 16px;
                border-radius: 20px;
                background: linear-gradient(135deg, #22304b, #2f4f9f);
                color: #fff;
                box-shadow: var(--shadow-soft);
                overflow: hidden;
                position: relative;
            }
            .account-panel::after {
                content: "";
                position: absolute;
                right: -48px;
                bottom: -64px;
                width: 150px;
                height: 150px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.12);
            }
            .account-panel h2 { position: relative; margin: 8px 0 8px; font-size: 1.18rem; letter-spacing: -0.04em; }
            .account-panel p { position: relative; margin: 0; color: rgba(255, 255, 255, 0.72); line-height: 1.6; font-size: 0.88rem; }
            .menu-title {
                display: block;
                margin: 16px 4px 8px;
                color: var(--muted);
                font-size: 0.72rem;
                font-weight: 800;
                letter-spacing: 0.12em;
                text-transform: uppercase;
            }
            .menu-links { display: grid; gap: 6px; }
            .menu-link {
                display: grid;
                grid-template-columns: 38px minmax(0, 1fr) auto;
                gap: 12px;
                align-items: center;
                min-height: 48px;
                padding: 8px 10px;
                border-radius: 14px;
                color: #5f6b7a;
                border: 1px solid transparent;
                transition: 0.18s ease;
            }
            .menu-link:hover, .menu-link.is-active {
                color: var(--primary);
                background: var(--primary-soft);
                border-color: #dce7ff;
            }
            .menu-link span:not(.menu-icon) { display: block; color: inherit; font-weight: 800; font-size: 0.92rem; }
            .menu-link small { display: block; margin-top: 2px; color: var(--muted); font-size: 0.74rem; }
            .menu-icon {
                width: 38px;
                height: 38px;
                border-radius: 13px;
                background: #fff;
                border: 1px solid var(--line);
                color: currentColor;
                font: 800 0.72rem "IBM Plex Mono", monospace;
            }
            .account-chip, .pill, .badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: fit-content;
                gap: 8px;
                border-radius: 999px;
                font-weight: 800;
            }
            .account-chip {
                position: relative;
                margin-top: 12px;
                padding: 7px 10px;
                background: rgba(255, 255, 255, 0.14);
                color: #fff;
                font-size: 0.78rem;
            }
            .support-card {
                margin-top: auto;
                padding: 16px;
                border-radius: 18px;
                background: var(--surface-soft);
                border: 1px solid var(--line);
            }
            .support-card strong { display: block; margin-bottom: 6px; }
            .support-card p { margin: 0; color: var(--muted); line-height: 1.6; font-size: 0.86rem; }

            .page-wrapper { min-width: 0; }
            .main {
                display: grid;
                gap: 22px;
                width: min(100% - 36px, 1420px);
                margin: 0 auto;
                padding: 22px 0 52px;
            }
            .topbar {
                position: sticky;
                top: 0;
                z-index: 15;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 18px;
                margin: 0 -18px 4px;
                padding: 18px;
                background: rgba(246, 248, 251, 0.84);
                backdrop-filter: blur(18px);
                border-bottom: 1px solid rgba(232, 237, 245, 0.82);
            }
            .topbar-title { display: flex; gap: 14px; align-items: center; min-width: 0; }
            .topbar-kicker {
                display: block;
                margin-bottom: 6px;
                color: var(--primary);
                font-size: 0.76rem;
                font-weight: 800;
                letter-spacing: 0.12em;
                text-transform: uppercase;
            }
            .topbar h1 { margin: 0; font-size: clamp(1.72rem, 3vw, 2.45rem); line-height: 1.05; letter-spacing: -0.06em; }
            .topbar p { margin: 7px 0 0; color: var(--muted); max-width: 780px; line-height: 1.6; }
            .topbar-actions, .toolbar-actions, .filter-row, .subnav, .list-actions, .checklist-actions {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }
            .avatar {
                width: 44px;
                height: 44px;
                border-radius: 50%;
                background: #fff;
                border: 1px solid var(--line);
                color: var(--primary);
                font-weight: 900;
                box-shadow: var(--shadow-soft);
                overflow: hidden;
            }
            .avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            .topbar-tools {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
                justify-content: flex-end;
            }
            .topbar-menu {
                position: relative;
            }
            .topbar-menu summary {
                list-style: none;
            }
            .topbar-menu summary::-webkit-details-marker {
                display: none;
            }
            .icon-button, .profile-trigger {
                min-height: 44px;
                border-radius: 14px;
                border: 1px solid var(--line);
                background: #fff;
                color: var(--text);
                cursor: pointer;
                box-shadow: 0 1px 0 rgba(31, 42, 68, 0.02);
                transition: 0.18s ease;
            }
            .icon-button {
                position: relative;
                display: inline-grid;
                place-items: center;
                width: 44px;
                font: 900 0.72rem "IBM Plex Mono", monospace;
            }
            .notification-dot {
                position: absolute;
                top: 8px;
                right: 8px;
                display: grid;
                place-items: center;
                min-width: 17px;
                height: 17px;
                padding: 0 5px;
                border-radius: 999px;
                background: var(--danger);
                color: #fff;
                font-size: 0.68rem;
                line-height: 1;
            }
            .profile-trigger {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 6px 12px 6px 6px;
            }
            .profile-trigger strong {
                display: block;
                font-size: 0.9rem;
                line-height: 1.1;
            }
            .profile-trigger small {
                display: block;
                margin-top: 2px;
                color: var(--muted);
                font-size: 0.76rem;
            }
            .dropdown-panel {
                position: absolute;
                top: calc(100% + 12px);
                right: 0;
                width: min(360px, calc(100vw - 28px));
                padding: 14px;
                border-radius: 20px;
                background: #fff;
                border: 1px solid var(--line);
                box-shadow: var(--shadow);
                z-index: 50;
            }
            .dropdown-panel h3 {
                margin: 0 0 10px;
                font-size: 1rem;
                letter-spacing: -0.02em;
            }
            .dropdown-list {
                display: grid;
                gap: 10px;
            }
            .notification-item, .profile-row, .quick-link {
                display: grid;
                gap: 4px;
                padding: 12px;
                border-radius: 14px;
                background: var(--surface-soft);
                border: 1px solid var(--line);
            }
            .notification-item strong, .quick-link strong {
                display: block;
                font-size: 0.9rem;
            }
            .notification-item span, .quick-link span, .profile-row span {
                color: var(--muted);
                font-size: 0.82rem;
                line-height: 1.5;
            }
            .profile-row {
                grid-template-columns: 44px minmax(0, 1fr);
                align-items: center;
            }
            .profile-actions {
                display: grid;
                gap: 8px;
                margin-top: 10px;
            }
            .profile-actions form {
                margin: 0;
            }
            .ghost-link, .logout-button, .button, .button-secondary, .button-danger {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 42px;
                padding: 11px 15px;
                border-radius: 12px;
                border: 1px solid var(--line);
                background: #fff;
                color: var(--text);
                font-weight: 800;
                cursor: pointer;
                transition: 0.18s ease;
                box-shadow: 0 1px 0 rgba(31, 42, 68, 0.02);
            }
            .button {
                color: #fff;
                border-color: transparent;
                background: linear-gradient(135deg, var(--primary), #7c5cff);
                box-shadow: 0 12px 22px rgba(93, 135, 255, 0.22);
            }
            .button-secondary { background: var(--surface-soft); }
            .logout-button {
                color: #fff;
                border-color: #172033;
                background: #172033;
            }
            .button-danger {
                color: #be4f39;
                border-color: #ffd9cf;
                background: var(--danger-soft);
            }
            .ghost-link:hover, .logout-button:hover, .button:hover, .button-secondary:hover, .button-danger:hover {
                transform: translateY(-1px);
                box-shadow: var(--shadow-soft);
            }

            .content, .stack, .mini-grid, .progress-stack, .signal-list, .onboarding-groups, .checklist-stack, .table-head, .list-grid { display: grid; gap: 16px; }
            .card {
                background: var(--surface);
                border: 1px solid var(--line);
                border-radius: var(--radius-xl);
                box-shadow: var(--shadow-soft);
            }
            .card-body { padding: 22px; }
            .grid-4, .grid-3, .grid-2, .stats-grid, .highlight-grid, .panel-grid, .month-grid, .form-grid {
                display: grid;
                gap: 16px;
            }
            .grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
            .grid-3, .stats-grid, .highlight-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .grid-2, .panel-grid { grid-template-columns: minmax(0, 1.2fr) minmax(320px, 0.8fr); }
            .form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .month-grid { grid-template-columns: repeat(6, minmax(0, 1fr)); align-items: end; }

            .metric-card, .highlight-card, .mini-card, .stat-card-soft, .month-card, .signal-item, .onboarding-group, .checklist-item, .table-row, .list-row {
                border-radius: var(--radius-lg);
                background: var(--surface);
                border: 1px solid var(--line);
            }
            .metric-card { padding: 20px; overflow: hidden; position: relative; }
            .metric-card::after {
                content: "";
                position: absolute;
                right: -30px;
                top: -30px;
                width: 90px;
                height: 90px;
                border-radius: 50%;
                background: var(--primary-soft);
            }
            .metric-label { display: block; margin-bottom: 12px; color: var(--muted); font-size: 0.92rem; font-weight: 700; }
            .metric-value { display: block; position: relative; font-size: clamp(1.65rem, 3vw, 2.35rem); line-height: 1; letter-spacing: -0.06em; }
            .metric-trend {
                display: inline-flex;
                align-items: center;
                margin-top: 14px;
                padding: 7px 10px;
                border-radius: 999px;
                background: var(--success-soft);
                color: #0f8f78;
                font-size: 0.82rem;
                font-weight: 800;
            }
            .metric-trend.is-danger { background: var(--danger-soft); color: #be4f39; }

            .section-header, .toolbar {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 14px;
            }
            .section-header h2, .section-header h3 { margin: 0; font-size: 1.16rem; letter-spacing: -0.03em; }
            .section-header p, .helper-text, .mini-card span, .mini-card small, .stat-card-soft span, .signal-item span, .signal-item small, .table-row small, .list-row small {
                color: var(--muted);
                line-height: 1.6;
            }
            .section-header p { margin: 7px 0 0; }
            .mini-card, .highlight-card, .stat-card-soft, .month-card, .signal-item, .onboarding-group, .checklist-item, .table-row, .list-row { padding: 16px; }
            .mini-card strong, .highlight-card strong, .stat-card-soft strong, .signal-item strong, .table-row strong, .list-row strong {
                display: block;
                margin-bottom: 6px;
            }
            .highlight-card strong, .stat-card-soft strong { font-size: 1.45rem; letter-spacing: -0.04em; }

            .pill, .badge {
                padding: 8px 11px;
                border: 1px solid #dce7ff;
                background: var(--primary-soft);
                color: var(--primary);
                font-size: 0.8rem;
            }
            .badge.is-warning { border-color: #ffe5b8; background: var(--warning-soft); color: #b76d00; }
            .badge.is-muted { border-color: var(--line); background: var(--surface-soft); color: var(--muted); }

            .flash-box, .error-box, .empty-state {
                padding: 15px 17px;
                border-radius: var(--radius-lg);
                line-height: 1.7;
            }
            .flash-box { background: var(--success-soft); border: 1px solid #c8f7ed; color: #0f8f78; }
            .error-box { background: var(--danger-soft); border: 1px solid #ffd9cf; color: #a54631; }
            .empty-state { background: var(--surface-soft); border: 1px dashed var(--line-strong); color: var(--muted); }

            .table-list, .list { display: grid; gap: 12px; }
            .table-head, .list-row {
                grid-template-columns: minmax(0, 1.2fr) minmax(160px, 0.5fr) minmax(160px, 0.5fr) auto;
                align-items: center;
            }
            .table-head {
                padding: 0 10px;
                color: var(--muted);
                font-size: 0.78rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.08em;
            }
            .table-row { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 16px; align-items: center; }
            .list-row { display: grid; gap: 14px; }
            .list-row code, .table-row code {
                color: var(--muted);
                font-family: "IBM Plex Mono", monospace;
                font-size: 0.82rem;
            }

            .field-group, .field-group-full { display: grid; gap: 8px; }
            .field-group-full { grid-column: 1 / -1; }
            .field-group label, .field-group-full label { font-size: 0.9rem; font-weight: 800; }
            .field-group input, .field-group select, .field-group textarea, .field-group-full input, .field-group-full select, .field-group-full textarea, .filter-row input, .filter-row select {
                width: 100%;
                min-height: 46px;
                padding: 12px 14px;
                border-radius: 12px;
                border: 1px solid var(--line);
                background: #fff;
                color: var(--text);
                outline: none;
                transition: 0.18s ease;
            }
            .field-group textarea, .field-group-full textarea { min-height: 130px; resize: vertical; }
            input:focus, select:focus, textarea:focus {
                border-color: rgba(93, 135, 255, 0.55);
                box-shadow: 0 0 0 4px rgba(93, 135, 255, 0.10);
            }
            .form-actions {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
                margin-top: 8px;
            }
            .inline-form { margin: 0; }
            .remember-toggle { display: inline-flex; align-items: center; gap: 10px; font-weight: 700; }
            .remember-toggle input { width: auto; accent-color: var(--primary); }

            .progress-track {
                height: 10px;
                border-radius: 999px;
                background: #eef2f7;
                overflow: hidden;
            }
            .progress-fill {
                display: block;
                height: 100%;
                border-radius: inherit;
                background: linear-gradient(90deg, var(--warning), var(--danger));
            }
            .progress-fill.is-teal { background: linear-gradient(90deg, var(--success), #44d7ff); }
            .setup-banner {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 20px;
            }
            .setup-banner h2 { margin: 12px 0 8px; font-size: clamp(1.35rem, 3vw, 1.95rem); letter-spacing: -0.04em; }
            .setup-banner p { margin: 0; color: var(--muted); line-height: 1.7; max-width: 760px; }
            .setup-banner-actions { display: grid; gap: 12px; min-width: min(360px, 100%); }
            .checklist-item { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 16px; align-items: center; }
            .checklist-item.is-done { background: var(--success-soft); border-color: #c8f7ed; }
            .status-dot { width: 12px; height: 12px; border-radius: 50%; background: #d8dee8; }
            .status-dot.is-done { background: var(--success); box-shadow: 0 0 0 6px rgba(19, 222, 185, 0.12); }
            .status-dot.is-pending { background: var(--warning); box-shadow: 0 0 0 6px rgba(255, 174, 31, 0.13); }

            .month-bars, .month-legend, .month-bar-group { display: grid; gap: 8px; }
            .month-bar { height: 10px; border-radius: 999px; background: #eef2f7; overflow: hidden; }
            .month-bar > span { display: block; height: 100%; border-radius: inherit; }
            .month-bar.is-receita > span { background: linear-gradient(90deg, var(--success), #44d7ff); }
            .month-bar.is-despesa > span { background: linear-gradient(90deg, var(--danger), var(--warning)); }
            .pagination-wrap nav { display: flex; justify-content: center; }
            .pagination-wrap svg { width: 18px; height: 18px; }
            .subnav-link {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 10px 13px;
                border-radius: 999px;
                border: 1px solid var(--line);
                background: #fff;
                color: var(--muted);
                font-weight: 800;
                font-size: 0.9rem;
            }
            .subnav-link.is-active, .subnav-link:hover { color: var(--primary); background: var(--primary-soft); border-color: #dce7ff; }

            .mobile-context, .mobile-dock { display: none; }
            .mobile-context {
                padding: 16px;
                border-radius: 20px;
                background: #172033;
                color: #fff;
                box-shadow: var(--shadow-soft);
            }
            .mobile-context-row { display: flex; justify-content: space-between; align-items: center; gap: 12px; }
            .mobile-context strong { display: block; margin-bottom: 4px; }
            .mobile-context small { color: rgba(255, 255, 255, 0.72); line-height: 1.5; }
            .mobile-dock {
                position: fixed;
                left: 12px;
                right: 12px;
                bottom: 12px;
                z-index: 40;
                grid-template-columns: repeat(auto-fit, minmax(62px, 1fr));
                gap: 8px;
                padding: 8px;
                border-radius: 22px;
                background: rgba(255, 255, 255, 0.92);
                border: 1px solid var(--line);
                box-shadow: var(--shadow);
                backdrop-filter: blur(18px);
            }
            .mobile-dock-link {
                display: grid;
                gap: 3px;
                justify-items: center;
                padding: 9px 6px;
                border-radius: 16px;
                color: var(--muted);
                text-align: center;
                font-size: 0.72rem;
            }
            .mobile-dock-link strong { color: inherit; font-size: 0.8rem; }
            .mobile-dock-link.is-active, .mobile-dock-link:hover { color: var(--primary); background: var(--primary-soft); }

            @media (max-width: 1180px) {
                .admin-shell { grid-template-columns: 1fr; }
                .sidebar { display: none; }
                .main { width: min(100% - 28px, 1180px); padding-bottom: 108px; }
                .mobile-context, .mobile-dock { display: grid; }
                .grid-4, .grid-3, .grid-2, .stats-grid, .form-grid, .panel-grid, .highlight-grid, .month-grid { grid-template-columns: 1fr; }
                .table-head, .list-row { grid-template-columns: 1fr; }
            }
            @media (max-width: 720px) {
                .main { width: min(100% - 20px, 1180px); padding-top: 12px; }
                .topbar { margin: 0 -10px 0; padding: 14px 10px; flex-direction: column; align-items: stretch; }
                .topbar-actions, .topbar-tools, .section-header, .toolbar, .toolbar-actions, .filter-row, .form-actions, .list-actions, .subnav, .checklist-actions, .setup-banner { flex-direction: column; align-items: stretch; }
                .topbar-title { align-items: flex-start; }
                .avatar { display: none; }
                .profile-trigger .avatar { display: inline-grid; }
                .topbar-menu, .icon-button, .profile-trigger { width: 100%; }
                .dropdown-panel { position: static; width: 100%; margin-top: 10px; }
                .checklist-item, .table-row { grid-template-columns: 1fr; }
                .ghost-link, .logout-button, .button, .button-secondary, .button-danger { width: 100%; }
            }
        </style>
    </head>
    <body>
        <div class="admin-shell">
            <aside class="sidebar">
                <div class="sidebar-rail">
                    <a class="brand-mark" href="{{ route('admin.dashboard') }}">MP</a>
                    <nav class="rail-stack" aria-label="Atalhos do painel">
                        <a class="rail-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">IN</a>
                        @if (in_array('financeiro', $capacidadesConta, true))
                            <a class="rail-link {{ request()->routeIs('admin.financeiro.*') ? 'is-active' : '' }}" href="{{ route('admin.financeiro.index') }}">FI</a>
                        @endif
                        @if (in_array('catalogo', $capacidadesConta, true))
                            <a class="rail-link {{ request()->routeIs('admin.produtos.*') ? 'is-active' : '' }}" href="{{ route('admin.produtos.index') }}">PR</a>
                        @endif
                        @if (in_array('lojas', $capacidadesConta, true))
                            <a class="rail-link {{ request()->routeIs('admin.lojas.*') ? 'is-active' : '' }}" href="{{ route('admin.lojas.index') }}">LJ</a>
                        @endif
                    </nav>
                </div>

                <div class="sidebar-panel">
                    <a class="brand" href="{{ route('admin.dashboard') }}">
                        <span class="brand-text">
                            <strong>Mania de Preco</strong>
                            <span>Painel lojista</span>
                        </span>
                    </a>

                    <section class="account-panel">
                        <span class="menu-title" style="margin:0; color:rgba(255,255,255,.62);">Conta ativa</span>
                        <h2>{{ $conta->nome_fantasia }}</h2>
                        <p>Operacao, catalogo, financeiro e precos em uma unica cabine de comando.</p>
                        <span class="account-chip">{{ $assinaturaAtual?->status ?? 'sem assinatura' }}</span>
                        @if (! empty($papelAtualConta))
                            <span class="account-chip">{{ $papelAtualConta }}</span>
                        @endif
                    </section>

                    <nav class="menu-card" aria-label="Navegacao administrativa">
                        <span class="menu-title">Geral</span>
                        <div class="menu-links">
                            <a class="menu-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <span class="menu-icon">IN</span>
                                <span>Dashboard<small>visao geral</small></span>
                            </a>
                            <a class="menu-link {{ request()->routeIs('admin.onboarding') ? 'is-active' : '' }}" href="{{ route('admin.onboarding') }}">
                                <span class="menu-icon">ON</span>
                                <span>Onboarding<small>implantacao</small></span>
                            </a>
                        </div>

                        <span class="menu-title">Operacao</span>
                        <div class="menu-links">
                            @if (in_array('lojas', $capacidadesConta, true))
                                <a class="menu-link {{ request()->routeIs('admin.lojas.*') ? 'is-active' : '' }}" href="{{ route('admin.lojas.index') }}">
                                    <span class="menu-icon">LJ</span>
                                    <span>Lojas<small>operacao</small></span>
                                </a>
                            @endif
                            @if (in_array('catalogo', $capacidadesConta, true))
                                <a class="menu-link {{ request()->routeIs('admin.produtos.*') ? 'is-active' : '' }}" href="{{ route('admin.produtos.index') }}">
                                    <span class="menu-icon">PR</span>
                                    <span>Produtos<small>catalogo</small></span>
                                </a>
                            @endif
                            @if (in_array('precos', $capacidadesConta, true))
                                <a class="menu-link {{ request()->routeIs('admin.precos.*') ? 'is-active' : '' }}" href="{{ route('admin.precos.index') }}">
                                    <span class="menu-icon">PC</span>
                                    <span>Precos<small>comparador</small></span>
                                </a>
                            @endif
                            @if (in_array('financeiro', $capacidadesConta, true))
                                <a class="menu-link {{ request()->routeIs('admin.financeiro.*') ? 'is-active' : '' }}" href="{{ route('admin.financeiro.index') }}">
                                    <span class="menu-icon">FI</span>
                                    <span>Financeiro<small>caixa e titulos</small></span>
                                </a>
                            @endif
                        </div>

                        @if (in_array('gestao', $capacidadesConta, true) || in_array('equipe', $capacidadesConta, true))
                            <span class="menu-title">Gestao</span>
                            <div class="menu-links">
                                @if (in_array('gestao', $capacidadesConta, true))
                                    <a class="menu-link {{ request()->routeIs('admin.assinatura') ? 'is-active' : '' }}" href="{{ route('admin.assinatura') }}">
                                        <span class="menu-icon">AS</span>
                                        <span>Assinatura<small>plano e cobranca</small></span>
                                    </a>
                                    <a class="menu-link {{ request()->routeIs('admin.configuracoes.*') ? 'is-active' : '' }}" href="{{ route('admin.configuracoes.edit') }}">
                                        <span class="menu-icon">CO</span>
                                        <span>Configuracoes<small>minha empresa</small></span>
                                    </a>
                                @endif
                                @if (in_array('equipe', $capacidadesConta, true))
                                    <a class="menu-link {{ request()->routeIs('admin.equipe.*') ? 'is-active' : '' }}" href="{{ route('admin.equipe.index') }}">
                                        <span class="menu-icon">EQ</span>
                                        <span>Equipe<small>acessos e papeis</small></span>
                                    </a>
                                    <a class="menu-link {{ request()->routeIs('admin.auditoria') ? 'is-active' : '' }}" href="{{ route('admin.auditoria') }}">
                                        <span class="menu-icon">AU</span>
                                        <span>Auditoria<small>historico de acoes</small></span>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </nav>

                    <section class="support-card">
                        <strong>Operacao guiada</strong>
                        <p>Use o menu por contexto: primeiro venda e catalogo, depois financeiro, equipe e ajustes de conta.</p>
                    </section>
                </div>
            </aside>

            <div class="page-wrapper">
                <main class="main">
                    <section class="mobile-context">
                        <div class="mobile-context-row">
                            <div>
                                <strong>{{ $conta->nome_fantasia }}</strong>
                                <small>@yield('heading')</small>
                            </div>
                            <span class="account-chip">{{ $assinaturaAtual?->status ?? 'sem assinatura' }}</span>
                        </div>
                    </section>

                    @php
                        $usuarioTopbar = auth()->user();
                        $nomeUsuarioTopbar = $usuarioTopbar?->name ?? 'Usuario';
                        $avatarUsuarioTopbar = $usuarioTopbar?->avatar_url;
                        $iniciaisTopbar = collect(preg_split('/\s+/', trim($nomeUsuarioTopbar)))
                            ->filter()
                            ->take(2)
                            ->map(fn ($parte) => mb_strtoupper(mb_substr($parte, 0, 1)))
                            ->implode('') ?: 'U';

                        $notificacoesTopbar = [];

                        if (! $assinaturaAtual) {
                            $notificacoesTopbar[] = [
                                'titulo' => 'Assinatura pendente',
                                'descricao' => 'Defina um plano para manter limites, cobranca e crescimento sob controle.',
                                'rota' => in_array('gestao', $capacidadesConta, true) ? route('admin.assinatura') : null,
                            ];
                        } elseif (in_array($assinaturaAtual->status, ['inadimplente', 'cancelada'], true)) {
                            $notificacoesTopbar[] = [
                                'titulo' => 'Atencao na assinatura',
                                'descricao' => 'O status atual pode afetar operacao, limites ou cobranca da conta.',
                                'rota' => in_array('gestao', $capacidadesConta, true) ? route('admin.assinatura') : null,
                            ];
                        } elseif ($assinaturaAtual->expira_em && $assinaturaAtual->expira_em->isBefore(now()->addDays(7))) {
                            $notificacoesTopbar[] = [
                                'titulo' => 'Vigencia perto do fim',
                                'descricao' => 'Revise a assinatura para evitar interrupcoes comerciais.',
                                'rota' => in_array('gestao', $capacidadesConta, true) ? route('admin.assinatura') : null,
                            ];
                        }

                        if ($conta->trial_ends_at && $conta->trial_ends_at->isFuture() && $conta->trial_ends_at->isBefore(now()->addDays(5))) {
                            $notificacoesTopbar[] = [
                                'titulo' => 'Trial quase terminando',
                                'descricao' => 'A conta esta perto do fim do periodo de teste.',
                                'rota' => in_array('gestao', $capacidadesConta, true) ? route('admin.assinatura') : null,
                            ];
                        }

                        if ($notificacoesTopbar === []) {
                            $notificacoesTopbar[] = [
                                'titulo' => 'Operacao sem alertas criticos',
                                'descricao' => 'Continue acompanhando catalogo, financeiro e precos pela rotina do painel.',
                                'rota' => route('admin.dashboard'),
                            ];
                        }

                        $atalhosTopbar = collect([
                            ['titulo' => 'Financeiro', 'descricao' => 'Caixa, titulos e movimentacoes.', 'rota' => route('admin.financeiro.index'), 'capacidade' => 'financeiro'],
                            ['titulo' => 'Produtos', 'descricao' => 'Catalogo e vitrine publica.', 'rota' => route('admin.produtos.index'), 'capacidade' => 'catalogo'],
                            ['titulo' => 'Precos', 'descricao' => 'Comparador e ofertas.', 'rota' => route('admin.precos.index'), 'capacidade' => 'precos'],
                            ['titulo' => 'Lojas', 'descricao' => 'Operacao e presenca local.', 'rota' => route('admin.lojas.index'), 'capacidade' => 'lojas'],
                        ])->filter(fn ($atalho) => in_array($atalho['capacidade'], $capacidadesConta, true))->take(3);
                    @endphp

                    <header class="topbar">
                        <div class="topbar-title">
                            <span class="avatar">
                                @if ($avatarUsuarioTopbar)
                                    <img src="{{ $avatarUsuarioTopbar }}" alt="Foto de {{ $nomeUsuarioTopbar }}">
                                @else
                                    {{ $iniciaisTopbar }}
                                @endif
                            </span>
                            <div>
                                <span class="topbar-kicker">Painel lojista</span>
                                <h1>@yield('heading')</h1>
                                <p>@yield('subheading')</p>
                            </div>
                        </div>

                        <div class="topbar-actions">
                            <div class="topbar-tools">
                                <details class="topbar-menu">
                                    <summary class="icon-button" aria-label="Abrir notificacoes">
                                        NT
                                        <span class="notification-dot">{{ count($notificacoesTopbar) }}</span>
                                    </summary>
                                    <div class="dropdown-panel">
                                        <h3>Notificacoes</h3>
                                        <div class="dropdown-list">
                                            @foreach ($notificacoesTopbar as $notificacao)
                                                @if ($notificacao['rota'])
                                                    <a class="notification-item" href="{{ $notificacao['rota'] }}">
                                                        <strong>{{ $notificacao['titulo'] }}</strong>
                                                        <span>{{ $notificacao['descricao'] }}</span>
                                                    </a>
                                                @else
                                                    <div class="notification-item">
                                                        <strong>{{ $notificacao['titulo'] }}</strong>
                                                        <span>{{ $notificacao['descricao'] }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </details>

                                <details class="topbar-menu">
                                    <summary class="icon-button" aria-label="Abrir atalhos rapidos">AT</summary>
                                    <div class="dropdown-panel">
                                        <h3>Atalhos rapidos</h3>
                                        <div class="dropdown-list">
                                            @foreach ($atalhosTopbar as $atalho)
                                                <a class="quick-link" href="{{ $atalho['rota'] }}">
                                                    <strong>{{ $atalho['titulo'] }}</strong>
                                                    <span>{{ $atalho['descricao'] }}</span>
                                                </a>
                                            @endforeach
                                            <a class="quick-link" href="{{ url('/') }}">
                                                <strong>Home publica</strong>
                                                <span>Veja a experiencia que o cliente acessa.</span>
                                            </a>
                                        </div>
                                    </div>
                                </details>

                                <details class="topbar-menu">
                                    <summary class="profile-trigger" aria-label="Abrir menu do usuario">
                                        <span class="avatar">
                                            @if ($avatarUsuarioTopbar)
                                                <img src="{{ $avatarUsuarioTopbar }}" alt="Foto de {{ $nomeUsuarioTopbar }}">
                                            @else
                                                {{ $iniciaisTopbar }}
                                            @endif
                                        </span>
                                        <span>
                                            <strong>{{ $nomeUsuarioTopbar }}</strong>
                                            <small>{{ $papelAtualConta ?: 'usuario' }} | {{ $assinaturaAtual?->status ?? 'sem assinatura' }}</small>
                                        </span>
                                    </summary>
                                    <div class="dropdown-panel">
                                        <h3>Minha conta</h3>
                                        <div class="profile-row">
                                            <span class="avatar">
                                                @if ($avatarUsuarioTopbar)
                                                    <img src="{{ $avatarUsuarioTopbar }}" alt="Foto de {{ $nomeUsuarioTopbar }}">
                                                @else
                                                    {{ $iniciaisTopbar }}
                                                @endif
                                            </span>
                                            <span>
                                                <strong>{{ $nomeUsuarioTopbar }}</strong>
                                                <span>{{ $usuarioTopbar?->email }}</span>
                                            </span>
                                        </div>
                                        <div class="profile-actions">
                                            <a class="ghost-link" href="{{ route('admin.perfil.edit') }}">Meu perfil</a>
                                            @if (in_array('gestao', $capacidadesConta, true))
                                                <a class="ghost-link" href="{{ route('admin.configuracoes.edit') }}">Configuracoes da conta</a>
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

                    <section class="content">
                        @if (session('status'))
                            <div class="flash-box">{{ session('status') }}</div>
                        @endif

                        @yield('content')
                    </section>
                </main>
            </div>
        </div>

        <nav class="mobile-dock" aria-label="Navegacao principal do admin">
            <a class="mobile-dock-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">
                <strong>Inicio</strong>
                <span>painel</span>
            </a>
            <a class="mobile-dock-link {{ request()->routeIs('admin.perfil.*') ? 'is-active' : '' }}" href="{{ route('admin.perfil.edit') }}">
                <strong>Perfil</strong>
                <span>acesso</span>
            </a>
            @if (in_array('financeiro', $capacidadesConta, true))
                <a class="mobile-dock-link {{ request()->routeIs('admin.financeiro.*') ? 'is-active' : '' }}" href="{{ route('admin.financeiro.index') }}">
                    <strong>Financeiro</strong>
                    <span>caixa</span>
                </a>
            @endif
            @if (in_array('catalogo', $capacidadesConta, true))
                <a class="mobile-dock-link {{ request()->routeIs('admin.produtos.*') ? 'is-active' : '' }}" href="{{ route('admin.produtos.index') }}">
                    <strong>Produtos</strong>
                    <span>catalogo</span>
                </a>
            @endif
            @if (in_array('lojas', $capacidadesConta, true))
                <a class="mobile-dock-link {{ request()->routeIs('admin.lojas.*') ? 'is-active' : '' }}" href="{{ route('admin.lojas.index') }}">
                    <strong>Lojas</strong>
                    <span>operacao</span>
                </a>
            @endif
            @if (in_array('equipe', $capacidadesConta, true))
                <a class="mobile-dock-link {{ request()->routeIs('admin.equipe.*') ? 'is-active' : '' }}" href="{{ route('admin.equipe.index') }}">
                    <strong>Equipe</strong>
                    <span>acessos</span>
                </a>
            @elseif (in_array('gestao', $capacidadesConta, true))
                <a class="mobile-dock-link {{ request()->routeIs('admin.configuracoes.*') ? 'is-active' : '' }}" href="{{ route('admin.configuracoes.edit') }}">
                    <strong>Conta</strong>
                    <span>empresa</span>
                </a>
            @elseif (in_array('onboarding', $capacidadesConta, true))
                <a class="mobile-dock-link {{ request()->routeIs('admin.onboarding') ? 'is-active' : '' }}" href="{{ route('admin.onboarding') }}">
                    <strong>Setup</strong>
                    <span>onboarding</span>
                </a>
            @endif
        </nav>
    </body>
</html>
