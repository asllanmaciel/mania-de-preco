# API mobile v1

Base local: `http://localhost:8000/api/mobile/v1`

## Objetivo
- Servir o app Android/iOS do Mania de Preço com um contrato estável, versionado e separado da API administrativa.
- Permitir que consumidores consultem ofertas, vejam produtos e lojas, criem conta e acompanhem alertas de preço.
- Manter o cadastro de consumidor sem criar uma conta lojista automaticamente.

## Autenticação

### Criar conta de consumidor
`POST /register`

Body:
```json
{
  "name": "Cliente Mobile",
  "email": "cliente@example.com",
  "password": "senha-segura",
  "password_confirmation": "senha-segura",
  "aceite_termos": true
}
```

Resposta:
```json
{
  "token": "1|token-sanctum",
  "user": {
    "id": 1,
    "name": "Cliente Mobile",
    "email": "cliente@example.com",
    "avatar_url": null,
    "perfil": "cliente",
    "alertas_count": 0
  }
}
```

### Login
`POST /login`

Body:
```json
{
  "email": "cliente@example.com",
  "password": "senha-segura"
}
```

### Sessão atual
`GET /me`

Header:
```http
Authorization: Bearer {token}
```

### Logout
`POST /logout`

Header:
```http
Authorization: Bearer {token}
```

## Catálogo público

### Listar ofertas
`GET /ofertas`

Filtros suportados:
- `busca`: busca por nome, descrição ou marca.
- `categoria`: slug da categoria.
- `cidade`: cidade da loja.
- `tipo_preco`: `dinheiro`, `pix`, `boleto`, `cartao` ou `parcelado`.
- `preco_ate`: preço máximo.
- `ordenar`: `menor_preco`, `maior_economia`, `mais_ofertas` ou `alfabetica`.
- `per_page`: entre 6 e 30 itens.

Resposta resumida:
```json
{
  "data": [
    {
      "id": 1,
      "nome": "Café Premium 500g",
      "slug": "cafe-premium-500g",
      "imagem": "http://localhost:8000/images/demo/cafe-premium.jpg",
      "categoria": {
        "id": 1,
        "nome": "Mercearia",
        "slug": "mercearia"
      },
      "marca": {
        "id": 1,
        "nome": "Torra Boa"
      },
      "resumo": {
        "menor_preco": 18.9,
        "maior_preco": 22.5,
        "economia": 3.6,
        "ofertas": 3
      },
      "melhores_ofertas": []
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 12,
    "total": 1
  },
  "filters": {
    "categorias": [],
    "cidades": [],
    "tipos_preco": []
  }
}
```

### Detalhe do produto
`GET /produtos/{produto}`

Retorna produto, galeria, resumo de preços, cidades disponíveis e ofertas ordenadas pelo menor preço.

### Detalhe da loja
`GET /lojas/{loja}`

Retorna dados públicos da loja, contatos, resumo de preços, avaliação média e ofertas em destaque.

## Alertas de preço

Todos os endpoints exigem:
```http
Authorization: Bearer {token}
```

### Listar alertas
`GET /alertas`

### Criar alerta
`POST /alertas`

Body:
```json
{
  "produto_id": 1,
  "preco_desejado": 18.00
}
```

### Atualizar alerta
`PATCH /alertas/{alerta}`

Body:
```json
{
  "preco_desejado": 17.50,
  "status": "ativo"
}
```

Status permitidos no app:
- `ativo`
- `inativo`

### Remover alerta
`DELETE /alertas/{alerta}`

## Analytics
- `mobile.catalog.filtered`: disparado quando o consumidor usa filtros no catálogo.
- `mobile.offers.listed`: disparado na listagem padrão de ofertas.
- `mobile.product.viewed`: disparado no detalhe de produto.
- `mobile.store.viewed`: disparado no detalhe de loja.
- `mobile.customer_registered`: disparado no cadastro mobile.
- `mobile.price_alert.created`: disparado na criação de alerta.
- `mobile.price_alert.updated`: disparado na edição de alerta.
- `mobile.price_alert.deleted`: disparado na remoção de alerta.

## Validação
Com Docker:
```bash
docker compose exec laravel.test php artisan test --filter=MobileApiTest
```
