@extends('layouts.institucional')

@section('title', 'Política de Privacidade')
@section('description', 'Política de privacidade do Mania de Preço com informações sobre dados, segurança e direitos dos usuários.')

@section('content')
    <section class="hero">
        <article class="hero-card">
            <span class="eyebrow">privacidade e dados</span>
            <h1>Seus dados precisam ter uma finalidade clara.</h1>
            <p>Esta política resume como o Mania de Preço trata dados pessoais e operacionais para login, comparação de preços, gestão de contas, cobrança, suporte e melhoria da experiência.</p>

            <div class="buttons">
                <a class="button" href="{{ route('suporte') }}">Falar com suporte</a>
                <a class="button-secondary" href="{{ route('termos') }}">Ver termos</a>
            </div>
        </article>

        <aside class="card stack">
            <span class="badge">Última atualização: {{ $ultimaAtualizacao }}</span>
            <div class="mini">
                <strong>LGPD como base</strong>
                <span class="small">A plataforma deve operar com finalidade, necessidade, segurança e transparência no tratamento de dados.</span>
            </div>
            <div class="mini">
                <strong>Controle do usuário</strong>
                <span class="small">Usuários podem solicitar orientação sobre acesso, correção ou exclusão de dados quando aplicável.</span>
            </div>
        </aside>
    </section>

    <section class="section content-grid">
        <article class="card article-card">
            <h2>Como tratamos informações</h2>
            <p>Coletamos e utilizamos dados necessários para autenticar usuários, operar contas de lojistas, exibir ofertas, registrar auditoria, processar cobranças e prestar suporte.</p>

            <h3>Dados que podem ser tratados</h3>
            <ul>
                <li>Dados de identificação, como nome, e-mail e vínculo com contas.</li>
                <li>Dados de empresa, como nome fantasia, documento, contato, endereço, segmento e preferências.</li>
                <li>Dados operacionais, como lojas, produtos, preços, lançamentos financeiros e eventos de auditoria.</li>
                <li>Dados técnicos, como endereço IP, agente de navegação, sessões e eventos de segurança.</li>
                <li>Dados de cobrança, quando houver integração com provedores como Asaas ou outros gateways.</li>
            </ul>

            <h3>Finalidades</h3>
            <p>Os dados são usados para permitir acesso seguro, entregar funcionalidades do SaaS, mostrar ofertas públicas, melhorar a operação da conta, cumprir obrigações legais, combater fraude, prestar suporte e evoluir o produto.</p>

            <h3>Compartilhamento</h3>
            <p>Dados podem ser compartilhados com provedores essenciais para hospedagem, e-mail, pagamento, segurança e suporte, sempre de acordo com a necessidade do serviço e com controles proporcionais.</p>

            <h3>Segurança</h3>
            <p>O sistema utiliza autenticação, hashing de senhas, tokens de redefinição, registros de auditoria e controle de permissões por conta. Nenhuma medida elimina todos os riscos, mas a plataforma deve evoluir continuamente para reduzir exposição.</p>

            <h3>Direitos dos titulares</h3>
            <p>Você pode solicitar informações sobre seus dados, correções ou orientação sobre exclusão quando aplicável. Algumas informações podem precisar ser mantidas por obrigação legal, segurança, auditoria ou execução de contrato.</p>

            <h3>Contato de privacidade</h3>
            <p>Para solicitações relacionadas a dados pessoais, envie uma mensagem para <a href="mailto:{{ $emailSuporte }}">{{ $emailSuporte }}</a>.</p>
        </article>

        <aside class="stack">
            <div class="mini">
                <strong>Antes do lançamento</strong>
                <span class="small">Esta política deve passar por revisão jurídica para ajustar controlador, operador, bases legais e retenção.</span>
            </div>
            <div class="mini">
                <strong>Transparência contínua</strong>
                <span class="small">Mudanças relevantes na política devem ser comunicadas com clareza aos usuários.</span>
            </div>
        </aside>
    </section>
@endsection
