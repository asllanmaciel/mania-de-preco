# Ajuste tipográfico da landing pública

## Contexto

A direção editorial anterior deixou a primeira dobra visualmente pesada, com título grande demais e pouco amigável para uma landing de comparação de preços. O ajuste reduz o impacto excessivo e aproxima a página de uma linguagem mais comum, clara e acessível.

## Alterações realizadas

- Substituição da fonte serifada decorativa por uma fonte sans mais amigável e comum na experiência pública.
- Redução do tamanho máximo do título principal e melhoria da largura de leitura para evitar quebras exageradas.
- Ajuste do peso, espaçamento e altura de linha dos títulos para deixar a página mais leve.
- Remoção visual da marca d'água tipográfica grande no hero.
- Troca do logo horizontal por ícone e texto em HTML para evitar renderização cortada ou inconsistente no navegador.
- Pequena redução da altura da primeira dobra para diminuir a sensação de bloco pesado.

## Arquivos impactados

- `resources/views/welcome.blade.php`

## Validação planejada

- Executar testes públicos do catálogo.
- Conferir formatação com `git diff --check`.
