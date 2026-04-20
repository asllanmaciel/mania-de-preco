# Auditoria visual e tipografica do front

## Objetivo

Registrar uma leitura objetiva do front atual do Mania de Preco para orientar os proximos ciclos de melhoria visual, UX e consistencia de produto.

## Mapa de interfaces

- Publico comercial: home, ofertas, loja publica, produto publico, novidades e paginas institucionais.
- Entrada e autenticacao: login, cadastro, recuperacao e redefinicao de senha.
- Painel lojista: dashboard, lancamento, onboarding, notificacoes, financeiro, lojas, produtos, precos, equipe, assinatura, configuracoes e auditoria.
- Backoffice: super admin, cliente final, suporte, planos, contas e notificacoes do cliente.

## Diagnostico atual

- O front publico tem uma linguagem mais comercial, quente e memoravel, baseada em `Space Grotesk`, tons terrosos e chamadas de conversao.
- Os paineis internos caminham para uma linguagem SaaS mais operacional, inspirada em MatDash, com cards, sidebar, topbar, icones e indicadores.
- A tipografia interna estava menos alinhada ao visual de dashboard profissional. O primeiro ajuste foi migrar admin, auth e backoffice para `Plus Jakarta Sans`.
- O topbar administrativo evoluiu bem, mas ainda precisa de refinamento fino em hierarquia, espaçamento, dropdowns e comportamento mobile.
- Ha CSS inline duplicado em varios arquivos publicos, o que dificulta consistencia e velocidade de evolucao.
- Algumas telas ainda usam muito texto explicativo e precisam ficar mais orientadas a decisao, especialmente em cards e listas administrativas.

## Decisoes de direcao visual

- Manter `Space Grotesk` no front publico para preservar energia comercial e personalidade de marca.
- Usar `Plus Jakarta Sans` nos ambientes internos para aproximar a experiencia de dashboard SaaS internacional.
- Manter `IBM Plex Mono` para codigos, indicadores compactos, badges tecnicos e pequenos elementos de leitura operacional.
- Priorizar mobile-first nas proximas telas, evitando layouts que dependem de tabelas largas ou blocos densos.
- Reduzir copy operacional repetitiva dentro de cards e usar mais hierarquia visual, icones e microindicadores.

## Prioridades recomendadas

1. Consolidar tokens visuais compartilhados para reduzir duplicacao de CSS.
2. Refinar o topbar e sidebar do admin ate parecerem definitivos.
3. Revisar o dashboard lojista para reduzir densidade e destacar decisoes principais.
4. Redesenhar listagens de produtos, lojas e precos com cards/tabelas mais premium e melhor leitura mobile.
5. Revisar a area do cliente final para ficar mais emocional, simples e direta.
6. Revisar o super admin para leitura executiva de plataforma, MRR, risco, suporte e contas.
7. Padronizar estados vazios, badges, botoes, filtros, formularios e mensagens de feedback.
8. Revisar paginas publicas para remover qualquer linguagem interna e manter copy de alta conversao.

## Primeira entrega aplicada

- Admin, auth e backoffice passaram a usar `Plus Jakarta Sans`.
- Os layouts internos ganharam variaveis de fonte.
- Titulos internos receberam ajuste de tracking para leitura mais limpa.
- Foi mantida a separacao entre linguagem publica comercial e linguagem interna operacional.

## Proximo ciclo sugerido

Refinar o dashboard lojista como tela principal de valor: menos blocos competindo, cards mais editoriais, graficos mais claros, indicadores com hierarquia mais forte e uma narrativa mais direta de decisao.
