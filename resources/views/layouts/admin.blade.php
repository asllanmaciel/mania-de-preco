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
                --bg: #f7f2ea;
                --surface: #ffffff;
                --surface-soft: #fff8f0;
                --line: #ece0d4;
                --line-strong: #dccbbc;
                --text: #19202e;
                --muted: #687385;
                --primary: #f45a24;
                --primary-soft: #fff0e8;
                --success: #0b8f80;
                --success-soft: #e6fbf7;
                --warning: #d69a27;
                --warning-soft: #fff7e4;
                --danger: #ef5b35;
                --danger-soft: #fff1ed;
                --sidebar: #ffffff;
                --rail: #fff4ea;
                --shadow: 0 16px 34px rgba(31, 42, 68, 0.08);
                --shadow-soft: 0 8px 22px rgba(31, 42, 68, 0.06);
                --radius-xl: 24px;
                --radius-lg: 18px;
                --radius-md: 14px;
                --sidebar-width: 318px;
                --font-sans: "Plus Jakarta Sans", sans-serif;
                --font-mono: "IBM Plex Mono", monospace;
                --tracking-tight: -0.045em;
            }

            * { box-sizing: border-box; }
            html { text-rendering: optimizeLegibility; -webkit-font-smoothing: antialiased; }
            body {
                margin: 0;
                min-height: 100vh;
                font-family: var(--font-sans);
                color: var(--text);
                background:
                    radial-gradient(circle at 12% 0%, rgba(93, 135, 255, 0.14), transparent 28%),
                    radial-gradient(circle at 86% 4%, rgba(19, 222, 185, 0.12), transparent 22%),
                    var(--bg);
            }
            a { color: inherit; text-decoration: none; }
            button, input, select, textarea { font: inherit; }
            .ui-icon {
                display: inline-block;
                flex: 0 0 auto;
                width: 1.15em;
                height: 1.15em;
                stroke-width: 2.1;
                vertical-align: -0.18em;
            }

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
                background: #21140f;
                box-shadow: 0 14px 24px rgba(244, 90, 36, 0.24);
                font: 800 0.9rem var(--font-mono);
                overflow: hidden;
            }
            .brand-mark img { width: 100%; height: 100%; object-fit: cover; display: block; }
            .rail-stack { display: grid; gap: 10px; width: 100%; margin-top: 14px; }
            .rail-link {
                position: relative;
                width: 50px;
                height: 50px;
                border-radius: 16px;
                color: #7a869a;
                background: transparent;
                border: 1px solid transparent;
                padding: 0;
                transition: 0.18s ease;
                cursor: pointer;
            }
            .rail-link:hover, .rail-link.is-active {
                color: var(--primary);
                background: #fff;
                border-color: var(--line);
                box-shadow: var(--shadow-soft);
            }
            .rail-link:focus-visible {
                outline: 3px solid rgba(244, 90, 36, 0.18);
                outline-offset: 3px;
            }
            .rail-link::after {
                content: attr(data-label);
                position: absolute;
                left: calc(100% + 12px);
                top: 50%;
                z-index: 60;
                min-width: max-content;
                max-width: 180px;
                padding: 8px 10px;
                border-radius: 11px;
                color: #fff;
                background: #172033;
                box-shadow: var(--shadow-soft);
                font-size: 0.75rem;
                font-weight: 800;
                line-height: 1;
                opacity: 0;
                pointer-events: none;
                transform: translate(4px, -50%);
                transition: 0.16s ease;
            }
            .rail-link::before {
                content: "";
                position: absolute;
                left: calc(100% + 6px);
                top: 50%;
                z-index: 61;
                width: 8px;
                height: 8px;
                border-radius: 2px;
                background: #172033;
                opacity: 0;
                pointer-events: none;
                transform: translate(4px, -50%) rotate(45deg);
                transition: 0.16s ease;
            }
            .rail-link:hover::after,
            .rail-link:hover::before,
            .rail-link:focus-visible::after,
            .rail-link:focus-visible::before {
                opacity: 1;
                transform: translate(0, -50%);
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
            .sidebar-module-panel { display: none; }
            .sidebar-module-panel.is-active { display: block; }
            .module-kicker {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin: 6px 4px 12px;
            }
            .module-kicker strong {
                display: block;
                font-size: 1rem;
                letter-spacing: -0.03em;
            }
            .module-kicker span {
                display: block;
                margin-top: 2px;
                color: var(--muted);
                font-size: 0.78rem;
                line-height: 1.45;
            }
            .module-count {
                display: inline-grid;
                place-items: center;
                min-width: 30px;
                height: 30px;
                padding: 0 9px;
                border-radius: 999px;
                color: var(--primary);
                background: var(--primary-soft);
                border: 1px solid var(--line);
                font: 800 0.74rem var(--font-mono);
            }
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
                font-size: 1.05rem;
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
                width: 100%;
                margin: 0;
                padding: 0 18px 52px;
            }
            .topbar {
                position: sticky;
                top: 0;
                z-index: 15;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                min-height: 74px;
                margin: 0 -18px;
                padding: 14px 18px;
                background: rgba(246, 248, 251, 0.84);
                backdrop-filter: blur(18px);
                border-bottom: 1px solid rgba(232, 237, 245, 0.82);
            }
            .topbar-left {
                display: flex;
                align-items: center;
                gap: 12px;
                min-width: 0;
            }
            .topbar-search {
                display: flex;
                align-items: center;
                gap: 12px;
                min-height: 44px;
                width: min(420px, 42vw);
                padding: 8px 14px;
                border-radius: 16px;
                background: rgba(255, 255, 255, 0.72);
                border: 1px solid var(--line);
                box-shadow: 0 1px 0 rgba(31, 42, 68, 0.02);
                color: var(--muted);
                cursor: pointer;
                transition: 0.18s ease;
            }
            .topbar-search:hover {
                background: #fff;
                box-shadow: var(--shadow-soft);
            }
            .topbar-search strong {
                display: block;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                color: var(--text);
                font-size: 0.88rem;
                font-weight: 800;
            }
            .topbar-search kbd {
                margin-left: auto;
                padding: 4px 7px;
                border-radius: 8px;
                border: 1px solid var(--line);
                background: #fff;
                color: var(--muted);
                font: 800 0.72rem var(--font-mono);
            }
            .topbar-search .dropdown-caret {
                margin-left: 0;
            }
            .topbar-compact-brand {
                display: none;
                align-items: center;
                gap: 10px;
                font-weight: 900;
            }
            .page-heading {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 18px;
                padding: 24px 0 0;
            }
            .page-heading-copy {
                min-width: 0;
            }
            .page-heading-kicker {
                display: block;
                margin-bottom: 6px;
                color: var(--primary);
                font-size: 0.76rem;
                font-weight: 800;
                letter-spacing: 0.12em;
                text-transform: uppercase;
            }
            .page-heading h1 { margin: 0; font-size: clamp(1.85rem, 3.2vw, 2.95rem); line-height: 1.02; letter-spacing: var(--tracking-tight); text-wrap: balance; }
            .page-heading p { margin: 10px 0 0; color: var(--muted); max-width: 780px; line-height: 1.7; }
            .page-heading-actions {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 10px;
                flex-wrap: wrap;
            }
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
                gap: 8px;
                flex-wrap: nowrap;
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
                font-size: 1.05rem;
            }
            .icon-button.has-caret {
                grid-template-columns: auto auto;
                width: auto;
                min-width: 50px;
                gap: 4px;
                padding: 0 11px;
            }
            .dropdown-caret {
                width: 14px;
                height: 14px;
                color: var(--muted);
                transition: 0.18s ease;
            }
            .topbar-menu[open] .dropdown-caret {
                transform: rotate(180deg);
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
                padding: 6px 11px 6px 6px;
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
            .command-panel {
                left: 0;
                right: auto;
                width: min(520px, calc(100vw - 28px));
                padding: 16px;
            }
            .command-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 12px;
            }
            .command-head h3 {
                margin: 0;
            }
            .command-head span {
                padding: 5px 8px;
                border-radius: 9px;
                border: 1px solid var(--line);
                color: var(--muted);
                font: 800 0.72rem var(--font-mono);
            }
            .command-input {
                display: flex;
                align-items: center;
                gap: 10px;
                min-height: 46px;
                padding: 0 13px;
                border-radius: 14px;
                border: 1px solid var(--line);
                background: var(--surface-soft);
                color: var(--muted);
            }
            .command-input input {
                width: 100%;
                border: 0;
                outline: 0;
                background: transparent;
                color: var(--text);
                font-weight: 700;
            }
            .command-input input::placeholder {
                color: var(--muted);
            }
            .command-list {
                display: grid;
                gap: 9px;
                max-height: 390px;
                margin-top: 12px;
                overflow-y: auto;
                padding-right: 4px;
            }
            .command-item {
                display: grid;
                grid-template-columns: 42px minmax(0, 1fr) auto;
                align-items: center;
                gap: 12px;
                padding: 11px;
                border-radius: 15px;
                border: 1px solid var(--line);
                background: #fff;
                transition: 0.18s ease;
            }
            .command-item:hover {
                border-color: #dce7ff;
                background: var(--primary-soft);
                transform: translateY(-1px);
            }
            .command-item strong,
            .command-item span {
                display: block;
            }
            .command-item strong {
                font-size: 0.92rem;
            }
            .command-item span {
                margin-top: 2px;
                color: var(--muted);
                font-size: 0.82rem;
                line-height: 1.45;
            }
            .command-item small {
                color: var(--muted);
                font-size: 0.74rem;
                font-weight: 900;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }
            .command-empty {
                display: none;
                padding: 14px;
                border-radius: 14px;
                background: var(--surface-soft);
                color: var(--muted);
                text-align: center;
                font-weight: 800;
            }
            .command-empty.is-visible {
                display: block;
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
            .profile-account-card {
                position: relative;
                display: grid;
                gap: 8px;
                margin-top: 10px;
                padding: 14px;
                border-radius: 16px;
                color: #fff;
                background: linear-gradient(135deg, #22304b, #2f4f9f);
                box-shadow: var(--shadow-soft);
                overflow: hidden;
            }
            .profile-account-card::after {
                content: "";
                position: absolute;
                right: -42px;
                bottom: -58px;
                width: 132px;
                height: 132px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.12);
            }
            .profile-account-card > * {
                position: relative;
            }
            .profile-account-card small {
                color: rgba(255, 255, 255, 0.62);
                font-size: 0.72rem;
                font-weight: 800;
                letter-spacing: 0.12em;
                text-transform: uppercase;
            }
            .profile-account-card strong {
                font-size: 1rem;
                letter-spacing: -0.03em;
            }
            .profile-account-card p {
                margin: 0;
                color: rgba(255, 255, 255, 0.72);
                line-height: 1.55;
                font-size: 0.84rem;
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
                gap: 8px;
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
                background: linear-gradient(135deg, var(--primary), #ba3c16);
                box-shadow: 0 12px 22px rgba(244, 90, 36, 0.22);
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
                font-family: var(--font-mono);
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
            .metric-head {
                position: relative;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 12px;
            }
            .metric-icon, .visual-icon {
                display: inline-grid;
                place-items: center;
                width: 42px;
                height: 42px;
                border-radius: 15px;
                color: var(--primary);
                background: var(--primary-soft);
                border: 1px solid #dce7ff;
            }
            .metric-icon.is-teal, .visual-icon.is-teal {
                color: #0f8f78;
                background: var(--success-soft);
                border-color: #c8f7ed;
            }
            .metric-icon.is-warning, .visual-icon.is-warning {
                color: #b76d00;
                background: var(--warning-soft);
                border-color: #ffe5b8;
            }
            .metric-icon.is-danger, .visual-icon.is-danger {
                color: #be4f39;
                background: var(--danger-soft);
                border-color: #ffd9cf;
            }
            .visual-hero {
                display: grid;
                grid-template-columns: minmax(0, 1fr) minmax(260px, 0.36fr);
                gap: 18px;
                align-items: stretch;
                overflow: hidden;
                background:
                    radial-gradient(circle at 12% 0%, rgba(93, 135, 255, 0.16), transparent 28%),
                    linear-gradient(135deg, #ffffff, #f8fbff);
            }
            .visual-hero-copy {
                display: grid;
                align-content: center;
                gap: 16px;
            }
            .visual-action-grid {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }
            .score-ring {
                --score: 0;
                display: grid;
                place-items: center;
                min-height: 220px;
                border-radius: 22px;
                background: conic-gradient(var(--success) calc(var(--score) * 1%), #e9eef7 0);
                padding: 16px;
                box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.72);
            }
            .score-ring-inner {
                display: grid;
                place-items: center;
                width: 100%;
                height: 100%;
                min-height: 188px;
                border-radius: 18px;
                background: #fff;
                text-align: center;
                padding: 18px;
            }
            .score-ring-inner strong {
                display: block;
                font-size: clamp(2.4rem, 5vw, 4rem);
                line-height: 0.95;
                letter-spacing: -0.08em;
            }
            .score-ring-inner span { color: var(--muted); font-weight: 800; }
            .chart-card {
                position: relative;
                overflow: hidden;
            }
            .chart-card::after {
                content: "";
                position: absolute;
                inset: auto -70px -90px auto;
                width: 210px;
                height: 210px;
                border-radius: 50%;
                background: rgba(93, 135, 255, 0.08);
            }
            .chart-legend {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
                color: var(--muted);
                font-size: 0.84rem;
                font-weight: 800;
            }
            .legend-dot {
                display: inline-flex;
                align-items: center;
                gap: 7px;
            }
            .legend-dot::before {
                content: "";
                width: 10px;
                height: 10px;
                border-radius: 999px;
                background: var(--success);
            }
            .legend-dot.is-danger::before { background: var(--danger); }
            .legend-dot.is-warning::before { background: var(--warning); }
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
                .topbar-compact-brand { display: inline-flex; }
                .topbar-search { width: min(360px, 38vw); }
                .main { padding-bottom: 108px; }
                .mobile-context, .mobile-dock { display: grid; }
                .grid-4, .grid-3, .grid-2, .stats-grid, .form-grid, .panel-grid, .highlight-grid, .month-grid, .visual-hero { grid-template-columns: 1fr; }
                .table-head, .list-row { grid-template-columns: 1fr; }
            }
            @media (max-width: 720px) {
                .main { padding: 12px 10px 108px; }
                .topbar { margin: -12px -10px 0; padding: 12px 10px; align-items: center; }
                .topbar-search { display: none; }
                .topbar-actions, .topbar-tools { justify-content: flex-end; }
                .section-header, .toolbar, .toolbar-actions, .filter-row, .form-actions, .list-actions, .subnav, .checklist-actions, .setup-banner { flex-direction: column; align-items: stretch; }
                .page-heading { align-items: flex-start; flex-direction: column; padding-top: 18px; }
                .page-heading-actions { justify-content: flex-start; }
                .avatar { display: none; }
                .profile-trigger .avatar { display: inline-grid; }
                .profile-trigger > span:last-child { display: none; }
                .topbar-menu, .icon-button, .profile-trigger { width: auto; }
                .dropdown-panel { position: static; width: 100%; margin-top: 10px; }
                .checklist-item, .table-row { grid-template-columns: 1fr; }
                .ghost-link, .logout-button, .button, .button-secondary, .button-danger { width: 100%; }
            }
        </style>
    </head>
    <body>
        <div class="admin-shell">
            @php
                $sidebarModules = collect([
                    [
                        'id' => 'geral',
                        'titulo' => 'Geral',
                        'descricao' => 'Visao, lancamento e acoes da conta.',
                        'icone' => 'home',
                        'active' => request()->routeIs('admin.dashboard')
                            || request()->routeIs('admin.lancamento')
                            || request()->routeIs('admin.onboarding')
                            || request()->routeIs('admin.notificacoes'),
                        'items' => collect([
                            ['titulo' => 'Dashboard', 'descricao' => 'visao geral', 'rota' => route('admin.dashboard'), 'icone' => 'home', 'active' => request()->routeIs('admin.dashboard')],
                            ['titulo' => 'Lancamento', 'descricao' => 'prontidao', 'rota' => route('admin.lancamento'), 'icone' => 'spark', 'active' => request()->routeIs('admin.lancamento')],
                            ['titulo' => 'Onboarding', 'descricao' => 'implantacao', 'rota' => route('admin.onboarding'), 'icone' => 'compass', 'active' => request()->routeIs('admin.onboarding'), 'capacidade' => 'onboarding'],
                            ['titulo' => 'Notificacoes', 'descricao' => 'central de acoes', 'rota' => route('admin.notificacoes'), 'icone' => 'bell', 'active' => request()->routeIs('admin.notificacoes')],
                        ]),
                    ],
                    [
                        'id' => 'vitrine',
                        'titulo' => 'Vitrine',
                        'descricao' => 'Lojas, produtos e precos publicados.',
                        'icone' => 'store',
                        'active' => request()->routeIs('admin.lojas.*')
                            || request()->routeIs('admin.produtos.*')
                            || request()->routeIs('admin.precos.*'),
                        'items' => collect([
                            ['titulo' => 'Lojas', 'descricao' => 'operacao', 'rota' => route('admin.lojas.index'), 'icone' => 'store', 'active' => request()->routeIs('admin.lojas.*'), 'capacidade' => 'lojas'],
                            ['titulo' => 'Produtos', 'descricao' => 'catalogo', 'rota' => route('admin.produtos.index'), 'icone' => 'package', 'active' => request()->routeIs('admin.produtos.*'), 'capacidade' => 'catalogo'],
                            ['titulo' => 'Precos', 'descricao' => 'comparador', 'rota' => route('admin.precos.index'), 'icone' => 'tag', 'active' => request()->routeIs('admin.precos.*'), 'capacidade' => 'precos'],
                        ]),
                    ],
                    [
                        'id' => 'financeiro',
                        'titulo' => 'Financeiro',
                        'descricao' => 'Caixa, titulos e classificacoes.',
                        'icone' => 'wallet',
                        'active' => request()->routeIs('admin.financeiro.*'),
                        'items' => collect([
                            ['titulo' => 'Resumo financeiro', 'descricao' => 'painel de caixa', 'rota' => route('admin.financeiro.index'), 'icone' => 'wallet', 'active' => request()->routeIs('admin.financeiro.index'), 'capacidade' => 'financeiro'],
                            ['titulo' => 'Lancamentos', 'descricao' => 'entradas e saidas', 'rota' => route('admin.financeiro.lancamentos.index'), 'icone' => 'tag', 'active' => request()->routeIs('admin.financeiro.lancamentos.*'), 'capacidade' => 'financeiro'],
                            ['titulo' => 'Contas', 'descricao' => 'bancos e carteiras', 'rota' => route('admin.financeiro.contas.index'), 'icone' => 'credit-card', 'active' => request()->routeIs('admin.financeiro.contas.*'), 'capacidade' => 'financeiro'],
                            ['titulo' => 'Categorias', 'descricao' => 'classificacao', 'rota' => route('admin.financeiro.categorias.index'), 'icone' => 'grid', 'active' => request()->routeIs('admin.financeiro.categorias.*'), 'capacidade' => 'financeiro'],
                            ['titulo' => 'Contas a pagar', 'descricao' => 'compromissos', 'rota' => route('admin.financeiro.contas-pagar.index'), 'icone' => 'shield', 'active' => request()->routeIs('admin.financeiro.contas-pagar.*'), 'capacidade' => 'financeiro'],
                            ['titulo' => 'Contas a receber', 'descricao' => 'recebiveis', 'rota' => route('admin.financeiro.contas-receber.index'), 'icone' => 'spark', 'active' => request()->routeIs('admin.financeiro.contas-receber.*'), 'capacidade' => 'financeiro'],
                        ]),
                    ],
                    [
                        'id' => 'gestao',
                        'titulo' => 'Gestao',
                        'descricao' => 'Conta, equipe, plano e seguranca.',
                        'icone' => 'settings',
                        'active' => request()->routeIs('admin.assinatura')
                            || request()->routeIs('admin.configuracoes.*')
                            || request()->routeIs('admin.equipe.*')
                            || request()->routeIs('admin.auditoria')
                            || request()->routeIs('admin.perfil.*'),
                        'items' => collect([
                            ['titulo' => 'Meu perfil', 'descricao' => 'usuario e senha', 'rota' => route('admin.perfil.edit'), 'icone' => 'user', 'active' => request()->routeIs('admin.perfil.*')],
                            ['titulo' => 'Assinatura', 'descricao' => 'plano e cobranca', 'rota' => route('admin.assinatura'), 'icone' => 'credit-card', 'active' => request()->routeIs('admin.assinatura'), 'capacidade' => 'gestao'],
                            ['titulo' => 'Configuracoes', 'descricao' => 'minha empresa', 'rota' => route('admin.configuracoes.edit'), 'icone' => 'settings', 'active' => request()->routeIs('admin.configuracoes.*'), 'capacidade' => 'gestao'],
                            ['titulo' => 'Equipe', 'descricao' => 'acessos e papeis', 'rota' => route('admin.equipe.index'), 'icone' => 'users', 'active' => request()->routeIs('admin.equipe.*'), 'capacidade' => 'equipe'],
                            ['titulo' => 'Auditoria', 'descricao' => 'historico de acoes', 'rota' => route('admin.auditoria'), 'icone' => 'shield', 'active' => request()->routeIs('admin.auditoria'), 'capacidade' => 'equipe'],
                        ]),
                    ],
                ])->map(function (array $module) use ($capacidadesConta) {
                    $module['items'] = $module['items']
                        ->filter(fn (array $item) => empty($item['capacidade']) || in_array($item['capacidade'], $capacidadesConta, true))
                        ->values();

                    return $module;
                })->filter(fn (array $module) => $module['items']->isNotEmpty())->values();

                $activeSidebarModule = $sidebarModules->firstWhere('active', true)['id'] ?? $sidebarModules->first()['id'];
            @endphp

            <aside class="sidebar">
                <div class="sidebar-rail">
                    <a class="brand-mark" href="{{ route('admin.dashboard') }}">
                        <img src="{{ asset('images/brand/mania-de-preco-mark.svg') }}" alt="Mania de Preco">
                    </a>
                    <nav class="rail-stack" aria-label="Modulos do painel">
                        @foreach ($sidebarModules as $module)
                            <button
                                class="rail-link {{ $module['id'] === $activeSidebarModule ? 'is-active' : '' }}"
                                type="button"
                                aria-label="Abrir modulo {{ $module['titulo'] }}"
                                aria-controls="sidebar-module-{{ $module['id'] }}"
                                aria-pressed="{{ $module['id'] === $activeSidebarModule ? 'true' : 'false' }}"
                                data-label="{{ $module['titulo'] }}"
                                data-sidebar-module-trigger="{{ $module['id'] }}"
                            >
                                <x-ui.icon :name="$module['icone']" />
                            </button>
                        @endforeach
                    </nav>
                </div>

                <div class="sidebar-panel">
                    <a class="brand" href="{{ route('admin.dashboard') }}">
                        <span class="brand-text">
                            <strong>Mania de Preco</strong>
                            <span>Painel lojista</span>
                        </span>
                    </a>

                    <nav class="menu-card" aria-label="Navegacao administrativa">
                        @foreach ($sidebarModules as $module)
                            <section
                                class="sidebar-module-panel {{ $module['id'] === $activeSidebarModule ? 'is-active' : '' }}"
                                id="sidebar-module-{{ $module['id'] }}"
                                data-sidebar-module-panel="{{ $module['id'] }}"
                            >
                                <div class="module-kicker">
                                    <span>
                                        <strong>{{ $module['titulo'] }}</strong>
                                        <span>{{ $module['descricao'] }}</span>
                                    </span>
                                    <span class="module-count">{{ $module['items']->count() }}</span>
                                </div>

                                <span class="menu-title">Subitens</span>
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

                    <section class="support-card">
                        <strong>Operacao guiada</strong>
                        <p>Escolha um modulo na coluna de icones e use os subitens para navegar com mais contexto.</p>
                    </section>
                </div>
            </aside>

            <div class="page-wrapper">
                <main class="main">
                    @php
                        $usuarioTopbar = auth()->user();
                        $nomeUsuarioTopbar = $usuarioTopbar?->name ?? 'Usuario';
                        $avatarUsuarioTopbar = $usuarioTopbar?->avatar_url;
                        $iniciaisTopbar = collect(preg_split('/\s+/', trim($nomeUsuarioTopbar)))
                            ->filter()
                            ->take(2)
                            ->map(fn ($parte) => mb_strtoupper(mb_substr($parte, 0, 1)))
                            ->implode('') ?: 'U';

                        $notificacoesTopbar = collect($notificacoesTopbar ?? []);
                        $notificacoesTopbarCount = $notificacoesTopbarCount ?? $notificacoesTopbar->count();

                        $atalhosTopbar = collect([
                            ['titulo' => 'Financeiro', 'descricao' => 'Caixa, titulos e movimentacoes.', 'rota' => route('admin.financeiro.index'), 'capacidade' => 'financeiro'],
                            ['titulo' => 'Produtos', 'descricao' => 'Catalogo e vitrine publica.', 'rota' => route('admin.produtos.index'), 'capacidade' => 'catalogo'],
                            ['titulo' => 'Precos', 'descricao' => 'Comparador e ofertas.', 'rota' => route('admin.precos.index'), 'capacidade' => 'precos'],
                            ['titulo' => 'Lojas', 'descricao' => 'Operacao e presenca local.', 'rota' => route('admin.lojas.index'), 'capacidade' => 'lojas'],
                        ])->filter(fn ($atalho) => in_array($atalho['capacidade'], $capacidadesConta, true))->take(3);

                        $comandosTopbar = collect([
                            ['titulo' => 'Dashboard', 'descricao' => 'Voltar para a visao geral da conta.', 'rota' => route('admin.dashboard'), 'icone' => 'home', 'tags' => 'inicio painel indicadores'],
                            ['titulo' => 'Centro de lancamento', 'descricao' => 'Ver prontidao, pendencias e proximas acoes.', 'rota' => route('admin.lancamento'), 'icone' => 'spark', 'tags' => 'lancamento prontidao go to market'],
                            ['titulo' => 'Central de acoes', 'descricao' => 'Abrir notificacoes e alertas operacionais.', 'rota' => route('admin.notificacoes'), 'icone' => 'bell', 'tags' => 'notificacoes alertas tarefas'],
                            ['titulo' => 'Onboarding', 'descricao' => 'Acompanhar implantacao e setup da conta.', 'rota' => route('admin.onboarding'), 'icone' => 'compass', 'capacidade' => 'onboarding', 'tags' => 'setup implantacao comecar'],
                            ['titulo' => 'Financeiro', 'descricao' => 'Caixa, titulos, contas e movimentacoes.', 'rota' => route('admin.financeiro.index'), 'icone' => 'wallet', 'capacidade' => 'financeiro', 'tags' => 'caixa dinheiro contas pagar receber'],
                            ['titulo' => 'Produtos', 'descricao' => 'Gerenciar catalogo, imagens e marcas.', 'rota' => route('admin.produtos.index'), 'icone' => 'package', 'capacidade' => 'catalogo', 'tags' => 'catalogo produto imagem marca'],
                            ['titulo' => 'Precos', 'descricao' => 'Publicar e revisar ofertas no comparador.', 'rota' => route('admin.precos.index'), 'icone' => 'tag', 'capacidade' => 'precos', 'tags' => 'ofertas comparador valores'],
                            ['titulo' => 'Lojas', 'descricao' => 'Organizar unidades, canais e presenca local.', 'rota' => route('admin.lojas.index'), 'icone' => 'store', 'capacidade' => 'lojas', 'tags' => 'unidade local endereco loja'],
                            ['titulo' => 'Equipe', 'descricao' => 'Gerenciar acessos, papeis e usuarios.', 'rota' => route('admin.equipe.index'), 'icone' => 'users', 'capacidade' => 'equipe', 'tags' => 'usuarios time acesso papel'],
                            ['titulo' => 'Configuracoes', 'descricao' => 'Atualizar dados comerciais da conta.', 'rota' => route('admin.configuracoes.edit'), 'icone' => 'settings', 'capacidade' => 'gestao', 'tags' => 'empresa conta dados perfil'],
                            ['titulo' => 'Home publica', 'descricao' => 'Ver a experiencia de descoberta do cliente.', 'rota' => url('/'), 'icone' => 'search', 'tags' => 'publico vitrine ofertas cliente'],
                        ])->filter(fn ($comando) => empty($comando['capacidade']) || in_array($comando['capacidade'], $capacidadesConta, true))->values();
                    @endphp

                    <header class="topbar">
                        <div class="topbar-left">
                            <a class="topbar-compact-brand" href="{{ route('admin.dashboard') }}">
                                <span class="brand-mark">
                                    <img src="{{ asset('images/brand/mania-de-preco-mark.svg') }}" alt="">
                                </span>
                                <span>Mania de Preco</span>
                            </a>

                            <details class="topbar-menu topbar-search-menu" data-command-palette>
                                <summary class="topbar-search" aria-label="Buscar ou abrir atalhos rapidos">
                                    <x-ui.icon name="search" />
                                    <strong>Buscar acoes e atalhos</strong>
                                    <kbd>Ctrl K</kbd>
                                    <x-ui.icon name="chevron-down" class="dropdown-caret" />
                                </summary>
                                <div class="dropdown-panel command-panel">
                                    <div class="command-head">
                                        <h3>Central rapida</h3>
                                        <span>Ctrl K</span>
                                    </div>
                                    <label class="command-input">
                                        <x-ui.icon name="search" />
                                        <input data-command-input type="search" placeholder="Digite financeiro, produtos, lojas..." autocomplete="off">
                                    </label>
                                    <div class="command-list" data-command-list>
                                        @foreach ($comandosTopbar as $comando)
                                            <a class="command-item" href="{{ $comando['rota'] }}" data-command="{{ \Illuminate\Support\Str::lower($comando['titulo'] . ' ' . $comando['descricao'] . ' ' . ($comando['tags'] ?? '')) }}">
                                                <span class="metric-icon" style="width:42px;height:42px;border-radius:14px;">
                                                    <x-ui.icon :name="$comando['icone']" />
                                                </span>
                                                <span>
                                                    <strong>{{ $comando['titulo'] }}</strong>
                                                    <span>{{ $comando['descricao'] }}</span>
                                                </span>
                                                <small>Abrir</small>
                                            </a>
                                        @endforeach
                                        <div class="command-empty" data-command-empty>Nenhum atalho encontrado.</div>
                                    </div>
                                </div>
                            </details>
                        </div>

                        <div class="topbar-actions">
                            <div class="topbar-tools">
                                <details class="topbar-menu">
                                    <summary class="icon-button has-caret" aria-label="Abrir notificacoes">
                                        <x-ui.icon name="bell" />
                                        <x-ui.icon name="chevron-down" class="dropdown-caret" />
                                        <span class="notification-dot">{{ $notificacoesTopbarCount }}</span>
                                    </summary>
                                    <div class="dropdown-panel">
                                        <h3>Notificacoes</h3>
                                        <div class="dropdown-list">
                                            @foreach ($notificacoesTopbar as $notificacao)
                                                @if ($notificacao['rota'])
                                                    <a class="notification-item" href="{{ $notificacao['rota'] }}">
                                                        <span class="metric-icon {{ $notificacao['tipo'] === 'risco' ? 'is-danger' : ($notificacao['tipo'] === 'alerta' ? 'is-warning' : ($notificacao['tipo'] === 'sucesso' ? 'is-teal' : '')) }}" style="width:34px;height:34px;border-radius:12px;">
                                                            <x-ui.icon :name="$notificacao['icone']" />
                                                        </span>
                                                        <span>
                                                            <strong>{{ $notificacao['titulo'] }}</strong>
                                                            <span>{{ $notificacao['descricao'] }}</span>
                                                        </span>
                                                    </a>
                                                @else
                                                    <div class="notification-item">
                                                        <span class="metric-icon {{ $notificacao['tipo'] === 'risco' ? 'is-danger' : ($notificacao['tipo'] === 'alerta' ? 'is-warning' : ($notificacao['tipo'] === 'sucesso' ? 'is-teal' : '')) }}" style="width:34px;height:34px;border-radius:12px;">
                                                            <x-ui.icon :name="$notificacao['icone']" />
                                                        </span>
                                                        <span>
                                                            <strong>{{ $notificacao['titulo'] }}</strong>
                                                            <span>{{ $notificacao['descricao'] }}</span>
                                                        </span>
                                                    </div>
                                                @endif
                                            @endforeach
                                            <a class="quick-link" href="{{ route('admin.notificacoes') }}">
                                                <strong>Ver central de acoes</strong>
                                                <span>Abra a lista completa com prioridades e proximos passos.</span>
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
                                            <small>{{ $conta->nome_fantasia }}</small>
                                        </span>
                                        <x-ui.icon name="chevron-down" class="dropdown-caret" />
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
                                        <div class="profile-account-card">
                                            <small>Conta ativa</small>
                                            <strong>{{ $conta->nome_fantasia }}</strong>
                                            <p>Operacao, catalogo, financeiro e precos em uma unica cabine de comando.</p>
                                            <div>
                                                <span class="account-chip">{{ $assinaturaAtual?->status ?? 'sem assinatura' }}</span>
                                                @if (! empty($papelAtualConta))
                                                    <span class="account-chip">{{ $papelAtualConta }}</span>
                                                @endif
                                            </div>
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

                    <section class="page-heading">
                        <div class="page-heading-copy">
                            <span class="page-heading-kicker">Painel lojista</span>
                            <h1>@yield('heading')</h1>
                            @hasSection('subheading')
                                <p>@yield('subheading')</p>
                            @endif
                        </div>

                        <div class="page-heading-actions">
                            <span class="pill">{{ $conta->nome_fantasia }}</span>
                            <span class="badge {{ in_array($assinaturaAtual?->status, ['ativa', 'trial'], true) ? '' : 'is-warning' }}">
                                {{ $assinaturaAtual?->status ?? 'sem assinatura' }}
                            </span>
                        </div>
                    </section>

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
                <x-ui.icon name="home" />
                <strong>Inicio</strong>
                <span>painel</span>
            </a>
            <a class="mobile-dock-link {{ request()->routeIs('admin.lancamento') ? 'is-active' : '' }}" href="{{ route('admin.lancamento') }}">
                <x-ui.icon name="spark" />
                <strong>Lancar</strong>
                <span>pronto</span>
            </a>
            <a class="mobile-dock-link {{ request()->routeIs('admin.perfil.*') ? 'is-active' : '' }}" href="{{ route('admin.perfil.edit') }}">
                <x-ui.icon name="user" />
                <strong>Perfil</strong>
                <span>acesso</span>
            </a>
            @if (in_array('financeiro', $capacidadesConta, true))
                <a class="mobile-dock-link {{ request()->routeIs('admin.financeiro.*') ? 'is-active' : '' }}" href="{{ route('admin.financeiro.index') }}">
                    <x-ui.icon name="wallet" />
                    <strong>Financeiro</strong>
                    <span>caixa</span>
                </a>
            @endif
            @if (in_array('catalogo', $capacidadesConta, true))
                <a class="mobile-dock-link {{ request()->routeIs('admin.produtos.*') ? 'is-active' : '' }}" href="{{ route('admin.produtos.index') }}">
                    <x-ui.icon name="package" />
                    <strong>Produtos</strong>
                    <span>catalogo</span>
                </a>
            @endif
            @if (in_array('lojas', $capacidadesConta, true))
                <a class="mobile-dock-link {{ request()->routeIs('admin.lojas.*') ? 'is-active' : '' }}" href="{{ route('admin.lojas.index') }}">
                    <x-ui.icon name="store" />
                    <strong>Lojas</strong>
                    <span>operacao</span>
                </a>
            @endif
            @if (in_array('equipe', $capacidadesConta, true))
                <a class="mobile-dock-link {{ request()->routeIs('admin.equipe.*') ? 'is-active' : '' }}" href="{{ route('admin.equipe.index') }}">
                    <x-ui.icon name="users" />
                    <strong>Equipe</strong>
                    <span>acessos</span>
                </a>
            @elseif (in_array('gestao', $capacidadesConta, true))
                <a class="mobile-dock-link {{ request()->routeIs('admin.configuracoes.*') ? 'is-active' : '' }}" href="{{ route('admin.configuracoes.edit') }}">
                    <x-ui.icon name="settings" />
                    <strong>Conta</strong>
                    <span>empresa</span>
                </a>
            @elseif (in_array('onboarding', $capacidadesConta, true))
                <a class="mobile-dock-link {{ request()->routeIs('admin.onboarding') ? 'is-active' : '' }}" href="{{ route('admin.onboarding') }}">
                    <x-ui.icon name="compass" />
                    <strong>Setup</strong>
                    <span>onboarding</span>
                </a>
            @endif
        </nav>

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
                const triggers = Array.from(document.querySelectorAll('[data-sidebar-module-trigger]'));
                const panels = Array.from(document.querySelectorAll('[data-sidebar-module-panel]'));

                if (!triggers.length || !panels.length) {
                    return;
                }

                const activateModule = (moduleId) => {
                    triggers.forEach((trigger) => {
                        const isActive = trigger.dataset.sidebarModuleTrigger === moduleId;
                        trigger.classList.toggle('is-active', isActive);
                        trigger.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                    });

                    panels.forEach((panel) => {
                        panel.classList.toggle('is-active', panel.dataset.sidebarModulePanel === moduleId);
                    });
                };

                triggers.forEach((trigger) => {
                    trigger.addEventListener('click', () => activateModule(trigger.dataset.sidebarModuleTrigger));
                });
            })();

            (() => {
                const palette = document.querySelector('[data-command-palette]');

                if (!palette) {
                    return;
                }

                const input = palette.querySelector('[data-command-input]');
                const items = Array.from(palette.querySelectorAll('[data-command]'));
                const empty = palette.querySelector('[data-command-empty]');

                const filterItems = () => {
                    const query = (input?.value || '').trim().toLowerCase();
                    let visible = 0;

                    items.forEach((item) => {
                        const matches = !query || item.dataset.command.includes(query);
                        item.style.display = matches ? '' : 'none';
                        visible += matches ? 1 : 0;
                    });

                    empty?.classList.toggle('is-visible', visible === 0);
                };

                input?.addEventListener('input', filterItems);

                palette.addEventListener('toggle', () => {
                    if (!palette.open) {
                        if (input) {
                            input.value = '';
                        }
                        filterItems();
                        return;
                    }

                    window.setTimeout(() => input?.focus(), 60);
                });

                document.addEventListener('keydown', (event) => {
                    const isShortcut = (event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k';

                    if (!isShortcut) {
                        return;
                    }

                    event.preventDefault();
                    palette.open = true;
                    window.setTimeout(() => input?.focus(), 60);
                });
            })();
        </script>
    </body>
</html>
