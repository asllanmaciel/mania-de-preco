# Changelogs do Projeto

Esta pasta centraliza os changelogs do projeto seguindo uma lógica orientada a produto.

## Princípios

- escrever como produto, não como código
- destacar valor entregue e impacto real
- evitar jargão técnico desnecessário
- manter clareza, objetividade e visão estratégica
- não usar emojis

## Estrutura obrigatória

Todo changelog deve conter:

1. título estratégico
2. tipo
3. impacto
4. módulo
5. resumo executivo
6. entregas realizadas
7. estratégia aplicada
8. resultado

## Tipos permitidos

- `feature`
- `improvement`
- `fix`
- `refactor`
- `infra`

## Impactos permitidos

- `baixo`
- `médio`
- `alto`

## Convenção de nomes

Use o padrão:

```text
YYYY-MM-DD_HHMMSS-slug-do-contexto.md
```

Exemplo:

```text
2026-04-18_123552-fundacao-saas.md
```

## Regra de uso neste projeto

- toda entrega relevante deve gerar um arquivo novo nesta pasta
- o texto deve explicar o que foi entregue e qual valor isso cria
- se houver trabalho em andamento, ele só deve entrar no changelog quando já representar uma entrega concreta
