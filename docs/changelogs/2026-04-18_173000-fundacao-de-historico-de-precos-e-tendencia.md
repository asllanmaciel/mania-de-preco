# Fundação de histórico de preços e tendência

**tipo:** feature  
**impacto:** alto  
**modulo:** preços / inteligência de mercado / base analítica

## Resumo executivo

O sistema passou a registrar histórico real de preços sempre que uma oferta é criada, alterada ou removida. Isso transforma o comparador em uma base temporal de mercado, pronta para sustentar tendência, auditoria, alertas melhores e leitura estratégica mais profunda.

## Entregas realizadas

- criação da tabela `historicos_precos` para armazenar eventos de preço ao longo do tempo
- registro automático de histórico na criação, atualização e remoção de ofertas
- preservação de contexto de produto, loja, tipo de preço e variação no momento de cada evento
- criação de endpoint público para consultar a linha do tempo de preços por produto
- adição de relações de histórico nos modelos centrais de catálogo e preço
- ampliação da cobertura automatizada para garantir auditoria e timeline consistentes

## Estratégia aplicada

A estratégia foi fortalecer primeiro a fundação analítica do produto. Antes de desenhar gráficos temporais ou alertas sofisticados na interface, o sistema precisava capturar os eventos certos com consistência, contexto e rastreabilidade.

## Resultado

O Mania de Preço deixa de comparar apenas o presente e passa a construir memória de mercado. Isso abre caminho para recursos mais valiosos no futuro, como tendência de preço, histórico por loja, alertas de queda com mais inteligência e páginas de produto muito mais ricas.
