# Identidade do painel e e-mail de suporte

## Contexto

O painel administrativo estava visualmente distante da identidade pública, ainda com aparência de template azul/roxo. Também faltava uma confirmação transacional para quem abre chamado no suporte público.

## Alterações realizadas

- Alinhamento da paleta do painel lojista com a marca pública do Mania de Preço.
- Alinhamento da paleta do backoffice e super admin com a mesma identidade visual.
- Substituição dos badges textuais "MP" pelo símbolo SVG real da marca no sidebar e topbar responsivo.
- Preservação da estrutura funcional dos painéis, ajustando apenas a direção visual global.
- Criação da notificação `ChamadoSuporteAbertoNotification`.
- Envio de confirmação por e-mail para o cliente ao abrir chamado público.
- Inclusão da confirmação de protocolo por e-mail no farol global de lançamento.
- Cobertura de teste para validar o envio sob demanda da notificação.

## Arquivos impactados

- `resources/views/layouts/admin.blade.php`
- `resources/views/layouts/backoffice.blade.php`
- `app/Notifications/ChamadoSuporteAbertoNotification.php`
- `app/Http/Controllers/Web/PublicTrustController.php`
- `app/Support/Lancamento/PlatformLaunchReadiness.php`
- `tests/Feature/Web/PublicCatalogTest.php`

## Validação planejada

- Executar testes públicos do catálogo.
- Executar testes administrativos.
- Executar a suíte completa.
- Conferir `git diff --check`.
