<?php

use App\Http\Controllers\AlertaPrecoController;
use App\Http\Controllers\Api\Mobile\AlertaController as MobileAlertaController;
use App\Http\Controllers\Api\Mobile\AuthController as MobileAuthController;
use App\Http\Controllers\Api\Mobile\CatalogController as MobileCatalogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvaliacaoLojaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CategoriaFinanceiraController;
use App\Http\Controllers\ContaController;
use App\Http\Controllers\ContaFinanceiraController;
use App\Http\Controllers\ContaPagarController;
use App\Http\Controllers\ContaReceberController;
use App\Http\Controllers\HistoricoPrecoController;
use App\Http\Controllers\LojaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\MovimentacaoFinanceiraController;
use App\Http\Controllers\PlanoAssinaturaController;
use App\Http\Controllers\PrecoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\Web\Webhooks\AsaasWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('categorias', [CategoriaController::class, 'index']);
Route::get('categorias/{id}', [CategoriaController::class, 'show']);

Route::get('marcas', [MarcaController::class, 'index']);
Route::get('marcas/{id}', [MarcaController::class, 'show']);

Route::get('produtos', [ProdutoController::class, 'index']);
Route::get('produtos/{id}', [ProdutoController::class, 'show']);
Route::get('produtos/{id}/historico-precos', [HistoricoPrecoController::class, 'produto']);

Route::get('lojas', [LojaController::class, 'index']);
Route::get('lojas/{id}', [LojaController::class, 'show']);

Route::get('precos', [PrecoController::class, 'index']);
Route::get('precos/{id}', [PrecoController::class, 'show']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('billing/webhooks/asaas', AsaasWebhookController::class)->name('billing.webhooks.asaas');

Route::prefix('mobile/v1')->name('api.mobile.')->group(function () {
    Route::get('ofertas', [MobileCatalogController::class, 'ofertas'])->name('ofertas.index');
    Route::get('produtos/{produto}', [MobileCatalogController::class, 'produto'])->name('produtos.show');
    Route::get('lojas/{loja}', [MobileCatalogController::class, 'loja'])->name('lojas.show');

    Route::post('register', [MobileAuthController::class, 'register'])->middleware('throttle:5,1')->name('register');
    Route::post('login', [MobileAuthController::class, 'login'])->middleware('throttle:6,1')->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [MobileAuthController::class, 'me'])->name('me');
        Route::post('logout', [MobileAuthController::class, 'logout'])->name('logout');

        Route::get('alertas', [MobileAlertaController::class, 'index'])->name('alertas.index');
        Route::post('alertas', [MobileAlertaController::class, 'store'])->name('alertas.store');
        Route::patch('alertas/{alerta}', [MobileAlertaController::class, 'update'])->name('alertas.update');
        Route::delete('alertas/{alerta}', [MobileAlertaController::class, 'destroy'])->name('alertas.destroy');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('contas', ContaController::class)->only(['index', 'store', 'show']);
    Route::apiResource('contas.categorias-financeiras', CategoriaFinanceiraController::class);
    Route::apiResource('contas.contas-financeiras', ContaFinanceiraController::class);
    Route::apiResource('contas.movimentacoes-financeiras', MovimentacaoFinanceiraController::class);
    Route::apiResource('contas.contas-pagar', ContaPagarController::class);
    Route::apiResource('contas.contas-receber', ContaReceberController::class);

    Route::apiResource('categorias', CategoriaController::class)->except(['index', 'show']);
    Route::apiResource('marcas', MarcaController::class)->except(['index', 'show']);
    Route::apiResource('produtos', ProdutoController::class)->except(['index', 'show']);
    Route::apiResource('lojas', LojaController::class)->except(['index', 'show']);
    Route::apiResource('precos', PrecoController::class)->except(['index', 'show']);
    Route::apiResource('avaliacoes', AvaliacaoLojaController::class);
    Route::apiResource('alertas', AlertaPrecoController::class);
    Route::apiResource('planos', PlanoAssinaturaController::class);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});
