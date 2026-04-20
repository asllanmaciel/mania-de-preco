# Central rapida de comandos no topbar admin

**tipo:** melhoria  
**impacto:** medio  
**modulo:** admin / UI / UX / navegacao

## Resumo executivo

O campo de busca do topbar administrativo passou a ser uma central rapida de comandos. O usuario pode abrir um painel de atalhos, filtrar destinos e navegar para areas importantes sem depender apenas do menu lateral.

## Entregas realizadas

- transformacao do acionador de busca em dropdown funcional
- criacao da central rapida com comandos filtraveis
- suporte ao atalho `Ctrl K`
- filtro local por titulo, descricao e palavras-chave
- comandos respeitando permissoes da conta
- links para dashboard, lancamento, notificacoes, onboarding, financeiro, lojas, produtos, precos, equipe, configuracoes e home publica
- estado vazio quando nenhum comando corresponde a busca
- cobertura automatizada para garantir a presenca da central rapida

## Estrategia aplicada

A melhoria aproveita a estrutura atual do Blade e adiciona comportamento leve com JavaScript local. Assim o painel ganha velocidade de navegacao sem adicionar dependencias front-end ou alterar as rotas existentes.

## Resultado

O topbar fica mais util e menos decorativo. A navegacao passa a se comportar como produto SaaS moderno, com acesso rapido a tarefas frequentes e descoberta mais facil das areas principais do sistema.
