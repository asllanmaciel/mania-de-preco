<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class HealthCheckController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = [
            'aplicacao' => $this->checkAplicacao(),
            'banco' => $this->checkBanco(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
        ];

        $saudavel = collect($checks)->every(fn (array $check) => $check['ok']);

        return response()->json([
            'status' => $saudavel ? 'ok' : 'degradado',
            'verificado_em' => now()->toIso8601String(),
            'checks' => $checks,
        ], $saudavel ? 200 : 503, [
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    private function checkAplicacao(): array
    {
        return $this->resultado(filled(config('app.key')), 'Chave da aplicacao configurada.');
    }

    private function checkBanco(): array
    {
        try {
            DB::selectOne('select 1 as ok');

            return $this->resultado(true, 'Conexao com banco operacional.');
        } catch (Throwable) {
            return $this->resultado(false, 'Falha ao consultar o banco.');
        }
    }

    private function checkCache(): array
    {
        try {
            $key = 'health:' . sha1((string) now()->timestamp);

            Cache::put($key, 'ok', now()->addMinute());
            $ok = Cache::get($key) === 'ok';
            Cache::forget($key);

            return $this->resultado($ok, $ok ? 'Cache operacional.' : 'Cache nao confirmou escrita e leitura.');
        } catch (Throwable) {
            return $this->resultado(false, 'Falha ao validar cache.');
        }
    }

    private function checkStorage(): array
    {
        $paths = [
            storage_path('framework'),
            storage_path('logs'),
        ];

        $ok = collect($paths)->every(fn (string $path) => is_dir($path) && is_writable($path));

        return $this->resultado($ok, $ok ? 'Diretorios de storage gravaveis.' : 'Storage sem permissao adequada.');
    }

    private function resultado(bool $ok, string $mensagem): array
    {
        return [
            'ok' => $ok,
            'mensagem' => $mensagem,
        ];
    }
}
