@extends('layouts.institucional')

@section('title', 'Suporte')
@section('description', 'Canais de suporte do Mania de Preço para lojistas, usuários e consumidores.')

@section('content')
    <section class="hero">
        <article class="hero-card">
            <span class="eyebrow">suporte e atendimento</span>
            <h1>Quando algo trava, o caminho de ajuda precisa ser simples.</h1>
            <p>Use esta página para entender como pedir ajuda, que informações enviar e quais situações podem ser priorizadas pela operação do Mania de Preço.</p>

            <div class="buttons">
                <a class="button" href="mailto:{{ $emailSuporte }}">Enviar e-mail</a>
                <a class="button-secondary" href="{{ route('home') }}">Voltar para ofertas</a>
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
                <span class="small">Esta página pode evoluir para central de ajuda, base de conhecimento e status page.</span>
            </div>
        </aside>
    </section>
@endsection
