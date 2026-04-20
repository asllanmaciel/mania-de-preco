# Prontidão global de lançamento

## Contexto

O produto já possui um centro de lançamento para a operação do lojista, mas ainda faltava uma leitura executiva para o super admin acompanhar se a plataforma como um todo está pronta para produção. A nova camada organiza os bloqueios antes do lançamento e deixa claro o que precisa ser configurado ou validado.

## Alterações realizadas

- Criação do analisador `PlatformLaunchReadiness` para avaliar a prontidão global do SaaS.
- Inclusão de grupos de checklist para produção e infraestrutura, receita e cobrança, experiência pública, operação e suporte.
- Cálculo de score global, bloqueios críticos e próximas ações recomendadas.
- Exibição do novo painel de prontidão no dashboard do super admin.
- Inclusão de indicadores para ambiente de produção, debug, HTTPS, fila, localização brasileira, Asaas, webhook, planos, assinaturas, e-mail transacional, vitrine pública, suporte e acessos.
- Ajuste dos defaults de timezone, idioma e faker para o contexto brasileiro.
- Cobertura de teste para garantir que o super admin visualize o checklist global de lançamento.

## Arquivos impactados

- `app/Support/Lancamento/PlatformLaunchReadiness.php`
- `app/Http/Controllers/Web/SuperAdmin/DashboardController.php`
- `resources/views/super-admin/dashboard.blade.php`
- `resources/views/layouts/backoffice.blade.php`
- `config/app.php`
- `tests/Feature/Web/AdminAccessTest.php`

## Validação planejada

- Executar os testes de acesso administrativo.
- Executar a suíte completa.
- Conferir `git diff --check`.
