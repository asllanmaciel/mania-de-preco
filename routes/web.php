<?php

use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\FinanceiroController as AdminFinanceiroController;
use App\Http\Controllers\Web\Admin\LojaController as AdminLojaController;
use App\Http\Controllers\Web\Admin\PrecoController as AdminPrecoController;
use App\Http\Controllers\Web\Admin\ProdutoController as AdminProdutoController;
use App\Http\Controllers\Web\Auth\SessionController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware('guest')->group(function () {
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/financeiro', AdminFinanceiroController::class)->name('financeiro.index');
        Route::resource('lojas', AdminLojaController::class)->except(['show']);
        Route::resource('produtos', AdminProdutoController::class)->except(['show']);
        Route::resource('precos', AdminPrecoController::class)->except(['show']);
    });

    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
});
