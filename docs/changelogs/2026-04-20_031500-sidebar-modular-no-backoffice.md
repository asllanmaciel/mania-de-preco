# Sidebar modular no backoffice

## Contexto

Depois de ajustar o painel lojista para uma navegacao inspirada no Matdash, o backoffice ainda permanecia com um menu simples de links. Isso criava diferenca visual entre areas do produto e deixava o super admin menos alinhado com o padrao de produto robusto.

## Alteracoes realizadas

- Transformacao do sidebar do backoffice em uma estrutura com rail de modulos e painel contextual.
- Criacao de modulos especificos para o super admin: Governanca, Contas, Receita e Suporte.
- Criacao de modulos especificos para a area do cliente: Cliente e Atalhos.
- Preservacao da exibicao adaptada conforme perfil, rota atual e dados disponiveis da conta.
- Inclusao de setas nos dropdowns de notificacoes, atalhos e usuario no topbar do backoffice.
- Inclusao de JavaScript leve para alternar modulos sem recarregar a pagina.
- Manutencao de exibicao expandida em telas menores para preservar navegacao mobile.

## Arquivos impactados

- `resources/views/layouts/backoffice.blade.php`

## Validacao planejada

- Executar testes de acesso administrativo e super admin.
- Executar testes da experiencia do cliente.
- Executar a suite completa.
- Conferir `git diff --check`.
