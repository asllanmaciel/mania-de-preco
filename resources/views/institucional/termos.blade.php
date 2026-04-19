@extends('layouts.institucional')

@section('title', 'Termos de Uso')
@section('description', 'Termos de uso do Mania de Preço para consumidores, lojistas e usuários da plataforma.')

@section('content')
    <section class="hero">
        <article class="hero-card">
            <span class="eyebrow">termos de uso</span>
            <h1>Regras claras para usar o Mania de Preço com confiança.</h1>
            <p>Estes termos explicam as condições gerais de uso da plataforma por consumidores, lojistas e usuários com acesso ao painel. O texto deve ser revisado juridicamente antes do lançamento oficial.</p>

            <div class="buttons">
                <a class="button" href="{{ route('home') }}">Voltar para ofertas</a>
                <a class="button-secondary" href="{{ route('privacidade') }}">Ver privacidade</a>
            </div>
        </article>

        <aside class="card stack">
            <span class="badge">Última atualização: {{ $ultimaAtualizacao }}</span>
            <div class="mini">
                <strong>Para consumidores</strong>
                <span class="small">Use as informações para comparar ofertas, conferir lojas e decidir melhor antes de comprar.</span>
            </div>
            <div class="mini">
                <strong>Para lojistas</strong>
                <span class="small">Publique dados corretos, mantenha preços atualizados e respeite as condições anunciadas.</span>
            </div>
        </aside>
    </section>

    <section class="section content-grid">
        <article class="card article-card">
            <h2>Condições gerais</h2>
            <p>Ao acessar ou utilizar o Mania de Preço, você concorda em usar a plataforma de forma lícita, transparente e compatível com a finalidade do serviço: facilitar comparação de preços, exposição de ofertas, operação financeira e gestão comercial.</p>

            <h3>Uso da plataforma</h3>
            <ul>
                <li>Consumidores podem consultar ofertas, lojas e produtos publicados na plataforma.</li>
                <li>Lojistas e usuários administrativos são responsáveis pelos dados cadastrados, incluindo preços, produtos, imagens, informações da loja e condições comerciais.</li>
                <li>O Mania de Preço pode alterar, evoluir ou remover funcionalidades para melhorar segurança, desempenho e experiência do produto.</li>
            </ul>

            <h3>Preços e ofertas</h3>
            <p>As ofertas exibidas dependem dos dados cadastrados e atualizados pelas lojas. Antes de concluir uma compra, recomendamos conferir disponibilidade, condição de pagamento, prazo, entrega e eventuais alterações diretamente com a loja responsável.</p>

            <h3>Contas, acesso e segurança</h3>
            <p>Usuários com acesso ao painel devem proteger suas credenciais, manter seus dados atualizados e comunicar qualquer suspeita de acesso indevido. A plataforma registra ações sensíveis para auditoria e governança da conta.</p>

            <h3>Planos, limites e cobrança</h3>
            <p>Contas de lojistas podem estar sujeitas a planos, limites operacionais e ciclos de cobrança. O uso acima dos limites contratados pode exigir upgrade, ajuste comercial ou intervenção do administrador da plataforma.</p>

            <h3>Disponibilidade</h3>
            <p>Trabalhamos para manter o serviço disponível, seguro e estável, mas podem ocorrer interrupções por manutenção, falhas externas, atualizações técnicas ou situações fora do controle da plataforma.</p>

            <h3>Contato</h3>
            <p>Para dúvidas sobre estes termos, fale com o suporte pelo e-mail <a href="mailto:{{ $emailSuporte }}">{{ $emailSuporte }}</a>.</p>
        </article>

        <aside class="stack">
            <div class="mini">
                <strong>Nota importante</strong>
                <span class="small">Este conteúdo é uma base operacional e precisa de validação jurídica antes do uso definitivo em produção.</span>
            </div>
            <div class="mini">
                <strong>Também leia</strong>
                <span class="small"><a href="{{ route('privacidade') }}">Política de Privacidade</a></span>
            </div>
        </aside>
    </section>
@endsection
