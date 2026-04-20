# Pré-flight operacional de lançamento

## Contexto
- O roadmap mostrava as fases até o lançamento, mas ainda faltava uma validação prática para produção.
- Alguns bloqueios dependem de credenciais externas, domínio, SSL e configuração real de ambiente.
- Era importante conseguir enxergar esses riscos tanto no painel quanto no terminal usado com Docker.

## Alterações realizadas
- Criada a classe `App\Support\Lancamento\LaunchPreflight` para consolidar validações de ambiente, infraestrutura, cobrança, produto e operação.
- Adicionado o comando `php artisan launch:check`.
- O comando retorna falha quando existem bloqueios críticos, permitindo uso antes de deploy ou QA final.
- Adicionado o bloco "Checklist operacional de produção" dentro do painel `super-admin/roadmap`.
- Atualizada a documentação do roadmap com a ferramenta de pré-flight.
- Criado teste automatizado para garantir que o comando reporta bloqueios críticos no ambiente local.

## Resultado
- O lançamento passa a ter uma régua operacional objetiva.
- Fica claro o que ainda depende de configuração real, como `APP_ENV`, `APP_DEBUG`, `APP_URL`, `ASAAS_API_KEY` e `ASAAS_WEBHOOK_TOKEN`.
- O time consegue rodar a checagem no navegador ou no terminal antes de publicar o produto.
