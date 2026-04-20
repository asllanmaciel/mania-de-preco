# Radar publico com snapshot atualizavel

**tipo:** melhoria  
**impacto:** alto  
**modulo:** front publico / radar de precos / experiencia em tempo quase real

## Resumo executivo

O radar da home publica evoluiu de um bloco visual estatico para uma area preparada para atualizacao automatica. A vitrine agora possui um endpoint publico de snapshot de mercado e o front consegue sincronizar dados de preco sem exigir recarregamento da pagina.

## Entregas realizadas

- criacao da rota publica `/radar-precos`
- inclusao de resposta JSON com totais, lojas ativas, ranking, pulso do grafico e itens do ticker
- refatoracao do catalogo publico para compartilhar filtros entre a home e o endpoint do radar
- centralizacao da montagem da fotografia de mercado em um snapshot reutilizavel
- atualizacao do card de radar para consumir dados via atributos `data-*`
- polling leve no front para sincronizar o radar periodicamente
- atualizacao dinamica de horario, minimo, maximo, lojas ativas, ranking, janela de economia e ticker
- fallback visual para falha de conexao sem quebrar a experiencia da pagina
- cobertura automatizada para validar o endpoint e a exposicao do radar vivo na home

## Estrategia aplicada

A implementacao prepara uma experiencia de tempo quase real sem depender ainda de WebSocket, fila ou infraestrutura de streaming. Isso permite lancar com confianca agora e evoluir depois para push em tempo real quando houver volume e necessidade operacional.

## Resultado

O Mania de Preco passa a ter uma vitrine publica mais viva e tecnicamente preparada para refletir alteracoes de preco feitas pelas lojas, aumentando percepcao de atualidade e valor para quem compara ofertas.
