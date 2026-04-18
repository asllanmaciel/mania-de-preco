<?php

use App\Http\Controllers\AlertaPrecoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvaliacaoLojaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CategoriaFinanceiraController;
use App\Http\Controllers\ContaController;
use App\Http\Controllers\ContaFinanceiraController;
use App\Http\Controllers\ContaPagarController;
use App\Http\Controllers\ContaReceberController;
use App\Http\Controllers\LojaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\MovimentacaoFinanceiraController;
use App\Http\Controllers\PlanoAssinaturaController;
use App\Http\Controllers\PrecoController;
use App\Http\Controllers\ProdutoController;
use Illuminate\Support\Facades\Route;

Route::get('categorias', [CategoriaController::class, 'index']);
Route::get('categorias/{id}', [CategoriaController::class, 'show']);

Route::get('marcas', [MarcaController::class, 'index']);
Route::get('marcas/{id}', [MarcaController::class, 'show']);

Route::get('produtos', [ProdutoController::class, 'index']);
Route::get('produtos/{id}', [ProdutoController::class, 'show']);

Route::get('lojas', [LojaController::class, 'index']);
Route::get('lojas/{id}', [LojaController::class, 'show']);

Route::get('precos', [PrecoController::class, 'index']);
Route::get('precos/{id}', [PrecoController::class, 'show']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

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
