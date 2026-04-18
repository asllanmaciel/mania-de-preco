# Correcao de mapeamento das tabelas de avaliacoes e alertas

**tipo:** fix  
**impacto:** medio  
**modulo:** base de dados / painel administrativo

## Resumo executivo

Foi corrigido um problema de mapeamento entre models e tabelas que impedia a abertura da tela de lojas no admin quando o sistema tentava contar avaliacoes relacionadas.

## Entregas realizadas

- ajuste explicito do nome da tabela no model de avaliacoes de loja
- ajuste explicito do nome da tabela no model de alertas de preco
- adicao de teste cobrindo a abertura autenticada da tela de lojas
- revalidacao da suite de testes do painel administrativo

## Estrategia aplicada

A correcao foi feita na camada de modelagem para alinhar o comportamento do Eloquent com os nomes reais definidos nas migrations. Isso evita novas falhas causadas por pluralizacao automatica incorreta em tabelas compostas.

## Resultado

O painel administrativo volta a abrir a area de lojas sem tentar consultar uma tabela inexistente, e o projeto ganha uma protecao automatizada para impedir a volta silenciosa desse problema.
