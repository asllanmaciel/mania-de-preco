# Correção de acesso super admin local

## Contexto
- O super admin existia no banco com `is_super_admin = 1`, mas o acesso podia cair em erro após login.
- O MySQL local estava com migrations pendentes, incluindo campos legais usados no checklist de lançamento.
- Acessar a URL de um painel incompatível com o perfil retornava 403, o que confundia o fluxo de login.

## Alterações realizadas
- Criado `config/view.php` para respeitar `VIEW_COMPILED_PATH` e permitir compilar views em `/tmp` no Docker local.
- Ajustado middleware de painel para redirecionar requisições `GET` ao painel correto do usuário.
- Mantido 403 para ações sensíveis em painel errado.
- Atualizados testes de acesso para cobrir redirecionamento amigável e proteção em `POST`.

## Validação planejada
- Rodar migrations no banco local.
- Validar login com `admin@maniadepreco.com.br / password`.
- Validar que `/admin` redireciona super admin para `/super-admin`.
