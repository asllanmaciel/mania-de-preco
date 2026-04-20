# API mobile v1 para app cliente

## Contexto
- Preparar a base estrutural para lançar o Mania de Preço também em Android e iOS.
- Separar a experiência mobile do app cliente da API administrativa já existente.
- Evitar que o cadastro de consumidor crie automaticamente uma conta lojista.

## Alterações realizadas
- Registradas rotas versionadas em `/api/mobile/v1` para catálogo, autenticação e alertas.
- Criado fluxo mobile de cadastro, login, sessão atual e logout com Sanctum.
- Criado contrato de catálogo mobile para listar ofertas, ver produto e ver loja com payload pronto para Flutter.
- Criado controller mobile de alertas de preço com listagem, criação, edição e remoção.
- Ajustado catálogo mobile para considerar apenas ofertas de lojas ativas nas métricas públicas.
- Adicionado rastreamento de analytics para ações mobile de catálogo, produto, loja, cadastro e alertas.
- Criado documento `docs/api-mobile-v1.md` com contrato inicial para implementação do app.
- Criada cobertura automatizada para os principais contratos da API mobile.

## Validação planejada
- Rodar `php artisan test --filter=MobileApiTest`.
- Rodar a suíte completa para garantir que a API antiga e as telas web continuam estáveis.
- Rodar `git diff --check` para validar espaços e formatação básica.
