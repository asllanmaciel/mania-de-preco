<?php

use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\AuditoriaController as AdminAuditoriaController;
use App\Http\Controllers\Web\Admin\AssinaturaController as AdminAssinaturaController;
use App\Http\Controllers\Web\Admin\Financeiro\CategoriaFinanceiraController as AdminCategoriaFinanceiraController;
use App\Http\Controllers\Web\Admin\Financeiro\ContaPagarController as AdminContaPagarController;
use App\Http\Controllers\Web\Admin\Financeiro\ContaReceberController as AdminContaReceberController;
use App\Http\Controllers\Web\Admin\FinanceiroController as AdminFinanceiroController;
use App\Http\Controllers\Web\Admin\Financeiro\ContaFinanceiraController as AdminContaFinanceiraController;
use App\Http\Controllers\Web\Admin\Financeiro\MovimentacaoFinanceiraController as AdminMovimentacaoFinanceiraController;
use App\Http\Controllers\Web\Admin\EquipeController as AdminEquipeController;
use App\Http\Controllers\Web\Admin\LancamentoController as AdminLancamentoController;
use App\Http\Controllers\Web\Admin\LojaController as AdminLojaController;
use App\Http\Controllers\Web\Admin\NotificacaoController as AdminNotificacaoController;
use App\Http\Controllers\Web\Admin\OnboardingController;
use App\Http\Controllers\Web\Admin\PrecoController as AdminPrecoController;
use App\Http\Controllers\Web\Admin\ProdutoController as AdminProdutoController;
use App\Http\Controllers\Web\Admin\ConfiguracaoContaController as AdminConfiguracaoContaController;
use App\Http\Controllers\Web\Admin\PerfilController as AdminPerfilController;
use App\Http\Controllers\Web\Auth\NewPasswordController;
use App\Http\Controllers\Web\Auth\PasswordResetLinkController;
use App\Http\Controllers\Web\Auth\RegisteredUserController;
use App\Http\Controllers\Web\Auth\SessionController;
use App\Http\Controllers\Web\Cliente\AlertaPrecoController as ClienteAlertaPrecoController;
use App\Http\Controllers\Web\Cliente\DashboardController as ClienteDashboardController;
use App\Http\Controllers\Web\Cliente\NotificacaoController as ClienteNotificacaoController;
use App\Http\Controllers\Web\HealthCheckController;
use App\Http\Controllers\Web\PanelRedirectController;
use App\Http\Controllers\Web\PublicCatalogController;
use App\Http\Controllers\Web\PublicProjectController;
use App\Http\Controllers\Web\PublicProductController;
use App\Http\Controllers\Web\PublicSeoController;
use App\Http\Controllers\Web\PublicStoreController;
use App\Http\Controllers\Web\PublicTrustController;
use App\Http\Controllers\Web\PublicUpdatesController;
use App\Http\Controllers\Web\SuperAdmin\ContaAssinaturaController as SuperAdminContaAssinaturaController;
use App\Http\Controllers\Web\SuperAdmin\AssinaturaBillingController as SuperAdminAssinaturaBillingController;
use App\Http\Controllers\Web\SuperAdmin\AnalyticsController as SuperAdminAnalyticsController;
use App\Http\Controllers\Web\SuperAdmin\ChamadoSuporteController as SuperAdminChamadoSuporteController;
use App\Http\Controllers\Web\SuperAdmin\ContaController as SuperAdminContaController;
use App\Http\Controllers\Web\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\Web\SuperAdmin\PlanoController as SuperAdminPlanoController;
use Illuminate\Support\Facades\Route;

