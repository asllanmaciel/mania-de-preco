@extends('layouts.institucional')

@section('title', 'Suporte')
@section('description', 'Central de suporte do Mania de Preço para lojistas, usuários e consumidores.')

@section('content')
    <section class="hero">
        <article class="hero-card">
            <span class="eyebrow">suporte e atendimento</span>
            <h1>Quando algo trava, o caminho de ajuda precisa ser simples.</h1>
            <p>Abra um chamado com contexto, acompanhe os pontos principais da operação e encontre respostas rápidas para continuar vendendo, comparando e decidindo sem perder tempo.</p>

            <div class="buttons">
                <a class="button" href="#abrir-chamado">Abrir chamado</a>
                <a class="button-secondary" href="mailto:{{ $emailSuporte }}">Enviar e-mail</a>
            </div>
        </article>

        <aside class="card stack">
            <span class="badge">Atendimento</span>
            <div class="mini">
                <strong>{{ $emailSuporte }}</strong>
                <span class="small">Canal principal para dúvidas, incidentes, dados, cobrança e acesso.</span>
            </div>
            <div class="mini">
                <strong>Inclua contexto</strong>
                <span class="small">Conta, e-mail de acesso, página, horário do erro e prints ajudam a resolver mais rápido.</span>
            </div>
            <div class="mini">
                <strong>Protocolo automático</strong>
                <span class="small">Chamados abertos por aqui entram em uma fila operacional para o time acompanhar prioridade e status.</span>
            </div>
        </aside>
    </section>

    @if (session('status'))
        <section class="section">
            <div class="flash-box">{{ session('status') }}</div>
        </section>
    @endif

    @if ($errors->any())
        <section class="section">
            <div class="error-box">Revise os campos destacados e tente enviar novamente. Quanto melhor o contexto, mais rápido o atendimento avança.</div>
        </section>
    @endif

    <section class="section">
        <div class="section-head">
            <div>
                <h2>Status operacional</h2>
                <p class="muted">Uma leitura rápida do que está funcionando e do que está em monitoramento.</p>
            </div>
        </div>

        <div class="grid">
            @foreach ($statusOperacional as $status)
                <article class="mini">
                    <strong>{{ $status['nome'] }}</strong>
                    <span class="badge" style="margin-bottom:10px;">{{ $status['status'] }}</span>
                    <span class="small">{{ $status['descricao'] }}</span>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section content-grid" id="abrir-chamado">
        <article class="card article-card">
            <h2>Abrir chamado</h2>
            <p>Use este canal para registrar incidentes, dúvidas de acesso, cobrança, dados, produtos ou operação. Ao enviar, o sistema gera um protocolo e coloca o pedido na fila de suporte.</p>

            <form method="POST" action="{{ route('suporte.chamados.store') }}" style="margin-top:18px;">
                @csrf

                <div class="field-grid">
                    <div class="field-group">
                        <label for="nome">Nome</label>
                        <input id="nome" name="nome" value="{{ old('nome', auth()->user()?->name) }}" required>
                        @error('nome') <span class="small">{{ $message }}</span> @enderror
                    </div>

                    <div class="field-group">
                        <label for="email">E-mail</label>
                        <input id="email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required>
                        @error('email') <span class="small">{{ $message }}</span> @enderror
                    </div>

                    <div class="field-group">
                        <label for="telefone">Telefone</label>
                        <input id="telefone" name="telefone" value="{{ old('telefone') }}" placeholder="Opcional">
                        @error('telefone') <span class="small">{{ $message }}</span> @enderror
                    </div>

                    <div class="field-group">
                        <label for="empresa">Empresa ou loja</label>
                        <input id="empresa" name="empresa" value="{{ old('empresa') }}" placeholder="Opcional">
                        @error('empresa') <span class="small">{{ $message }}</span> @enderror
                    </div>

                    <div class="field-group">
                        <label for="categoria">Categoria</label>
                        <select id="categoria" name="categoria" required>
                            @foreach ($categoriasSuporte as $valor => $rotulo)
                                <option value="{{ $valor }}" @selected(old('categoria') === $valor)>{{ $rotulo }}</option>
                            @endforeach
                        </select>
                        @error('categoria') <span class="small">{{ $message }}</span> @enderror
                    </div>

                    <div class="field-group">
                        <label for="prioridade">Prioridade percebida</label>
                        <select id="prioridade" name="prioridade" required>
                            @foreach ($prioridadesSuporte as $valor => $rotulo)
                                <option value="{{ $valor }}" @selected(old('prioridade', 'normal') === $valor)>{{ $rotulo }}</option>
                            @endforeach
                        </select>
                        @error('prioridade') <span class="small">{{ $message }}</span> @enderror
                    </div>

                    <div class="field-group-full">
                        <label for="assunto">Assunto</label>
                        <input id="assunto" name="assunto" value="{{ old('assunto') }}" placeholder="Ex.: Não consigo acessar o painel" required>
                        @error('assunto') <span class="small">{{ $message }}</span> @enderror
                    </div>

                    <div class="field-group-full">
                        <label for="origem_url">Link relacionado</label>
                        <input id="origem_url" type="url" name="origem_url" value="{{ old('origem_url', url()->previous() !== url()->current() ? url()->previous() : '') }}" placeholder="Opcional">
                        @error('origem_url') <span class="small">{{ $message }}</span> @enderror
                    </div>

                    <div class="field-group-full">
                        <label for="mensagem">O que aconteceu?</label>
                        <textarea id="mensagem" name="mensagem" required placeholder="Conte o que você tentou fazer, o que esperava ver e o que apareceu na tela.">{{ old('mensagem') }}</textarea>
                        @error('mensagem') <span class="small">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <span class="small">Não envie senhas, tokens ou dados sensíveis pelo chamado.</span>
                    <button class="button" type="submit">Enviar chamado</button>
                </div>
            </form>
        </article>

        <aside class="stack">
            <div class="mini">
                <strong>Atendimento mais rápido</strong>
                <span class="small">Informe página, horário, navegador, conta e prints quando possível.</span>
            </div>
            <div class="mini">
                <strong>Prioridade crítica</strong>
                <span class="small">Use apenas quando login, cobrança, publicação de preços ou operação essencial estiver parada.</span>
            </div>
            <div class="mini">
                <strong>Canal alternativo</strong>
                <span class="small">Se preferir, envie o mesmo contexto para {{ $emailSuporte }}.</span>
            </div>
        </aside>
    </section>

    <section class="section">
        <div class="section-head">
            <div>
                <h2>Como podemos ajudar</h2>
                <p class="muted">Organizar o pedido certo reduz idas e vindas e acelera a resposta.</p>
            </div>
        </div>

        <div class="grid">
            <article class="mini">
                <strong>Acesso e senha</strong>
                <span class="small">Use a recuperação de senha quando não conseguir entrar. Se ainda assim falhar, envie o e-mail da conta e o horário da tentativa.</span>
            </article>

            <article class="mini">
                <strong>Planos e cobrança</strong>
                <span class="small">Informe nome da conta, status exibido em assinatura e qualquer link ou mensagem de cobrança que apareceu.</span>
            </article>

            <article class="mini">
                <strong>Produtos e preços</strong>
                <span class="small">Envie produto, loja, preço esperado, preço exibido e a URL onde encontrou a diferença.</span>
            </article>

            <article class="mini">
                <strong>Financeiro</strong>
                <span class="small">Informe data, valor, conta financeira e descrição do lançamento ou título envolvido.</span>
            </article>

            <article class="mini">
                <strong>Dados e privacidade</strong>
                <span class="small">Solicitações sobre dados pessoais devem vir com identificação do titular e e-mail vinculado ao acesso.</span>
            </article>

            <article class="mini">
                <strong>Problemas técnicos</strong>
                <span class="small">Inclua navegador, dispositivo, mensagem de erro, rota acessada e se o problema acontece novamente após atualizar a página.</span>
            </article>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <div>
                <h2>Perguntas frequentes</h2>
                <p class="muted">Respostas curtas para os pontos que mais costumam travar a jornada.</p>
            </div>
        </div>

        <div class="grid">
            @foreach ($perguntasFrequentes as $faq)
                <article class="mini">
                    <strong>{{ $faq['pergunta'] }}</strong>
                    <span class="small">{{ $faq['resposta'] }}</span>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section content-grid">
        <article class="card article-card">
            <h2>Prioridade de atendimento</h2>
            <p>Incidentes que impedem login, cobrança, operação crítica da conta ou publicação de preços tendem a ter maior prioridade. Dúvidas de uso, ajustes de cadastro e solicitações comerciais podem seguir uma fila de atendimento normal.</p>

            <h3>Antes de abrir chamado</h3>
            <ul>
                <li>Verifique se está usando o e-mail correto.</li>
                <li>Tente redefinir a senha se o problema for acesso.</li>
                <li>Confira se sua conta está ativa e com assinatura regular.</li>
                <li>Inclua prints quando a tela exibir uma mensagem inesperada.</li>
            </ul>
        </article>

        <aside class="stack">
            <div class="mini">
                <strong>Privacidade</strong>
                <span class="small">Nunca envie senhas por e-mail ou mensagens de suporte.</span>
            </div>
            <div class="mini">
                <strong>Documentação viva</strong>
                <span class="small">Esta página evolui para central de ajuda, base de conhecimento e status page conforme a operação cresce.</span>
            </div>
        </aside>
    </section>
@endsection
