# Roadmap de lançamento e decisão de pagamentos

## Contexto
- O lançamento precisa manter uma ordem clara para não misturar polimento visual, produção, app e pagamentos sem prioridade.
- A decisão entre Asaas e Mercado Pago precisava ficar explícita no roadmap.
- O produto já possui base técnica inicial para Asaas, enquanto Mercado Pago ainda não possui gateway operacional.

## Alterações realizadas
- Criado `docs/roadmap-lancamento.md` como roadmap vivo do MVP.
- Definido Asaas como provedor principal de cobrança para o lançamento.
- Registrado Mercado Pago como alternativa planejada pós-MVP.
- Adicionado Mercado Pago em `config/billing.php` como provedor mapeado no roadmap, sem ativá-lo operacionalmente.
- Ajustada a criação/edição de assinatura no super admin para orientar o uso de Asaas como provedor recomendado.
- Incluído o item "Provedor de cobrança definido" no checklist global de prontidão de lançamento.

## Resultado
- O time passa a ter um roteiro único para lançamento web, cobrança, polimento visual e app mobile.
- A cobrança do MVP fica menos ambígua: validar Asaas primeiro, evoluir Mercado Pago depois.
- O super admin evita cadastrar provedores livres que ainda não existem no backend.
