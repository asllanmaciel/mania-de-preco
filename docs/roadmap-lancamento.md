# Roadmap de lançamento

## Decisão atual
- O lançamento do MVP será web-first, com painel lojista, super admin, área do cliente e vitrine pública.
- O app mobile será iniciado depois que o MVP web estiver vendável, com foco inicial no consumidor.
- A cobrança recorrente terá Asaas como provedor principal do MVP.
- Mercado Pago fica como alternativa planejada, mas não deve bloquear o lançamento inicial.

## Por que Asaas primeiro
- O código já possui integração inicial com Asaas para cliente, assinatura, checkout e webhook.
- O fluxo B2B recorrente do SaaS combina bem com assinatura, boleto, Pix e cobrança recorrente.
- Implementar dois gateways antes do lançamento aumenta risco operacional e QA sem aumentar o valor imediato do MVP.

## Papel do Mercado Pago
- Deve permanecer no roadmap como opção futura para checkout, Pix e canais mais voltados a varejo/consumidor.
- A entrada do Mercado Pago deve acontecer depois que o fluxo Asaas estiver validado em produção.
- Antes de implementar, será necessário definir se ele será usado para assinatura do lojista, pagamento avulso, marketplace ou compra direta.

## Fase 1: MVP web vendável
Status: em andamento.

Objetivo:
- Permitir apresentar, vender e operar o Mania de Preço para contas piloto com confiança.

Entregas concluídas:
- Painel lojista com dashboard, onboarding, financeiro, lojas, produtos, preços, equipe, assinatura e auditoria.
- Super admin com contas, planos, assinaturas, suporte em cards, analytics e prontidão global de lançamento.
- Área do cliente com alertas de preço.
- Vitrine pública com ofertas, lojas, produtos, novidades, suporte, termos, privacidade, sitemap e robots.
- API mobile v1 para consumidores.
- Seeds demo para navegação sem cadastro manual.

Pendências antes de lançamento controlado:
- Refinar visual geral e tipografia das telas principais.
- Fechar jornada comercial de onboarding do lojista.
- Configurar Asaas em sandbox com credenciais reais de teste.
- Validar webhook Asaas ponta a ponta.
- Configurar e-mail transacional real.
- Configurar produção com domínio, SSL, fila, scheduler, backup e monitoramento.
- Rodar QA completo com perfis super admin, lojista, cliente e visitante.

## Fase 2: Cobrança real com Asaas
Status: próximo bloco estrutural.

Objetivo:
- Permitir ativar plano, gerar cobrança recorrente e refletir inadimplência/recebimento no sistema.

Entregas já existentes:
- Configuração `billing.default_provider`.
- Gateway Asaas para sincronizar conta e assinatura.
- Webhook Asaas com token.
- Auditoria de eventos de webhook.
- Link de cobrança visível para o lojista quando existe checkout.

Próximas ações:
- Preencher `ASAAS_API_KEY`, `ASAAS_WEBHOOK_TOKEN` e `ASAAS_SUBSCRIPTION_BILLING_TYPE`.
- Criar uma assinatura real de teste no super admin e sincronizar com Asaas sandbox.
- Validar criação de cliente no Asaas.
- Validar criação de assinatura no Asaas.
- Validar geração de link de cobrança.
- Simular pagamento confirmado e atraso via webhook.
- Exibir estados de cobrança com mais clareza para lojista e super admin.

## Fase 3: Polimento visual e conversão
Status: em andamento contínuo.

Objetivo:
- Aumentar percepção de qualidade, confiança e valor comercial.

Próximas ações:
- Revisar tipografia geral.
- Padronizar cards, métricas, tabelas, empty states e botões.
- Melhorar landing pública com copy mais forte e visual menos pesado.
- Melhorar páginas de loja e produto.
- Revisar responsividade mobile-first.
- Garantir que textos públicos não usem linguagem de documentação interna.

## Fase 4: App mobile cliente
Status: base de API criada, app ainda não iniciado.

Objetivo:
- Criar app Android/iOS para consumidor acompanhar ofertas e alertas de preço.

Base pronta:
- `GET /api/mobile/v1/ofertas`
- `GET /api/mobile/v1/produtos/{produto}`
- `GET /api/mobile/v1/lojas/{loja}`
- `POST /api/mobile/v1/register`
- `POST /api/mobile/v1/login`
- `GET /api/mobile/v1/me`
- `POST /api/mobile/v1/logout`
- CRUD de `/api/mobile/v1/alertas`

Escopo sugerido para o primeiro app:
- Login e cadastro.
- Busca e filtros de ofertas.
- Detalhe de produto com comparação de preços.
- Detalhe de loja.
- Alertas de preço.
- Perfil simples.

Tecnologia recomendada:
- Flutter para lançar Android e iOS com uma base única.

## Fase 5: Expansão pós-MVP
Status: planejado.

Possíveis frentes:
- Mercado Pago como segundo provedor de pagamento.
- Notificações push no app.
- Importação de produtos e preços por planilha.
- Relatórios avançados para lojistas.
- Automação de alertas por e-mail, WhatsApp ou push.
- Painel comercial para acompanhar funil de contas e upgrades.

## Critério para lançamento controlado
- Nenhum bloqueio crítico no centro de prontidão global.
- Asaas sandbox validado ponta a ponta.
- E-mail transacional funcionando.
- Produção com domínio, SSL, fila, backup e monitoramento.
- Jornada do lojista testada sem intervenção técnica.
- Vitrine pública com dados suficientes para demonstrar valor.
- Suporte operacional com fila revisada.
- QA final concluído em desktop e mobile.

## Ferramenta de pré-flight
- O super admin possui o painel `super-admin/roadmap` com o checklist operacional de produção.
- O mesmo diagnóstico pode ser executado no terminal com `php artisan launch:check`.
- O comando retorna falha quando existem bloqueios críticos, permitindo uso em QA manual, deploy controlado ou esteira de validação.
- Os grupos avaliados são ambiente, infraestrutura, cobrança, produto e operação.
