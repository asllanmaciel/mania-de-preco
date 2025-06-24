<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\LojaController;
use App\Http\Controllers\PrecoController;
use App\Http\Controllers\AvaliacaoLojaController;
use App\Http\Controllers\AlertaPrecoController;
use App\Http\Controllers\PlanoAssinaturaController;

use App\Http\Controllers\AuthController;

// ✅ Rotas públicas (consulta)
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

// ✅ Rotas protegidas (CRUD completo)
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('categorias', CategoriaController::class)->except(['index', 'show']);
    Route::apiResource('marcas', MarcaController::class)->except(['index', 'show']);
    Route::apiResource('produtos', ProdutoController::class)->except(['index', 'show']);
    Route::apiResource('lojas', LojaController::class)->except(['index', 'show']);
    Route::apiResource('precos', PrecoController::class)->except(['index', 'show']);

    Route::apiResource('avaliacoes', AvaliacaoLojaController::class);
    Route::apiResource('alertas', AlertaPrecoController::class);
    Route::apiResource('planos', PlanoAssinaturaController::class);
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user',     [AuthController::class, 'user']);
});
