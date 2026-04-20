# Painel de roadmap de lançamento

## Contexto
- O roadmap já existia como documentação, mas precisava virar uma tela operacional dentro do super admin.
- A visão de lançamento precisava mostrar o que está pronto, o que bloqueia produção e quais frentes ficam para depois do MVP.
- O painel também precisava facilitar testes e decisões sem depender de leitura manual de arquivos.

## Alterações realizadas
- Criado o painel `super-admin/roadmap` para acompanhar o roadmap vivo de lançamento.
- Criada a classe `App\Support\Lancamento\LaunchRoadmap` para calcular fases, progresso, pendências críticas e próxima ação.
- Adicionadas fases para MVP web, cobrança com Asaas, produção segura, polimento/conversão, app mobile e expansão pós-MVP.
- Incluído o roadmap no menu lateral do super admin, dentro do módulo de governança.
- Adicionado atalho para o roadmap na dashboard do super admin.
- Criado teste de acesso para garantir que o super admin consegue abrir o painel de roadmap.

## Resultado
- O super admin passa a ter uma visão executiva clara do caminho até o lançamento.
- Pendências críticas como Asaas, produção, e-mail transacional e revisão visual ficam visíveis em uma única tela.
- O roadmap deixa de ser apenas documentação e passa a funcionar como painel de acompanhamento do produto.
