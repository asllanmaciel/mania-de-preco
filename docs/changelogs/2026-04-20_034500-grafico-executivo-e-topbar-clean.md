# Grafico executivo e topbar clean

## Contexto

O painel precisava ganhar leitura visual mais forte, com graficos mais proximos do padrao de dashboards SaaS modernos. Tambem era necessario reduzir o peso visual dos icones do topbar, que ainda pareciam botoes dentro de caixas com bordas.

## Alteracoes realizadas

- Inclusao de grafico financeiro principal em SVG nativo no dashboard lojista.
- Criacao de visual com linha de saldo, area preenchida, pontos e barras de receitas e despesas.
- Manutencao dos dados reais da serie mensal ja calculada pelo controller.
- Remocao das bordas e caixas pesadas dos icones do topbar no painel lojista.
- Remocao das bordas e caixas pesadas dos icones do topbar no backoffice.
- Inclusao de hover mais leve nos controles do topbar.
- Inclusao de icones contextuais nos cards de plano do dia e saude da conta.

## Arquivos impactados

- `resources/views/admin/dashboard.blade.php`
- `resources/views/layouts/admin.blade.php`
- `resources/views/layouts/backoffice.blade.php`

## Validacao planejada

- Executar testes de acesso administrativo.
- Executar testes operacionais do admin.
- Executar a suite completa.
- Conferir `git diff --check`.
