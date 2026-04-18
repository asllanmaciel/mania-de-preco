# Limites operacionais por plano

**tipo:** feature  
**impacto:** alto  
**modulo:** billing / planos / governanca

## Resumo executivo

O SaaS passou a aplicar limites reais de uso por plano, conectando a configuracao comercial de assinaturas com a operacao diaria da conta.

## Entregas realizadas

- criacao de um medidor central de uso da conta por plano
- calculo de usuarios ativos, lojas cadastradas e produtos conectados ao comparador
- bloqueio de novas lojas quando o limite contratado for atingido
- bloqueio de novos membros ativos quando o limite de usuarios for atingido
- bloqueio de novos produtos no comparador quando o limite do plano for atingido
- exibicao de consumo do plano no dashboard administrativo do lojista
- exibicao de consumo em lojas, equipe e precos para orientar a operacao no contexto certo
- exibicao do uso comercial do plano no detalhe da conta no super admin
- ajuste do seed demo para manter a conta em um plano com capacidade visivel
- cobertura automatizada para validar bloqueios de lojas, usuarios e produtos

## Estrategia aplicada

A estrategia foi transformar planos em contratos operacionais executaveis. Em vez de deixar os limites apenas como informacao comercial, o sistema agora mede consumo, mostra capacidade disponivel e impede que a conta ultrapasse o que foi contratado.

## Resultado

O Mania de Preco ganha uma base mais madura para monetizacao, upgrade de plano e atendimento de contas maiores. A operacao fica mais previsivel, o super admin passa a enxergar oportunidades comerciais com mais clareza e o lojista entende quando esta perto de precisar crescer de plano.
