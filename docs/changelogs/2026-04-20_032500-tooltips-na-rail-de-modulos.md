# Tooltips na rail de modulos

## Contexto

A navegacao inspirada no Matdash deixou a coluna esquerda mais limpa, mas os icones isolados ainda podiam exigir descoberta por tentativa. Era importante manter os icones como seletores de modulo sem transformar a rail em atalhos diretos, mas oferecendo uma leitura rapida do que cada modulo representa.

## Alteracoes realizadas

- Inclusao de tooltip textual nos icones da rail do painel lojista.
- Inclusao de tooltip textual nos icones da rail do backoffice e area do cliente.
- Suporte a exibicao por hover e foco de teclado.
- Preservacao da semantica de botoes de modulo, sem navegar diretamente ao clicar no icone.
- Manutencao da experiencia mobile sem tooltips, com modulos expandidos como lista.

## Arquivos impactados

- `resources/views/layouts/admin.blade.php`
- `resources/views/layouts/backoffice.blade.php`

## Validacao planejada

- Executar testes de acesso administrativo.
- Executar testes da experiencia do cliente.
- Conferir `git diff --check`.
