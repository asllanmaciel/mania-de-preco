<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Recuperar senha | Mania de Preco</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|ibm-plex-mono:400,500" rel="stylesheet" />
        <style>
            body { margin:0; min-height:100vh; font-family:"Space Grotesk", sans-serif; color:#21140f; background:linear-gradient(180deg,#fff6e8 0%,#f5efe3 100%); }
            .shell { max-width:860px; margin:0 auto; padding:28px; }
            .brand { display:inline-flex; align-items:center; gap:12px; font-weight:700; text-decoration:none; color:inherit; margin-bottom:28px; }
            .brand-badge { display:inline-grid; place-items:center; width:42px; height:42px; border-radius:14px; background:linear-gradient(135deg,#ff6b2c,#ff9f52); font-family:"IBM Plex Mono", monospace; }
            .card { padding:34px; border-radius:30px; background:rgba(255,251,245,.9); border:1px solid rgba(70,39,22,.1); box-shadow:0 24px 70px rgba(65,27,10,.12); }
            h1 { margin:0; font-size:clamp(2rem,4vw,3.4rem); line-height:1; }
            p, .helper { color:#70534b; line-height:1.75; }
            form { display:grid; gap:18px; margin-top:24px; }
            label { display:grid; gap:8px; font-weight:600; }
            input { width:100%; padding:15px 16px; border-radius:14px; border:1px solid rgba(76,42,22,.12); background:#fff; font:inherit; }
            .button, .link { display:inline-flex; justify-content:center; padding:15px 18px; border-radius:16px; border:0; font:inherit; font-weight:700; text-decoration:none; cursor:pointer; }
            .button { background:linear-gradient(135deg,#ff6b2c,#ff9f52); color:#2d150d; }
            .link { color:#21140f; background:rgba(255,255,255,.75); border:1px solid rgba(76,42,22,.12); }
            .actions { display:flex; gap:12px; flex-wrap:wrap; }
            .status, .errors { padding:14px 16px; border-radius:16px; margin-top:18px; line-height:1.6; }
            .status { background:rgba(15,159,143,.08); color:#0a7167; }
            .errors { background:rgba(182,72,51,.08); color:#8c3525; }
            @media (max-width:720px) { .shell { padding:18px; } .card { padding:24px; } .actions { flex-direction:column; } }
        </style>
    </head>
    <body>
        <main class="shell">
            <a class="brand" href="{{ url('/') }}">
                <span class="brand-badge">MP</span>
                <span>Mania de Preco</span>
            </a>

            <section class="card">
                <h1>Recupere seu acesso com seguranca.</h1>
                <p>Informe o e-mail usado na conta. Se ele existir na base, enviaremos um link para redefinir a senha.</p>

                @if (session('status'))
                    <div class="status">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="errors">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <label for="email">
                        <span>E-mail</span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                        <span class="helper">Use o e-mail vinculado ao seu usuario no painel.</span>
                    </label>

                    <div class="actions">
                        <button class="button" type="submit">Enviar link de recuperacao</button>
                        <a class="link" href="{{ route('login') }}">Voltar ao login</a>
                    </div>
                </form>
            </section>
        </main>
    </body>
</html>
