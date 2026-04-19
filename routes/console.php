<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
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