Route::get('/', PublicCatalogController::class)->name('home');
Route::get('/ofertas', PublicCatalogController::class)->name('ofertas');
Route::get('/radar-precos', [PublicCatalogController::class, 'radar'])->middleware('throttle:60,1')->name('radar.precos');
Route::get('/robots.txt', [PublicSeoController::class, 'robots'])->name('seo.robots');
Route::get('/sitemap.xml', [PublicSeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/health', HealthCheckController::class)->name('health');
Route::get('/projeto', PublicProjectController::class)->name('projeto');
Route::get('/novidades', [PublicUpdatesController::class, 'index'])->name('novidades.index');
Route::get('/novidades/{slug}', [PublicUpdatesController::class, 'show'])->name('novidades.show');
Route::get('/termos-de-uso', [PublicTrustController::class, 'termos'])->name('termos');
Route::get('/privacidade', [PublicTrustController::class, 'privacidade'])->name('privacidade');
Route::get('/suporte', [PublicTrustController::class, 'suporte'])->name('suporte');
Route::post('/suporte', [PublicTrustController::class, 'abrirChamado'])->middleware('throttle:5,1')->name('suporte.chamados.store');
Route::get('/lojas/{loja}', PublicStoreController::class)->name('lojas.public.show');
Route::get('/produtos/{produto}', PublicProductController::class)->name('produtos.public.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
    Route::get('/cadastro', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/cadastro', [RegisteredUserController::class, 'store'])->middleware('throttle:5,1')->name('register.store');
    Route::get('/esqueci-minha-senha', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/esqueci-minha-senha', [PasswordResetLinkController::class, 'store'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/resetar-senha/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/resetar-senha', [NewPasswordController::class, 'store'])->middleware('throttle:6,1')->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/painel', PanelRedirectController::class)->name('painel.redirect');

    Route::prefix('super-admin')->name('super-admin.')->middleware('panel:super-admin')->group(function () {
        Route::get('/', SuperAdminDashboardController::class)->name('dashboard');
        Route::get('analytics', SuperAdminAnalyticsController::class)->name('analytics');
        Route::resource('contas', SuperAdminContaController::class)->only(['index', 'show']);
        Route::resource('planos', SuperAdminPlanoController::class)->except(['show', 'destroy']);
        Route::get('suporte', [SuperAdminChamadoSuporteController::class, 'index'])->name('suporte.index');
        Route::get('suporte/{chamado}', [SuperAdminChamadoSuporteController::class, 'show'])->name('suporte.show');
        Route::patch('suporte/{chamado}', [SuperAdminChamadoSuporteController::class, 'update'])->name('suporte.update');
        Route::get('contas/{conta}/assinaturas/nova', [SuperAdminContaAssinaturaController::class, 'create'])
            ->name('contas.assinaturas.create');
        Route::post('contas/{conta}/assinaturas', [SuperAdminContaAssinaturaController::class, 'store'])
            ->name('contas.assinaturas.store');
        Route::get('contas/{conta}/assinaturas/{assinatura}/editar', [SuperAdminContaAssinaturaController::class, 'edit'])
            ->name('contas.assinaturas.edit');
        Route::put('contas/{conta}/assinaturas/{assinatura}', [SuperAdminContaAssinaturaController::class, 'update'])
            ->name('contas.assinaturas.update');
        Route::post('contas/{conta}/assinaturas/{assinatura}/sincronizar', SuperAdminAssinaturaBillingController::class)
            ->name('assinaturas.billing.sync');
    });

    Route::prefix('admin')->name('admin.')->middleware('panel:admin')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/lancamento', AdminLancamentoController::class)->name('lancamento');
        Route::get('/perfil', [AdminPerfilController::class, 'edit'])->name('perfil.edit');
        Route::put('/perfil', [AdminPerfilController::class, 'update'])->name('perfil.update');
        Route::put('/perfil/senha', [AdminPerfilController::class, 'updatePassword'])->name('perfil.password');
        Route::get('/notificacoes', AdminNotificacaoController::class)->name('notificacoes');
        Route::patch('/notificacoes/interacao', [AdminNotificacaoController::class, 'interagir'])->name('notificacoes.interagir');
        Route::get('/onboarding', OnboardingController::class)->name('onboarding')->middleware('conta.can:onboarding');
        Route::get('/auditoria', AdminAuditoriaController::class)->name('auditoria')->middleware('conta.can:equipe');
        Route::get('/assinatura', AdminAssinaturaController::class)
            ->name('assinatura')
            ->middleware('conta.can:gestao');
        Route::get('/configuracoes', [AdminConfiguracaoContaController::class, 'edit'])
            ->name('configuracoes.edit')
            ->middleware('conta.can:gestao');
        Route::put('/configuracoes', [AdminConfiguracaoContaController::class, 'update'])
            ->name('configuracoes.update')
            ->middleware('conta.can:gestao');
        Route::resource('equipe', AdminEquipeController::class)
            ->except(['show', 'destroy'])
            ->middleware('conta.can:equipe');
        Route::prefix('financeiro')->name('financeiro.')->middleware('conta.can:financeiro')->group(function () {
            Route::get('/', AdminFinanceiroController::class)->name('index');
            Route::resource('categorias', AdminCategoriaFinanceiraController::class)->except(['show']);
            Route::resource('contas', AdminContaFinanceiraController::class)->except(['show']);
            Route::resource('lancamentos', AdminMovimentacaoFinanceiraController::class)->except(['show']);
            Route::resource('contas-pagar', AdminContaPagarController::class)->except(['show']);
            Route::resource('contas-receber', AdminContaReceberController::class)->except(['show']);
        });
        Route::resource('lojas', AdminLojaController::class)->except(['show'])->middleware('conta.can:lojas');
        Route::resource('produtos', AdminProdutoController::class)->except(['show'])->middleware('conta.can:catalogo');
        Route::resource('precos', AdminPrecoController::class)->except(['show'])->middleware('conta.can:precos');
    });

    Route::prefix('cliente')->name('cliente.')->middleware('panel:cliente')->group(function () {
        Route::get('/', ClienteDashboardController::class)->name('dashboard');
        Route::get('/notificacoes', ClienteNotificacaoController::class)->name('notificacoes');
        Route::patch('/notificacoes/interacao', [ClienteNotificacaoController::class, 'interagir'])->name('notificacoes.interagir');
        Route::post('/alertas', [ClienteAlertaPrecoController::class, 'store'])->name('alertas.store');
        Route::patch('/alertas/{alerta}', [ClienteAlertaPrecoController::class, 'update'])->name('alertas.update');
        Route::delete('/alertas/{alerta}', [ClienteAlertaPrecoController::class, 'destroy'])->name('alertas.destroy');
    });

    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
});
