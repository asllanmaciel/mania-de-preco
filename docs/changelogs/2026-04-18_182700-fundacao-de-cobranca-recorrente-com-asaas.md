# Fundacao de cobranca recorrente com base pronta para escala

**tipo:** feature  
**impacto:** alto  
**modulo:** billing / assinaturas / super admin

## Resumo executivo

O Mania de Preco passou a ter uma fundacao real de cobranca recorrente, com Asaas como primeiro provedor operacional e arquitetura preparada para incorporar novos meios de pagamento no crescimento do produto.

## Entregas realizadas

- criacao da camada estrutural de billing para contas e assinaturas
- integracao inicial com Asaas para sincronizar cliente e assinatura recorrente
- criacao de rota de webhook para manter o status financeiro alinhado com o provedor
- registro de eventos de webhook para rastreabilidade, idempotencia e operacao mais segura
- liberacao de acao no super admin para sincronizar a cobranca da conta sem fluxo manual externo
- ampliacao do seed demo para deixar a conta preparada para integracao real
- cobertura automatizada da sincronizacao com o provedor e do processamento de webhook

## Estrategia aplicada

A estrategia foi iniciar por um provedor mais aderente ao contexto de SaaS recorrente no Brasil, sem acoplar o produto a uma unica empresa. Em vez de construir uma integracao fechada e dificil de expandir, a base foi organizada para separar cliente externo, assinatura externa, sincronizacao e webhook, criando um caminho seguro para crescimento comercial e futuras expansoes para outros gateways.

## Resultado

O produto sai de uma assinatura apenas interna e passa a ter base concreta para cobranca real, conciliacao de status e operacao de receita recorrente. Isso aproxima o sistema de um lancamento comercial de verdade e prepara o terreno para checkout, automacoes financeiras e governanca mais madura da base de clientes.
