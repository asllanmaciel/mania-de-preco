# Roadmap de lancamento

Atualizado em 22/04/2026.

## Decisao atual
- O Mania de Preco sera lancado como SaaS web-first.
- O cliente pagante principal e o lojista.
- O consumidor final usa a vitrine, ofertas, alertas e futuramente o app.
- O provedor de pagamento definido para o MVP passa a ser Mercado Pago.
- A integracao Asaas existente fica como referencia tecnica/legado, nao como caminho principal do lancamento.
- O app mobile deve nascer depois que o MVP web estiver validado, com Flutter como recomendacao principal.

## Onde estamos agora
- Sistema local carregando apos subir Docker com `docker compose up -d`.
- Home publica respondendo `200`.
- Login respondendo `200`.
- Todas as migrations aplicadas.
- Suite completa passando com 85 testes e 563 assertions.
- Base demo atual com 9 usuarios, 3 contas, 7 lojas, 8 produtos, 23 precos, 3 planos, 3 assinaturas e 3 chamados.
- Painel super admin possui roadmap, analytics e pre-flight.
- Pre-flight agora deve considerar Mercado Pago como caminho principal de cobranca.

## Leitura honesta de lancamento
O produto esta em MVP avancado, mas ainda nao esta pronto para producao aberta. Ele ja serve para demonstracao, validacao assistida e preparacao de contas piloto. Para lancar como SaaS de verdade, precisamos fechar quatro blocos: producao, Mercado Pago, QA por perfil e polimento visual/comercial.

## Bloqueios criticos
- Configurar ambiente final com `APP_ENV=production`.
- Configurar `APP_DEBUG=false`.
- Configurar `APP_URL` com dominio real e HTTPS.
- Implementar gateway Mercado Pago.
- Criar webhook Mercado Pago.
- Configurar `MERCADO_PAGO_ACCESS_TOKEN`.
- Configurar `MERCADO_PAGO_PUBLIC_KEY`.
- Configurar `MERCADO_PAGO_WEBHOOK_SECRET`.
- Validar pagamento/assinatura Mercado Pago em sandbox.

## O que ja existe
- Plataforma SaaS multi-conta.
- Separacao de paineis: super admin, lojista e cliente.
- Painel lojista com dashboard, onboarding, financeiro, lojas, produtos, precos, equipe, assinatura e auditoria.
- Super admin com contas, planos, assinaturas, suporte, analytics, roadmap e pre-flight.
- Vitrine publica com home, ofertas, radar, produto, loja, novidades, suporte, termos, privacidade, sitemap e robots.
- Area do cliente com alertas de preco.
- API mobile v1 para consumidores.
- Seeds demo para apresentacao.
- Integracao Asaas inicial para cliente, assinatura, checkout e webhook.
- Configuracao Mercado Pago mapeada em `config/billing.php`.
- Health check operacional.
- Rate limit em rotas sensiveis.
- Consentimento legal nos formularios.
- Changelogs e documentacao evolutiva.

## O que falta para lancar
- Gateway Mercado Pago operacional.
- Webhook Mercado Pago com auditoria de eventos.
- Tela/fluxo de assinatura ajustado para Mercado Pago.
- Credenciais Mercado Pago sandbox configuradas.
- Teste ponta a ponta de assinatura, checkout, pagamento aprovado, pagamento recusado, atraso e cancelamento.
- Ambiente de producao com dominio, SSL, fila, scheduler, backup e monitoramento.
- QA completo com visitante, cliente, lojista owner, gestor, financeiro e super admin.
- Revisao visual da landing e dos paineis para parecer SaaS premium.
- Copy comercial final sem linguagem interna de projeto.
- Eventos de analytics reais ou demo para validar funil.
- Ritual operacional de suporte, cobranca e acompanhamento de contas piloto.

## Roadmap por fase

### Fase 1: Base SaaS web
Status: avancado.

Concluido:
- Multi-conta.
- Usuarios por conta.
- Papeis e permissoes por modulo.
- Planos e assinaturas.
- Painel lojista.
- Painel super admin.
- Area do cliente.
- Auditoria e notificacoes.

Falta:
- Refinar a experiencia visual geral.
- Fechar QA por papel.
- Ajustar narrativa comercial para SaaS de mercado.

### Fase 2: Produto publico e demanda
Status: funcional, precisa polimento.

Concluido:
- Home publica.
- Ofertas.
- Radar de precos.
- Pagina de produto.
- Pagina de loja.
- Novidades/changelogs publicos.
- Suporte publico.
- Termos, privacidade, robots e sitemap.

