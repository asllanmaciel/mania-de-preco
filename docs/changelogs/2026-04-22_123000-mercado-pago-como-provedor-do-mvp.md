# Mercado Pago como provedor do MVP

## Contexto
- A decisao de pagamentos mudou: o lancamento do SaaS deve trabalhar com Mercado Pago.
- O sistema ja tinha base operacional em Asaas, mas Mercado Pago estava apenas como alternativa futura.
- Era necessario atualizar roadmap, dossie, pre-flight e painel para mostrar exatamente onde estamos e o que falta para lancar.

## Alteracoes realizadas
- Mercado Pago passou a ser marcado em `config/billing.php` como provedor decidido para o MVP.
- Asaas foi reclassificado como integracao legada/temporaria.
- O roadmap vivo passou a mostrar "Cobranca real com Mercado Pago" como fase principal.
- O pre-flight passou a exigir gateway, webhook e credenciais Mercado Pago.
- A prontidao global de lancamento passou a avaliar Mercado Pago como provedor principal.
- O formulario de assinatura no super admin agora indica Mercado Pago como decisao do MVP, ainda em implementacao.
- `docs/roadmap-lancamento.md` foi reescrito para refletir o estado atual do sistema e app.
- `docs/dossie-sistema-mania-de-preco.md` foi atualizado com a nova estrategia de pagamentos.

## Resultado
- O produto agora deixa claro que Mercado Pago e o caminho de lancamento.
- O sistema tambem deixa claro que a integracao Mercado Pago ainda precisa ser implementada antes de producao.
- O roadmap ficou mais objetivo: antes de lancar, precisamos fechar gateway Mercado Pago, webhook, credenciais, sandbox, producao e QA por perfil.
