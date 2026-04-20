# Suporte em cards com detalhe

## Contexto
- A central de suporte estava visualmente pesada porque cada chamado aparecia com mensagem completa e formulário direto na listagem.
- A operação precisa bater o olho na fila, priorizar e clicar para analisar com calma.

## Alterações realizadas
- Transformada a listagem de suporte em cards clicáveis.
- Criada rota de detalhe `/super-admin/suporte/{chamado}`.
- Criada tela de detalhe com mensagem completa, contexto do solicitante, conta vinculada, linha do chamado e formulário de atualização.
- Mantida atualização de status, prioridade e observação interna, agora retornando para o detalhe do chamado.
- Atualizados testes do super admin para cobrir listagem em cards, detalhe e atualização.

## Validação planejada
- Rodar `php artisan test --filter=AdminAccessTest`.
- Validar manualmente a navegação entre `/super-admin/suporte` e o detalhe de um chamado.
