# Governanca de equipe no painel do lojista

**tipo:** feature  
**impacto:** alto  
**modulo:** admin / equipe / permissoes

## Resumo executivo

O painel do lojista passou a ter uma base real de governanca de equipe, permitindo separar lideranca, operacao e financeiro com mais seguranca dentro da conta.

## Entregas realizadas

- criacao da area de equipe no painel administrativo da conta
- adicao de papeis operacionais para organizar responsabilidades internas
- criacao do fluxo de cadastro e edicao de membros da conta
- protecao da governanca da equipe para `owner` e `gestor`
- bloqueio de acesso para perfis operacionais sem permissao de administracao de pessoas
- ampliacao do seed demo com membros extras para deixar essa camada visivel no ambiente
- cobertura automatizada para gestao de equipe e bloqueio por papel

## Estrategia aplicada

A estrategia foi separar acesso administrativo de governanca da conta. Em vez de permitir que qualquer usuario com entrada no painel admin controle pessoas e papeis, o produto agora passa a reconhecer niveis de responsabilidade dentro da conta, o que melhora seguranca, organizacao operacional e capacidade de escalar times sem perda de controle.

## Resultado

O Mania de Preco ganha uma base mais madura para operar clientes reais com multiplos usuarios. Isso reduz dependencia de um unico login, melhora a distribuicao de responsabilidade dentro da conta e prepara o sistema para proximas evolucoes de permissao fina, auditoria e onboarding comercial de equipes.
