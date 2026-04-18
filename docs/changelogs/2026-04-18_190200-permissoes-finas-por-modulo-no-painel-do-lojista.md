# Permissoes finas por modulo no painel do lojista

**tipo:** feature  
**impacto:** alto  
**modulo:** admin / permissoes / governanca

## Resumo executivo

O painel do lojista passou a aplicar permissoes por modulo, conectando papeis da equipe a acessos reais em financeiro, catalogo, precos, lojas, onboarding e gestao de equipe.

## Entregas realizadas

- criacao de uma politica central de capacidades por papel da conta
- protecao das rotas de financeiro, catalogo, precos, lojas, equipe e onboarding
- ajuste da navegacao do admin para exibir apenas modulos permitidos ao perfil logado
- adaptacao do dashboard para nao oferecer atalhos bloqueados ao usuario
- ajuste do onboarding para ocultar acoes sem permissao e evitar caminhos quebrados
- cobertura automatizada para perfis financeiro, catalogo, viewer e governanca da equipe
- correcao da aplicacao de middleware no grupo financeiro para garantir bloqueio real por modulo

## Estrategia aplicada

A estrategia foi transformar papeis em regras operacionais reais. Em vez de usar apenas nomes de perfil, o sistema agora trabalha com capacidades de acesso por modulo, criando uma camada mais flexivel, testavel e pronta para evoluir para permissoes ainda mais granulares conforme o produto cresce.

## Resultado

O Mania de Preco ganha uma governanca muito mais segura para contas com equipes reais. Cada pessoa passa a enxergar e acessar apenas o que faz sentido para sua responsabilidade, reduzindo risco operacional, melhorando clareza no uso diario e preparando o SaaS para clientes maiores com separacao de funcoes.
