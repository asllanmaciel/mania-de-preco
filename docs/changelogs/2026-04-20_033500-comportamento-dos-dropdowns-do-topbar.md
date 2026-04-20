# Comportamento dos dropdowns do topbar

## Contexto

Os menus do topbar ja tinham notificacoes, atalhos e perfil, mas podiam ficar abertos ao mesmo tempo. Em uma interface administrativa de uso real, esse comportamento deixa a tela visualmente poluida e menos previsivel.

## Alteracoes realizadas

- Fechamento automatico de outros dropdowns quando um menu do topbar e aberto.
- Fechamento dos dropdowns ao clicar fora da area do menu.
- Fechamento dos dropdowns com a tecla `Escape`.
- Aplicacao do comportamento no painel lojista.
- Aplicacao do comportamento no backoffice, super admin e area do cliente.

## Arquivos impactados

- `resources/views/layouts/admin.blade.php`
- `resources/views/layouts/backoffice.blade.php`

## Validacao planejada

- Executar testes de acesso administrativo.
- Executar testes da experiencia do cliente.
- Conferir `git diff --check`.
