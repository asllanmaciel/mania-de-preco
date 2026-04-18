# Mania de Preco

Backend Laravel de um SaaS para pequenos lojistas controlarem o financeiro, publicarem catálogo e exibirem preços para clientes encontrarem as melhores ofertas.

## Visão do produto

O sistema foi estruturado para unir dois pilares no mesmo núcleo:

- gestão financeira por conta/empresa
- catálogo público e comparação de preços por loja

O cliente pagante é a conta do lojista. Cada conta pode ter usuários, lojas, assinatura e seu próprio módulo financeiro.

## Domínio principal

### Núcleo SaaS

- `contas`
- `conta_user`
- `planos`
- `assinaturas`

### Operação comercial

- `lojas`
- `categorias`
- `marcas`
- `produtos`
- `precos`
- `alertas_precos`
- `avaliacoes_lojas`

### Financeiro

- `categorias_financeiras`
- `contas_financeiras`
- `movimentacoes_financeiras`
- `contas_pagar`
- `contas_receber`

## Fluxo pensado para o produto

1. Usuário se cadastra.
2. O sistema cria automaticamente uma `conta` em período de trial.
3. A conta recebe usuários, lojas e assinatura.
4. A loja publica produtos e preços.
5. A mesma conta controla entradas, saídas, contas a pagar e contas a receber.
6. O consumidor consulta preços públicos enquanto o lojista usa o painel privado.

## Endpoints principais

### Públicos

- `POST /api/register`
- `POST /api/login`
- `GET /api/categorias`
- `GET /api/marcas`
- `GET /api/produtos`
- `GET /api/lojas`
- `GET /api/precos`

### Protegidos

- `GET /api/contas`
- `POST /api/contas`
- `GET /api/contas/{conta}`
- `apiResource /api/contas/{conta}/categorias-financeiras`
- `apiResource /api/contas/{conta}/contas-financeiras`
- `apiResource /api/contas/{conta}/movimentacoes-financeiras`
- `apiResource /api/contas/{conta}/contas-pagar`
- `apiResource /api/contas/{conta}/contas-receber`

## Setup local

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Rodando com Docker

O projeto está preparado para Laravel Sail com:

- PHP 8.3
- MySQL 8
- Redis
- Mailpit

Suba os containers:

```bash
vendor/bin/sail up -d
vendor/bin/sail artisan migrate --seed
```

No PowerShell:

```powershell
vendor\bin\sail up -d
vendor\bin\sail artisan migrate --seed
```

Se o wrapper do Sail falhar no Windows, use os scripts PowerShell do projeto:

```powershell
.\scripts\docker-up.ps1 -Seed
.\scripts\docker-artisan.ps1 route:list
.\scripts\docker-down.ps1
```

URLs úteis em desenvolvimento:

- aplicação: `http://localhost:8000`
- Mailpit: `http://localhost:8025`
- MySQL exposto na máquina: `127.0.0.1:3306`

Para parar:

```bash
vendor/bin/sail down
```

## Testando a API

Você pode testar com Postman, Insomnia ou Bruno usando a coleção em `docs/postman/mania-de-preco.collection.json`.

Fluxo mínimo:

1. `POST /api/register`
2. copiar o `token`
3. enviar `Authorization: Bearer {token}`
4. chamar `GET /api/contas`
5. usar o `id` da conta para criar recursos financeiros

## Próximos passos

- onboarding com seleção da conta ativa
- dashboard financeiro consolidado
- histórico de preço por produto
- gestão de estoque e custo por loja
- políticas/autorização por papel
- testes de API para fluxos críticos

## Documentos

- arquitetura: [docs/arquitetura-saas.md](docs/arquitetura-saas.md)
- changelogs: [docs/changelogs/README.md](docs/changelogs/README.md)
