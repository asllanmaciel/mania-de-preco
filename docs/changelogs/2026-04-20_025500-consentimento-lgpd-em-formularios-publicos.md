# Consentimento LGPD em formularios publicos

## Contexto

O cadastro de consumidor e a abertura de chamados publicos precisavam deixar o aceite de Termos de Uso e Politica de Privacidade explicito antes do lancamento. Alem da validacao visual, era importante registrar dados minimos de auditoria para reduzir risco operacional.

## Alteracoes realizadas

- Inclusao de checkbox obrigatorio de aceite no cadastro de consumidor.
- Inclusao de checkbox obrigatorio de aceite no formulario publico de suporte.
- Criacao de versoes configuraveis para Termos de Uso e Politica de Privacidade em `config/legal.php`.
- Registro de data, versao dos documentos, IP e user agent no cadastro de usuarios.
- Ocultacao de IP e user agent de consentimento nas respostas serializadas do usuario.
- Registro de data e versoes legais nos chamados publicos de suporte.
- Aplicacao da mesma exigencia de aceite no cadastro via API.
- Inclusao do consentimento legal no farol global de lancamento.
- Cobertura de testes para cadastro, suporte publico, bloqueio sem aceite e persistencia dos dados legais.

## Arquivos impactados

- `config/legal.php`
- `database/migrations/2026_04_20_025500_add_legal_consent_fields.php`
- `app/Models/User.php`
- `app/Models/ChamadoSuporte.php`
- `app/Http/Controllers/Web/Auth/RegisteredUserController.php`
- `app/Http/Controllers/Web/PublicTrustController.php`
- `app/Http/Controllers/AuthController.php`
- `app/Support/Lancamento/PlatformLaunchReadiness.php`
- `resources/views/layouts/auth.blade.php`
- `resources/views/layouts/institucional.blade.php`
- `resources/views/auth/register.blade.php`
- `resources/views/institucional/suporte.blade.php`
- `tests/Feature/Web/ClienteExperienceTest.php`
- `tests/Feature/Web/PublicCatalogTest.php`

## Validacao planejada

- Executar testes da experiencia do cliente.
- Executar testes publicos do catalogo e suporte.
- Executar a suite completa.
- Conferir `git diff --check`.
