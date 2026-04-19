# Central dinamica de notificacoes e acoes

**tipo:** feature  
**impacto:** alto  
**modulo:** notificacoes / admin / cliente / UX operacional

## Resumo executivo

As notificacoes deixaram de ser apenas itens visuais no dropdown e passaram a ser geradas a partir de dados reais do sistema. A central aponta riscos, alertas, oportunidades e proximas acoes para lojistas, clientes e super admin.

## Entregas realizadas

- criacao do motor `CentralNotificacoes`
- geracao dinamica de notificacoes para o painel lojista
- geracao dinamica de notificacoes para a area do cliente
- geracao inicial de sinais para o super admin
- nova rota `/admin/notificacoes`
- nova rota `/cliente/notificacoes`
- pagina de central de acoes no painel lojista
- pagina de notificacoes na area do cliente
- topbar administrativa conectada aos sinais reais da conta
- topbar do backoffice conectada aos sinais do cliente ou super admin
- menu administrativo com entrada para a central de notificacoes
- cobertura automatizada para abertura das centrais

## Estrategia aplicada

Nesta fase, a central foi implementada como leitura dinamica dos dados existentes, sem criar uma nova tabela de persistencia. Isso reduz complexidade, evita migracao desnecessaria e ja entrega valor real para o MVP: o sistema identifica o que merece acao agora.

## Resultado

O produto passa a orientar o usuario de forma mais ativa. Em vez de apenas exibir paineis, ele aponta prioridades como assinatura em risco, titulos vencendo, onboarding pendente, vitrine sem precos e alertas de preco atendidos.
