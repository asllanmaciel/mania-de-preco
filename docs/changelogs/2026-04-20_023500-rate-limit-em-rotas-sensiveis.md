# Rate limit em rotas sensíveis

## Contexto

Antes de abrir tráfego real, formulários públicos e endpoints de autenticação precisam de proteção contra abuso, spam e tentativas repetidas automatizadas.

## Alterações realizadas

- Aplicação de rate limit no endpoint público do radar de preços.
- Aplicação de rate limit na abertura de chamados pelo suporte público.
- Aplicação de rate limit em login, cadastro, solicitação de recuperação de senha e envio de nova senha.
- Inclusão do item "Limites de abuso em rotas sensíveis" no farol global de lançamento do super admin.
- Cobertura de teste para garantir que o suporte público bloqueie excesso de tentativas.
- Cobertura de teste para garantir que o farol global exiba a proteção como etapa de lançamento.

## Arquivos impactados

- `routes/web.php`
- `app/Support/Lancamento/PlatformLaunchReadiness.php`
- `tests/Feature/Web/AdminAccessTest.php`
- `tests/Feature/Web/PublicCatalogTest.php`

## Validação planejada

- Executar testes públicos do catálogo.
- Executar testes de acesso administrativo.
- Executar a suíte completa.
- Conferir `git diff --check`.
