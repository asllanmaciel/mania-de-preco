# Automacao de baixa com reflexo em lancamentos e saldo

**tipo:** feature  
**impacto:** alto  
**modulo:** financeiro / automacao de baixa

## Resumo executivo

O financeiro passou a automatizar a baixa de titulos pagos e recebidos, refletindo esse evento em lancamentos financeiros e no saldo da conta financeira vinculada.

## Entregas realizadas

- adicao de vinculacao entre titulos, contas financeiras e lancamentos
- criacao de servico central para sincronizar baixa, lancamento e saldo
- atualizacao dos fluxos web e API de contas a pagar e contas a receber
- exigencia de conta financeira ao marcar um titulo como pago ou recebido
- indicacao visual no painel quando o titulo ja possui baixa automatica ativa
- execucao da migration no ambiente Docker e validacao automatizada da regra

## Estrategia aplicada

A entrega foi desenhada para eliminar retrabalho manual no financeiro. Em vez de depender de cadastros paralelos, o sistema agora usa o status do titulo como gatilho para materializar a movimentacao correspondente e recalcular o saldo da conta afetada.

## Resultado

O modulo financeiro fica mais proximo do comportamento esperado de um SaaS de gestao real, com mais consistencia entre previsao, baixa e caixa efetivo. Isso reduz erro operacional e cria uma base melhor para conciliacao e automacoes futuras.
