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
                --bg:#f4ecdf;
                --surface:rgba(255,250,242,.88);
                --line:rgba(78,43,25,.12);
                --text:#24140e;
                --muted:#715349;
                --accent:#ff6b2c;
                --accent-2:#0f9f8f;
                --shadow:0 24px 60px rgba(57,29,16,.12);
                --radius-xl:30px;
                --radius-lg:22px;
                --radius-md:16px;
            }
            * { box-sizing:border-box; }
            body {
                margin:0;
                min-height:100vh;
                font-family:"Space Grotesk", sans-serif;
                color:var(--text);
                background:
                    radial-gradient(circle at top left, rgba(255,107,44,.18), transparent 30%),
                    radial-gradient(circle at top right, rgba(15,159,143,.16), transparent 24%),
                    linear-gradient(180deg, #fff7ea 0%, #f4ecdf 48%, #efe3d0 100%);
            }
            a { color:inherit; text-decoration:none; }
            .shell { max-width:1280px; margin:0 auto; padding:28px 18px 48px; display:grid; gap:18px; }
            .topbar, .section-head, .toolbar { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; }
            .brand { display:inline-flex; align-items:center; gap:12px; font-weight:700; }
            .brand-badge {
                display:inline-grid; place-items:center; width:42px; height:42px; border-radius:14px;
                background:linear-gradient(135deg,var(--accent),#ff9f52); color:#27140e; font-family:"IBM Plex Mono", monospace;
            }
            .nav { display:flex; gap:10px; flex-wrap:wrap; }
            .chip, .button, .button-secondary, .logout-button {
                display:inline-flex; align-items:center; justify-content:center; padding:12px 16px; border-radius:14px;
                border:1px solid var(--line); background:rgba(255,255,255,.72); color:var(--text); font-weight:600; font-family:inherit;
            }
            .button { background:linear-gradient(135deg,var(--accent),#ff9f52); border-color:transparent; color:#2d150d; box-shadow:0 18px 36px rgba(255,107,44,.18); }
            .button-secondary { background:rgba(255,255,255,.72); }
            .logout-button { background:#2a160f; border-color:#2a160f; color:#fff5ef; cursor:pointer; }
            .card { background:var(--surface); border:1px solid rgba(255,255,255,.7); border-radius:var(--radius-lg); box-shadow:var(--shadow); backdrop-filter:blur(14px); }
            .card-body { padding:24px; }
            .hero { padding:28px; border-radius:var(--radius-xl); }
            .hero h1 { margin:0; font-size:clamp(2rem,4vw,3rem); line-height:1; }
            .hero p { margin:10px 0 0; color:var(--muted); line-height:1.7; max-width:800px; }
            .grid-4, .grid-3, .grid-2, .list { display:grid; gap:18px; }
            .grid-4 { grid-template-columns:repeat(4, minmax(0, 1fr)); }
            .grid-3 { grid-template-columns:repeat(3, minmax(0, 1fr)); }
            .grid-2 { grid-template-columns:repeat(2, minmax(0, 1fr)); }
            .metric, .list-row, .mini-card { padding:18px; border-radius:18px; background:rgba(255,255,255,.72); border:1px solid rgba(76,42,22,.08); }
            .metric strong, .mini-card strong { display:block; margin-bottom:8px; font-size:1.8rem; }
            .metric span, .mini-card span, .list-row small { color:var(--muted); line-height:1.6; }
            .list-row strong { display:block; margin-bottom:6px; }
            .flash-box { padding:16px 18px; border-radius:18px; background:rgba(15,159,143,.08); border:1px solid rgba(15,159,143,.12); color:#0a7167; }
            @media (max-width:980px) {
                .topbar, .section-head, .toolbar, .nav { flex-direction:column; align-items:stretch; }
                .grid-4, .grid-3, .grid-2 { grid-template-columns:1fr; }
                .button, .button-secondary, .chip, .logout-button { width:100%; }
            }
        </style>
    </head>
    <body>
        <div class="shell">
            <header class="topbar">
                <a class="brand" href="@yield('brand_route', route('painel.redirect'))">
                    <span class="brand-badge">MP</span>
                    <span>@yield('brand_label', 'Mania de Preco')</span>
                </a>

                <div class="nav">
                    @yield('nav')
                    <a class="chip" href="{{ url('/') }}">Home publica</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="logout-button" type="submit">Sair</button>
                    </form>
                </div>
            </header>

            @if (session('status'))
                <div class="flash-box">{{ session('status') }}</div>
            @endif

            @yield('content')
        </div>
    </body>
</html>
