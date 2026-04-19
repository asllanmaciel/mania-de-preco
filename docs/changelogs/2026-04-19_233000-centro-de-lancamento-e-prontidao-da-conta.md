# Centro de lancamento e prontidao da conta

**tipo:** feature  
**impacto:** alto  
**modulo:** admin / ativacao / operacao / go to market

## Resumo executivo

O painel lojista ganhou uma central de prontidao de lancamento. A nova area mostra se a conta esta pronta para ser apresentada, operada e usada por clientes reais, organizando pendencias por identidade comercial, vitrine, financeiro e governanca.

## Entregas realizadas

- criacao do analisador `ContaLaunchReadiness`
- nova rota `/admin/lancamento`
- novo controller `LancamentoController`
- nova tela `Centro de lancamento`
- score de prontidao de 0 a 100
- classificacao de maturidade da conta
- separacao de pendencias criticas e melhorias recomendadas
- mapa de prontidao por grupo
- proximas acoes priorizadas com links para as areas certas
- atalho no menu lateral administrativo
- atalho no dock mobile
- atalho no dashboard principal
- cobertura automatizada para abertura da central

## Estrategia aplicada

A nova camada nao substitui onboarding nem notificacoes. Ela consolida os sinais principais em uma leitura executiva de lancamento, ajudando o lojista a entender se a conta esta pronta para demonstracao, operacao diaria e exposicao publica.

## Resultado

O Mania de Preco fica mais proximo de um produto comercializavel: o painel deixa claro o que ainda bloqueia uma apresentacao forte e orienta o usuario para resolver as proximas prioridades sem depender de explicacao externa.
