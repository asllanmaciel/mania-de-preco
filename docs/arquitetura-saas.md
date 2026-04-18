# Arquitetura SaaS do Mania de Preço

## Objetivo

Construir um SaaS para pequenos lojistas com dois resultados no mesmo produto:

- controlar a operação financeira
- ganhar visibilidade ao publicar preços e catálogo

## Estratégia de produto

O projeto foi organizado com foco em B2B primeiro.

- Quem paga: lojista
- Quem usa o comparador: consumidor final
- Efeito de rede: mais lojas cadastradas melhoram a busca pública

## Blocos de domínio

### 1. Multiempresa

`Conta` é o tenant principal do sistema.

Cada conta possui:

- usuários
- lojas
- assinatura
- categorias financeiras
- contas financeiras
- movimentações
- contas a pagar
- contas a receber

### 2. Catálogo e comparação

O catálogo de `categorias`, `marcas` e `produtos` foi mantido como base compartilhada.

Isso permite:

- padronizar produtos entre lojas
- comparar preços do mesmo item
- evoluir depois para catálogo interno por loja sem perder o comparador

### 3. Financeiro

O financeiro roda por conta, com possibilidade de segmentação por loja.

Principais entidades:

- `categorias_financeiras`: classifica receitas e despesas
- `contas_financeiras`: caixa, banco, cartão ou carteira digital
- `movimentacoes_financeiras`: lançamentos efetivos ou previstos
- `contas_pagar`: obrigações financeiras
- `contas_receber`: receitas previstas

## Regras importantes

### Isolamento de dados

Todo recurso financeiro pertence a uma `conta`.

Toda `loja` também pertence a uma `conta`.

Qualquer escrita privada deve respeitar essa regra.

### Cadastro inicial

No registro de usuário:

- cria usuário
- cria conta trial
- vincula o usuário como `owner`
- gera token Sanctum

### Segurança

Correções aplicadas na base:

- Sanctum habilitado no `User`
- `CategoriaController` implementado
- `alertas` agora sempre pertencem ao usuário autenticado
- `avaliações` agora sempre pertencem ao usuário autenticado
- `lojas` e `preços` agora validam acesso à conta

## Arquitetura recomendada para a próxima etapa

### Camada de aplicação

- Requests dedicadas por recurso
- Policies por conta e por papel
- Services para onboarding, assinatura e consolidação financeira

### Camada de produto

- dashboard por conta
- relatórios por período
- variação histórica de preços
- favoritos e alertas públicos
- importação por planilha

### Camada operacional

- fila para alertas de preço
- jobs para atualização/normalização de catálogo
- observabilidade com logs e eventos por conta

## Roadmap técnico sugerido

### Fase 1

- autenticação e onboarding
- CRUD financeiro básico
- contas e lojas
- catálogo e preços

### Fase 2

- dashboard consolidado
- contas a pagar e receber com baixas
- filtros e paginação de catálogo
- histórico de preço

### Fase 3

- estoque por loja
- importação em massa
- métricas de margem
- automações e alertas
