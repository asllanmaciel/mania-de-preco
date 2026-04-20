# Central de analytics no super admin

## Contexto
- Dar ao super admin uma visão dedicada para acompanhar tração real do produto.
- Transformar os eventos capturados pela plataforma em leitura executiva para lançamento.
- Facilitar decisões sobre aquisição, app mobile, alertas e vitrines mais relevantes.

## Alterações realizadas
- Criada rota `/super-admin/analytics`.
- Criado controller dedicado para consolidar eventos por período, canal, funil, ranking e linha do tempo.
- Criada tela visual de analytics com métricas, gráfico diário, distribuição por canal, funil de conversão, eventos mais fortes, produtos e lojas com mais interesse.
- Adicionado item “Analytics” no módulo de Governança do menu do super admin.
- Adicionado atalho para analytics no dashboard do super admin.

## Validação planejada
- Rodar teste focado do super admin.
- Rodar a suíte completa.
- Rodar `git diff --check`.
