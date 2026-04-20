# Layout full width e headers de segurança

## Contexto

O painel ainda estava visualmente preso a um container central no desktop, fazendo o topbar parecer menor do que a área real de trabalho. Além disso, faltava uma camada básica de headers de segurança para respostas web antes de produção.

## Alterações realizadas

- Ajuste do layout base do painel lojista para usar largura total no desktop.
- Ajuste do layout base do backoffice e super admin para topbar full width no desktop.
- Preservação de espaçamentos específicos para mobile.
- Criação do middleware `ApplySecurityHeaders`.
- Aplicação de headers `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy` e `Permissions-Policy` nas rotas web.
- Cobertura de teste para garantir os headers de segurança no endpoint de health check.

## Arquivos impactados

- `resources/views/layouts/admin.blade.php`
- `resources/views/layouts/backoffice.blade.php`
- `app/Http/Middleware/ApplySecurityHeaders.php`
- `bootstrap/app.php`
- `tests/Feature/HealthCheckTest.php`

## Validação planejada

- Executar teste de health check.
- Executar testes administrativos.
- Executar a suíte completa.
- Conferir `git diff --check`.
