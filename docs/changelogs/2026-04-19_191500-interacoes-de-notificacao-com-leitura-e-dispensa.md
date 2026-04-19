# Interacoes de notificacao com leitura e dispensa

**tipo:** feature  
**impacto:** alto  
**modulo:** notificacoes / UX operacional / admin / cliente

## Resumo executivo

A central de notificacoes ganhou persistencia de interacoes. Agora o usuario pode marcar alertas como vistos e dispensar avisos por 24 horas, reduzindo ruido operacional sem perder o historico de sinais importantes.

## Entregas realizadas

- criacao da tabela `notificacao_interacoes`
- criacao do model `NotificacaoInteracao`
- inclusao de chaves estaveis para cada notificacao gerada pelo motor central
- persistencia de leitura por usuario, contexto e escopo
- dispensa temporaria de notificacoes por 24 horas
- nova rota `PATCH /admin/notificacoes/interacao`
- nova rota `PATCH /cliente/notificacoes/interacao`
- botoes de marcar como vista e dispensar na central administrativa
- botoes de marcar como vista e dispensar na central do cliente
- topbar administrativa priorizando apenas notificacoes pendentes
- cobertura automatizada para interacao nas centrais de admin e cliente

## Estrategia aplicada

A persistencia foi criada separada da geracao das notificacoes. Isso mantem o motor dinamico e evita duplicar dados de negocio, enquanto registra apenas o estado de interacao do usuario com cada aviso.

## Resultado

O sistema fica mais proximo de uma operacao real de SaaS: as notificacoes passam a respeitar o contexto do usuario, diminuem repeticao desnecessaria e tornam a central de acoes mais util no uso diario.
