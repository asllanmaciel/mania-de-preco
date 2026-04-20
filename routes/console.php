<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Support\Lancamento\LaunchPreflight;
use Symfony\Component\Console\Command\Command;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('demo:refresh {--force : Executa sem pedir confirmacao}', function (): int {
    if (! app()->environment(['local', 'testing'])) {
        $this->error('O comando demo:refresh so pode ser executado em ambiente local ou de testes.');

        return Command::FAILURE;
    }

    if (! $this->option('force') && ! $this->confirm('Isso recria o banco e carrega a base demo. Deseja continuar?')) {
        $this->warn('Operacao cancelada.');

        return Command::FAILURE;
    }

    $this->call('migrate:fresh', [
        '--seed' => true,
        '--force' => true,
    ]);

    $this->info('Base demo recriada com sucesso.');

    return Command::SUCCESS;
})->purpose('Recria a base local com dados demo de lancamento');

Artisan::command('launch:check {--json : Exibe a analise em JSON}', function (): int {
    $analise = app(LaunchPreflight::class)->analisar();

    if ($this->option('json')) {
        $this->line(json_encode($analise, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $analise['pronto'] ? Command::SUCCESS : Command::FAILURE;
    }

    $this->info('Pre-flight de lancamento');
    $this->line("Status: {$analise['status']['label']}");
    $this->line("Score: {$analise['score']}%");
    $this->line("Bloqueios criticos: {$analise['bloqueios_criticos']}");
    $this->newLine();

    foreach ($analise['grupos'] as $grupo) {
        $this->line("{$grupo['titulo']} ({$grupo['score']}%)");

        foreach ($grupo['itens'] as $item) {
            $status = $item['concluida'] ? 'OK' : ($item['critica'] ? 'CRITICO' : 'PENDENTE');
            $valor = $item['valor'] ? " | {$item['valor']}" : '';

            $this->line("  [{$status}] {$item['titulo']}{$valor}");
        }

        $this->newLine();
    }

    if ($analise['bloqueios_criticos'] > 0) {
        $this->error('Resolva os bloqueios criticos antes de considerar o produto pronto para producao.');

        return Command::FAILURE;
    }

    $this->info('Sem bloqueios criticos. Proximo passo: QA final de jornada e deploy controlado.');

    return Command::SUCCESS;
})->purpose('Executa o pre-flight de lancamento para validar producao, cobranca, produto e operacao');