Falta:
- Landing mais forte e comercial.
- Cards e tipografia mais consistentes.
- Prova social e narrativa de valor para lojistas.
- Melhor leitura mobile-first.
- Dados de analytics para funil real.

### Fase 3: Mercado Pago
Status: decisao tomada, implementacao pendente.

Concluido:
- Mercado Pago definido como provedor principal do MVP.
- Variaveis de configuracao mapeadas: `MERCADO_PAGO_ACCESS_TOKEN`, `MERCADO_PAGO_PUBLIC_KEY`, `MERCADO_PAGO_WEBHOOK_SECRET`.
- Estrutura de billing existente pode servir de referencia pela integracao Asaas.

Falta:
- Criar `MercadoPagoClient`.
- Criar `MercadoPagoBillingGateway`.
- Registrar Mercado Pago no `BillingManager`.
- Criar controller de webhook Mercado Pago.
- Criar processor de webhook Mercado Pago.
- Persistir eventos em `billing_webhook_events`.
- Atualizar formulario de assinatura para usar Mercado Pago quando habilitado.
- Criar testes de sincronizacao e webhook Mercado Pago.
- Atualizar pre-flight para ficar verde quando o gateway existir.

### Fase 4: Producao segura
Status: bloqueada por ambiente final.

Falta:
- Servidor/hospedagem definida.
- Dominio final.
- HTTPS.
- Variaveis production.
- Banco de producao.
- Storage persistente.
- Backup.
- Worker de fila.
- Scheduler.
- Monitoramento de uptime.
- Logs e alertas.

### Fase 5: QA de lancamento
Status: parcialmente coberto por testes automatizados.

Concluido:
- 85 testes automatizados passando.
- Cobertura para catalogo publico, admin, cliente, mobile API, webhook Asaas, pre-flight e health.

Falta:
- Checklist manual por perfil.
- QA de responsividade real em celular.
- QA dos fluxos financeiros principais.
- QA de assinatura com Mercado Pago.
- QA de suporte publico.
- QA de recuperacao de senha/e-mail real.

### Fase 6: App mobile
Status: API pronta, app ainda nao iniciado.

Concluido:
- `GET /api/mobile/v1/ofertas`
- `GET /api/mobile/v1/produtos/{produto}`
- `GET /api/mobile/v1/lojas/{loja}`
- `POST /api/mobile/v1/register`
- `POST /api/mobile/v1/login`
- `GET /api/mobile/v1/me`
- `POST /api/mobile/v1/logout`
- CRUD de alertas em `/api/mobile/v1/alertas`

Falta:
- Criar projeto Flutter.
- Criar design system mobile.
- Implementar login/cadastro.
- Implementar home/ofertas/filtros.
- Implementar produto/loja.
- Implementar alertas.
- Implementar perfil.
- Preparar build Android/iOS.
- Publicacao nas lojas depois do MVP web validado.

## App: estrategia recomendada
Flutter continua sendo a melhor opcao para este momento. Ele reduz custo, entrega Android e iOS com uma base unica e conversa bem com a API mobile v1 ja criada.

O app nao deve bloquear o lancamento web. O papel dele e ser uma frente de crescimento para consumidores depois que o SaaS web estiver gerando valor para lojistas.

## Ordem recomendada agora
1. Implementar Mercado Pago como gateway principal.
2. Validar Mercado Pago em sandbox ponta a ponta.
3. Ajustar formulario e comunicacao de assinaturas para Mercado Pago.
4. Resolver ambiente de producao e variaveis criticas.
5. Rodar QA completo por perfil.
6. Revisar visual/copy da landing e paineis principais.
7. Preparar contas piloto e dados reais.
8. Ligar monitoramento, backup e rotina operacional.
9. Lancar producao controlada.
10. Iniciar projeto Flutter do app cliente.

## Criterio para dizer que pode lancar
- Pre-flight sem bloqueios criticos.
- Mercado Pago validado em sandbox.
- Dominio real com HTTPS.
- E-mail transacional funcionando.
- Backup, fila, scheduler e monitoramento configurados.
- Jornada do lojista validada sem intervencao tecnica.
- Jornada do cliente validada em desktop e celular.
- Suporte operacional com chamados criticos zerados ou justificados.
- Landing com cara de SaaS profissional.
- QA final concluido com todos os perfis.

## Comandos uteis
Subir ambiente:
```bash
docker compose up -d
```

Rodar pre-flight:
```bash
docker compose exec laravel.test php artisan launch:check
```

Rodar testes:
```bash
docker compose exec laravel.test php artisan test
```

Recriar base demo:
```bash
docker compose exec laravel.test php artisan demo:refresh --force
```
