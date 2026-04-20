# Sidebar por modulos no painel lojista

## Contexto

O painel precisava se aproximar mais do comportamento visual do Matdash. A coluna de icones da esquerda estava funcionando como atalhos diretos, mas em produtos administrativos mais robustos esse espaco costuma trocar o contexto do menu lateral e revelar subitens do modulo selecionado.

## Alteracoes realizadas

- Transformacao dos icones da rail lateral em seletores de modulo.
- Organizacao do menu lateral em quatro grupos: Geral, Vitrine, Financeiro e Gestao.
- Exibicao de subitens apenas para o modulo ativo.
- Preservacao das permissoes por capacidade da conta para esconder itens indisponiveis.
- Inclusao de interacao em JavaScript leve para alternar modulos sem recarregar a pagina.
- Ajuste do texto de apoio do sidebar para explicar a navegacao por contexto.

## Arquivos impactados

- `resources/views/layouts/admin.blade.php`

## Validacao planejada

- Executar testes de acesso administrativo.
- Executar testes operacionais do admin.
- Conferir `git diff --check`.
