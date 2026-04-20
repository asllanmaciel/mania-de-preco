# Health check operacional

## Contexto

Para operar em produção com monitoramento real, o sistema precisa de um endpoint simples e seguro que indique se a aplicação está viva e se dependências essenciais estão respondendo.

## Alterações realizadas

- Criação do endpoint público `GET /health` com resposta JSON.
- Validação de chave da aplicação, conexão com banco, escrita/leitura de cache e permissões básicas de storage.
- Resposta `200` quando todos os checks passam e `503` quando algum item essencial falha.
- Inclusão de header `Cache-Control: no-store, no-cache, must-revalidate` para evitar cache em monitores externos.
- Conexão do health check ao farol de prontidão global do super admin.
- Cobertura de teste para validar estrutura, status e checks principais do endpoint.

## Arquivos impactados

- `app/Http/Controllers/Web/HealthCheckController.php`
- `routes/web.php`
- `app/Support/Lancamento/PlatformLaunchReadiness.php`
- `tests/Feature/HealthCheckTest.php`

## Validação planejada

- Executar teste específico do health check.
- Executar a suíte completa.
- Conferir `git diff --check`.
