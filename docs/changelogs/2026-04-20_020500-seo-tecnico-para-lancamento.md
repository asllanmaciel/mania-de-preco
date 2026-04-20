# SEO técnico para lançamento

## Contexto

Para lançar o produto com mais confiança, as páginas públicas precisam estar preparadas para descoberta por buscadores sem expor áreas privadas como admin, super admin, cliente, login e endpoints técnicos.

## Alterações realizadas

- Criação do controller público de SEO para responder `robots.txt` e `sitemap.xml`.
- Remoção do `robots.txt` estático para permitir resposta dinâmica baseada nas rotas da aplicação.
- Inclusão de bloqueios de indexação para áreas privadas e rotas internas.
- Inclusão do `sitemap.xml` dinâmico com home, ofertas, páginas institucionais, novidades, lojas ativas com ofertas e produtos ativos com preços publicados.
- Conexão de `robots` e `sitemap` ao farol de prontidão global do super admin.
- Cobertura de testes para validar robots, sitemap e presença das URLs públicas principais.

## Arquivos impactados

- `app/Http/Controllers/Web/PublicSeoController.php`
- `resources/views/seo/sitemap.blade.php`
- `routes/web.php`
- `app/Support/Lancamento/PlatformLaunchReadiness.php`
- `tests/Feature/Web/PublicCatalogTest.php`
- `public/robots.txt`

## Validação planejada

- Executar testes públicos do catálogo.
- Executar a suíte completa.
- Conferir `git diff --check`.
