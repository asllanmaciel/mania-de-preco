# Correção do login em branco no Docker local

## Contexto
- A rota `/login` respondia `200`, mas entregava corpo vazio.
- O volume `G:` estava 100% cheio dentro do Docker.
- O Laravel havia gerado views compiladas com `0 bytes` em `storage/framework/views`, causando tela em branco.

## Alterações realizadas
- Ajustado `docker-compose.yml` para usar `VIEW_COMPILED_PATH=/tmp/laravel-views` no container Laravel.
- Ajustado `LOG_CHANNEL=stderr` no container Laravel para evitar escrita de logs no volume local cheio.
- Removidas views compiladas quebradas e recriado o container `laravel.test`.
- Validado que `http://localhost:8000/login` voltou a renderizar o HTML da tela de login.

## Observação operacional
- O drive `G:` segue praticamente sem espaço livre.
- É recomendável liberar alguns GB antes de rodar suíte completa, uploads, seeds pesados ou novas instalações.
