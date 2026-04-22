# Dossie do sistema Mania de Preco

Atualizado em 22/04/2026.

## Resumo executivo
O Mania de Preco e um SaaS web-first para lojistas controlarem operacao financeira, publicarem ofertas e ganharem visibilidade diante de consumidores que buscam economia. A estrategia combina B2B e B2C: o lojista paga pelo SaaS; o consumidor usa a vitrine, alertas e futuramente o app.

A decisao de pagamentos foi atualizada: Mercado Pago passa a ser o provedor principal do MVP. A integracao Asaas existente continua como referencia tecnica/legado, mas nao deve conduzir o lancamento.

## Onde estamos
- Sistema local carregando com Docker.
- Home e login respondendo.
- Todas as migrations aplicadas.
- 85 testes automatizados passando.
- Roadmap e pre-flight no super admin.
- Base demo navegavel.
- API mobile v1 criada.
- Mercado Pago decidido, mas ainda nao implementado como gateway real.

## Ideia do produto
O produto resolve uma dor dupla:
- Lojista: controlar financeiro, equipe, lojas, produtos, precos, assinatura e suporte.
- Consumidor: encontrar melhores precos, lojas, produtos e criar alertas.

O diferencial potencial esta na combinacao entre operacao e aquisicao: o lojista nao apenas organiza a empresa, ele transforma preco e vitrine em canal de demanda.

## Posicionamento sugerido
Uma plataforma para lojas transformarem preco, vitrine e operacao em aquisicao de clientes e controle de margem.

Promessa para lojistas:
- Publicar ofertas com mais clareza.
- Entender competitividade de precos.
- Controlar financeiro e operacao em um painel.
- Ganhar visibilidade em um ambiente de comparacao.

Promessa para consumidores:
- Encontrar onde comprar melhor.
- Acompanhar produtos e lojas.
- Criar alertas de preco.
- Economizar tempo.

## Perfis do sistema
- Super admin: governa a plataforma, contas, planos, assinaturas, suporte, analytics, roadmap e pre-flight.
- Lojista owner: administra conta, assinatura, equipe, lojas, produtos, precos e financeiro.
- Lojista gestor/financeiro/catalogo: acessa modulos conforme permissao.
- Cliente: acompanha ofertas e alertas.
- Visitante: consulta vitrine publica, novidades, suporte e paginas legais.

## Modulos atuais
### Publico
- Home.
- Ofertas.
- Radar de precos.
- Produto.
- Loja.
- Novidades.
- Suporte.
- Termos e privacidade.
- SEO tecnico.

### Lojista
- Dashboard.
- Centro de lancamento.
- Onboarding.
- Financeiro.
- Categorias financeiras.
- Contas financeiras.
- Lancamentos.
- Contas a pagar.
- Contas a receber.
- Lojas.
- Produtos.
- Precos.
- Equipe.
- Perfil.
- Configuracoes.
- Assinatura.
- Auditoria.
- Notificacoes.

### Super admin
- Dashboard executivo.
- Analytics.
- Roadmap.
- Pre-flight.
- Contas.
- Planos.
- Assinaturas.
- Sincronizacao de billing atual.
- Suporte em cards.

### Cliente
- Radar pessoal.
- Alertas.
- Notificacoes.

### API mobile
- Ofertas.
- Produto.
- Loja.
- Cadastro.
- Login.
- Sessao atual.
- Logout.
- Alertas.

## Arquitetura tecnica
- Laravel 12.
- PHP 8.3.
- MySQL 8.
- Redis.
- Mailpit.
- Laravel Sanctum.
- Docker/Sail.
- Blade/CSS proprio.
- Vite/Tailwind disponivel.

## Pagamentos
### Decisao atual
Mercado Pago sera o provedor principal do MVP.

### Estado atual
- Configuracao Mercado Pago existe em `config/billing.php`.
- Variaveis mapeadas: `MERCADO_PAGO_ACCESS_TOKEN`, `MERCADO_PAGO_PUBLIC_KEY`, `MERCADO_PAGO_WEBHOOK_SECRET`.
- Gateway Mercado Pago ainda nao existe.
- Webhook Mercado Pago ainda nao existe.
- BillingManager ainda nao opera Mercado Pago.
- Formulario de assinatura ainda nao esta pronto para usar Mercado Pago operacionalmente.
- Asaas possui integracao inicial e deve ser tratado como legado/referencia.

### O que precisa ser feito
- Criar client Mercado Pago.
- Criar gateway Mercado Pago.
- Registrar gateway no BillingManager.
- Criar webhook Mercado Pago.
- Criar processor de eventos.
- Auditar eventos na tabela existente.
- Atualizar telas de assinatura.
- Criar testes de integracao e webhook.
- Validar sandbox ponta a ponta.

## App mobile
Flutter continua sendo a recomendacao para Android/iOS.

Motivos:
- Uma base de codigo.
- Menor custo inicial.
- Velocidade para MVP.
- Boa integracao com API REST.

Escopo inicial:
- Login/cadastro.
- Home com ofertas.
- Busca e filtros.
- Produto.
- Loja.
- Alertas.
- Perfil.

Fora do primeiro app:
- Pagamento no app.
- Marketplace.
- Compra direta.
- Geolocalizacao avancada.
- Funcionalidades de lojista.

## Estado de maturidade
Pronto para:
- Demonstracao local.
- Validacao assistida.
- Conversas comerciais.
- Preparacao de contas piloto.
- QA interno.

Ainda nao pronto para:
- Producao aberta.
- Venda self-service.
- Cobranca real com Mercado Pago.
- App nas lojas.
- Trafego publico amplo.

## Principais riscos
- Mercado Pago ainda nao implementado, apesar de agora ser decisao do MVP.
- Visual ainda precisa parecer mais SaaS premium.
- Comparador depende de densidade de lojas, produtos e precos.
- Analytics existe, mas precisa de eventos reais para gerar leitura.
- Producao ainda depende de dominio, SSL, backup, fila, scheduler e monitoramento.
- Permissoes e policies devem amadurecer antes de escala maior.

## Prioridades de lancamento
1. Mercado Pago operacional.
2. Producao segura.
3. QA por perfil.
4. Revisao visual/copy.
5. Dados reais ou demo forte para apresentacao.
6. Monitoramento e suporte.
7. Contas piloto.
8. App Flutter depois do web validado.

## Criterio de MVP lancavel
- Pre-flight sem bloqueios criticos.
- Mercado Pago validado em sandbox.
- Pagamento aprovado, recusado, atrasado e cancelado refletindo no sistema.
- Dominio HTTPS.
- E-mail real.
- Backup e monitoramento.
- Lojista consegue operar sem ajuda tecnica.
- Cliente consegue buscar ofertas e criar alerta.
- Super admin consegue acompanhar contas, suporte, billing e analytics.

## Conclusao
O Mania de Preco ja passou do ponto de prototipo simples: existe base SaaS, multi-painel, catalogo, financeiro, suporte, analytics e API mobile. O gargalo agora e lancamento real. A decisao por Mercado Pago muda a proxima prioridade: antes de criar mais recursos, precisamos transformar pagamento em uma trilha operacional de SaaS, com assinatura, checkout, webhook e status financeiro confiaveis.
