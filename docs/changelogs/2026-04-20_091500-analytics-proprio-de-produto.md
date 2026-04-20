# Analytics proprio de produto

## Contexto

Para lancar com mais seguranca, a plataforma precisava capturar sinais basicos de uso sem depender imediatamente de ferramentas externas. Busca, visualizacao de produto, visualizacao de loja, cadastro e suporte sao eventos essenciais para entender tracao, conversao e atritos iniciais.

## Alteracoes realizadas

- Criacao da configuracao `config/analytics.php` com chave para habilitar ou pausar analytics interno.
- Criacao da tabela `analytics_events` para armazenar eventos de produto.
- Criacao do model `AnalyticsEvent`.
- Criacao do servico `ProductAnalytics` para centralizar captura de eventos.
- Captura de filtros e buscas na vitrine publica.
- Captura de visualizacao de produto publico.
- Captura de visualizacao de loja publica.
- Captura de cadastro de cliente.
- Captura de abertura de chamado publico.
- Inclusao de metricas de eventos no dashboard do super admin.
- Inclusao de lista de sinais recentes e ranking de eventos dos ultimos 7 dias no super admin.
- Inclusao do analytics interno no farol global de lancamento.
- Cobertura de testes para eventos publicos, cadastro e suporte.

## Arquivos impactados

- `config/analytics.php`
- `database/migrations/2026_04_20_090000_create_analytics_events_table.php`
- `app/Models/AnalyticsEvent.php`
- `app/Support/Analytics/ProductAnalytics.php`
- `app/Http/Controllers/Web/PublicCatalogController.php`
- `app/Http/Controllers/Web/PublicProductController.php`
- `app/Http/Controllers/Web/PublicStoreController.php`
- `app/Http/Controllers/Web/Auth/RegisteredUserController.php`
- `app/Http/Controllers/Web/PublicTrustController.php`
- `app/Http/Controllers/Web/SuperAdmin/DashboardController.php`
- `app/Support/Lancamento/PlatformLaunchReadiness.php`
- `resources/views/super-admin/dashboard.blade.php`
- `tests/Feature/Web/PublicCatalogTest.php`
- `tests/Feature/Web/ClienteExperienceTest.php`
- `tests/Feature/Web/AdminAccessTest.php`

## Validacao planejada

- Executar testes publicos do catalogo.
- Executar testes da experiencia do cliente.
- Executar testes de acesso administrativo.
- Executar a suite completa.
- Conferir `git diff --check`.
