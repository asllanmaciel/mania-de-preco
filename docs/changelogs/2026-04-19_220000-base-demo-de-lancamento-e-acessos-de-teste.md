# Base demo de lancamento e acessos de teste

**tipo:** melhoria  
**impacto:** alto  
**modulo:** dados demo / login / operacao local / super admin

## Resumo executivo

A base demo foi fortalecida para apoiar apresentacao, navegacao e validacao do produto como um MVP em fase de lancamento. O ambiente local agora mostra melhor a operacao da plataforma, com mais contas, sinais comerciais, suporte e billing.

## Entregas realizadas

- ampliacao do seed com contas adicionais para leitura do super admin
- inclusao de uma conta saudavel no plano Growth
- inclusao de uma conta em risco no plano Scale
- criacao de assinaturas demo com dados de billing Asaas
- criacao de chamados de suporte com prioridades e status diferentes
- criacao de eventos demo de webhook de billing processados e com falha
- inclusao de lojas extras vinculadas as novas contas
- melhoria da tela de login com acessos demo de lojista, super admin e cliente
- criacao do comando `php artisan demo:refresh`
- protecao do comando para rodar apenas em ambiente local ou de testes
- cobertura automatizada para garantir que os acessos demo aparecem no login

## Estrategia aplicada

A evolucao foi feita sobre o seed principal para manter a experiencia local simples: ao rodar migrations com seed, o sistema ja fica pronto para demonstracao. O comando `demo:refresh` foi adicionado como atalho controlado para reconstruir a vitrine local quando necessario.

## Resultado

O Mania de Preco passa a abrir com uma massa de dados mais convincente para demonstrar o produto ponta a ponta: lojista, cliente final e super admin conseguem testar seus paineis com contexto realista, sem depender de cadastro manual antes da apresentacao.
