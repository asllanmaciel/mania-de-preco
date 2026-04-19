# Central de suporte com chamados

**tipo:** feature  
**impacto:** alto  
**modulo:** suporte / super admin / operacao

## Resumo executivo

O Mania de Preco ganhou uma central de suporte mais proxima de uma operacao SaaS real, com formulario publico de abertura de chamados, protocolo automatico, status operacional, FAQ e uma fila de atendimento no painel de super admin.

## Entregas realizadas

- criacao da tabela de chamados de suporte com protocolo, prioridade, status, categoria, origem, contexto e vinculos opcionais com conta e usuario
- criacao do model de chamados com listas centralizadas de status, categorias e prioridades
- criacao do envio publico de chamados pela pagina de suporte
- geracao automatica de protocolo para cada novo chamado
- enriquecimento da pagina publica de suporte com status operacional, FAQ, orientacoes e formulario completo
- criacao da central de suporte no super admin com metricas, filtros, lista de chamados e atualizacao de status/prioridade
- inclusao de suporte na navegacao do super admin
- inclusao de chamados abertos nas metricas do dashboard de super admin
- cobertura automatizada para abertura publica de chamado e gestao da fila pelo super admin

## Estrategia aplicada

A estrategia foi transformar suporte em fluxo operacional rastreavel desde o lancamento. Em vez de depender apenas de e-mail, o produto passa a guardar contexto, gerar protocolo e permitir que o backoffice priorize incidentes, cobranca, acesso, catalogo e privacidade.

## Resultado

O sistema fica mais preparado para clientes reais, com visibilidade de atrito, base para SLA, atendimento estruturado e historico operacional. Essa entrega abre caminho para notificacoes por e-mail, respostas ao cliente, central de ajuda completa, status page e integracao futura com ferramentas externas de atendimento.
