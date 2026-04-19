# Topbar admin inspirado no MatDash

**tipo:** melhoria  
**impacto:** medio  
**modulo:** admin / UI / UX / layout

## Resumo executivo

O layout administrativo foi ajustado para separar a barra superior do cabecalho da pagina. A topbar passa a funcionar como area de ferramentas, com contexto compacto da conta, atalhos, notificacoes e menu do usuario, enquanto titulo e subtitulo ficam em uma area propria do conteudo.

## Entregas realizadas

- analise da estrutura de topbar do template MatDash local
- remocao do titulo e subtitulo de dentro da topbar administrativa
- criacao do bloco `page-heading` para titulo, subtitulo e status da conta
- topbar mais compacta e alinhada ao padrao de painel SaaS
- manutencao dos atalhos rapidos no topo
- manutencao do dropdown de notificacoes
- manutencao do dropdown do usuario com conta ativa
- ajuste responsivo para tablets e mobile
- validacao automatizada das telas administrativas

## Estrategia aplicada

A referencia MatDash usa a topbar como barra de navegacao e ferramentas, deixando o contexto da pagina fora dela. O ajuste segue essa logica sem copiar o template diretamente, preservando a identidade visual atual do Mania de Preco.

## Resultado

O painel fica mais limpo, menos pesado visualmente e mais proximo do comportamento esperado em dashboards profissionais: a topbar permanece fixa para acoes recorrentes, e cada tela ganha seu proprio cabecalho contextual abaixo dela.
