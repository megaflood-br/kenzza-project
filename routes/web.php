<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductSheetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketReplyController;
use App\Http\Controllers\MediaKitController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Ecommerce\CartController;
use App\Http\Controllers\Ecommerce\CheckoutController;
use App\Http\Controllers\Ecommerce\CustomerController;
use App\Http\Controllers\PriceTableController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\OrderShippingController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Ecommerce\SitemapController;


/*
|--------------------------------------------------------------------------
| Public Routes (Acessíveis por qualquer visitante)
|--------------------------------------------------------------------------
*/
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('shop.sitemap');
Route::get('/', [ProductController::class, 'home'])->name('shop.home');
Route::get('/produtos/{slug?}', [ProductController::class, 'shopIndex'])->name('shop.index');
Route::get('/produto/{product}', [ProductController::class, 'showShop'])->name('shop.product');
Route::get('/calcular-frete', [ProductController::class, 'calcularFrete'])->name('product.frete');
Route::post('/webhook/melhor-envio', [WebhookController::class, 'handleMelhorEnvio']);
Route::post('/webhook/asaas', [WebhookController::class, 'handle']);
Route::redirect('/zap', 'https://wa.me/5511971753231');


// Carrinho de Compras
Route::post('/carrinho/cupom', [CartController::class, 'applyCoupon'])->name('cart.coupon');
Route::patch('/carrinho/atualizar/{id}', [CartController::class, 'update'])->name('cart.update');
Route::prefix('carrinho')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
});

// Rota AJAX para Login Inline de dentro do Carrinho (One-Page Checkout)
Route::post('/checkout/login-ajax', [CheckoutController::class, 'loginAjax'])->name('checkout.login.ajax');

// Rotas institucionais da empresa
Route::get('/a-empresa', function () { return view('ecommerce.about'); })->name('shop.about');
Route::get('/fidelidade-e-parcerias', function () { return view('ecommerce.membership'); })->name('shop.membership');

// Tabelas de Preço e Institucionais
Route::get('/fichas-tecnicas/{id}', [ProductSheetController::class, 'showPublic'])->name('sheets.show.public');
Route::get('/tabela-distribuidor', [PriceTableController::class, 'publicTable'])->name('prices.public');
// --- ADICIONE ESTA LINHA ABAIXO ---
Route::get('/tabela-representante', [PriceTableController::class, 'representativeTable'])->name('prices.representative');
Route::get('/tabela-salao', [PriceTableController::class, 'salonTable'])->name('prices.salon');
Route::get('/politicas-de-envio', function () { return view('ecommerce.shipping-policy'); })->name('shop.shipping');
Route::get('/termos-e-condicoes', function () { return view('ecommerce.terms'); })->name('shop.terms');
Route::get('/lgpd', function () { return view('ecommerce.lgpd'); })->name('shop.lgpd');
Route::get('/politica-devolucao', function () {
    // Se o arquivo estiver em resources/views/return-policy.blade.php
    return view('ecommerce.return-policy');
})->name('politica-devolucao');

// Login Customizado para Distribuidores
Route::get('/distribuidor/login', function () { return view('auth.login'); })->name('distribuidor.login');

// Leads, Links e Webhooks
Route::post('/webhook/infinitepay', [WebhookController::class, 'handle']);
Route::get('/seja-um-distribuidor', function () { return view('distribuidor'); });
Route::get('/links', function () { return view('links'); });
Route::post('/enviar-lead', [LeadController::class, 'store'])->name('leads.store');
Route::get('/manutencao-site', function () { return view('manutencao'); })->name('manutencao');

/*
|--------------------------------------------------------------------------
| Private Routes (Requer Autenticação e E-mail Verificado)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // ==========================================
    // MÓDULO COMERCIAL / CRM (Admin & Manager)
    // ==========================================
    Route::prefix('admin/crm')->name('crm.')->group(function () {
        Route::get('/distribuidores', [\App\Http\Controllers\Admin\CrmController::class, 'index'])->name('distributors');
        Route::get('/pedidos/novo', [\App\Http\Controllers\Admin\CrmController::class, 'createOrder'])->name('orders.create');
        Route::post('/pedidos/novo', [\App\Http\Controllers\Admin\CrmController::class, 'storeOrder'])->name('orders.store');
    });

    Route::post('/notificacoes/toggle', [NotificationController::class, 'togglePush'])->name('admin.notifications.toggle');

    // Perfil do Usuário Logado
    $profileCtrl = ProfileController::class;
    Route::get('/profile', [$profileCtrl, 'edit'])->name('profile.edit');
    Route::patch('/profile', [$profileCtrl, 'update'])->name('profile.update');
    Route::delete('/profile', [$profileCtrl, 'destroy'])->name('profile.destroy');

    Route::get('/complete-profile', [$profileCtrl, 'complete'])->name('profile.complete');
    Route::post('/complete-profile', [$profileCtrl, 'updateComplete'])->name('profile.update-complete');

    // Rota AJAX para Salvar Endereço (Acessível antes de passar pelo profile.complete)
    Route::post('/checkout/endereco-ajax', [CheckoutController::class, 'salvarEnderecoAjax'])->name('checkout.endereco-ajax');

    // --- CORREÇÃO: MUDANÇA DE LOCAL DAS ROTAS DE CHECKOUT ---
    // Retiradas do middleware profile.complete para evitar loops de sessão e quedas de redirecionamento no Asaas
    Route::post('/checkout/pagar', [CheckoutController::class, 'checkout'])->name('checkout.pagar');
    Route::match(['get', 'post'], '/checkout/pagamento', [CheckoutController::class, 'paymentView'])->name('checkout.payment');
    Route::post('/checkout/processar/{method}', [CheckoutController::class, 'processPayment'])->name('checkout.process');
    Route::post('/checkout/pix-confirmar', [CheckoutController::class, 'confirmPix'])->name('checkout.pix.confirm');
    Route::get('/obrigado', function() { return view('ecommerce.obrigado'); })->name('shop.obrigado');

    // --- ÁREA RESTRITA POR PERFIL COMPLETO (Suporte, Painéis e Adm) ---
    Route::middleware(['profile.complete'])->group(function () {

        // Suporte / Tickets
        Route::resource('tickets', TicketController::class);
        Route::post('tickets/{ticket}/replies', [TicketReplyController::class, 'store'])->name('tickets.replies.store');

        // Rota Inteligente de Redirecionamento de Painel CORRIGIDA para o perfil 'manager'
        Route::get('/painel-redirecionar', function () {
    $user = auth()->user();

    if (in_array($user->role, ['admin', 'manager', 'editor'])) {
        return redirect()->route('dashboard');
    } elseif ($user->role === 'representative') {
        return redirect()->route('dashboard'); // O representante usa o mesmo dashboard da Sandy
    } elseif ($user->role === 'distributor') {
        return redirect()->route('distributor.panel');
    }

    return redirect()->route('customer.panel');
})->name('painel.redirect');

        // --- ÁREA DO CLIENTE (CONSUMIDOR E SALÃO) ---
        Route::middleware(['role:consumer,salon'])->prefix('minha-conta')->group(function () {
            $customerController = CustomerController::class;
            Route::get('/painel', [$customerController, 'index'])->name('customer.panel');
            Route::get('/pedidos', [$customerController, 'orders'])->name('customer.orders');
            Route::get('/pedidos/{order}', [$customerController, 'showOrder'])->name('customer.orders.show');
        });

        // --- ÁREA DO PARCEIRO (DISTRIBUIDOR E REPRESENTANTE) ---
        Route::middleware(['role:distributor,representative'])->prefix('parceiro')->group(function () {
            Route::get('/painel', function () { return view('distributor.panel'); })->name('distributor.panel');
            Route::get('/tabela-precos', [PriceTableController::class, 'index'])->name('prices.index');
            Route::get('/pedidos', [OrderController::class, 'index'])->name('orders.index');
            Route::get('/pedidos/novo', [OrderController::class, 'create'])->name('orders.create');
            Route::post('/pedidos/revisar', [OrderController::class, 'reviewOrder'])->name('orders.review');
            Route::post('/pedidos/confirmar', [OrderController::class, 'storeOrder'])->name('orders.storeOrder');
            Route::get('/fichas', [ProductSheetController::class, 'index'])->name('sheets.index');
            Route::get('/fichas/{id}', [ProductSheetController::class, 'show'])->name('sheets.show.distributor');
            Route::get('/kits-divulgacao', [MediaKitController::class, 'index'])->name('media.index');
        });

        // --- ÁREA ADMINISTRATIVA CORRIGIDA (Agora inclui a role 'manager') ---
        Route::middleware(['role:admin,manager,editor,representative'])->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/logs', [AuditLogController::class, 'index'])->name('admin.logs.index');
            Route::get('/logs/{id}', [AuditLogController::class, 'show'])->name('admin.logs.show');

            Route::prefix('admin')->group(function () {
                Route::resource('sheets', ProductSheetController::class)->names([
                    'index' => 'sheets.index.admin', 'create' => 'sheets.create', 'store' => 'sheets.store',
                    'show' => 'sheets.show', 'edit' => 'sheets.edit', 'update' => 'sheets.update', 'destroy' => 'sheets.destroy',
                ]);

                Route::resource('products', ProductController::class)->scoped(['product' => 'id'])->names('products');
                Route::resource('categories', CategoryController::class)->names('categories');
                Route::resource('banners', BannerController::class)->names('banners');
                Route::resource('users', UserController::class);

                Route::get('/midia', [MediaKitController::class, 'index'])->name('media.admin');

                // Pedidos
                Route::get('/pedidos', [OrderController::class, 'adminIndex'])->name('admin.orders.index');
                Route::patch('/pedidos/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');

                // Etiquetas de envio
                Route::post('/pedidos/{id}/etiqueta', [OrderShippingController::class, 'emitirEtiqueta'])->name('admin.etiqueta.emitir');

                Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
                Route::post('/leads/{lead}/promote', [LeadController::class, 'promote'])->name('leads.promote');
                Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
                Route::get('/configuracoes/margens', [SettingController::class, 'index'])->name('admin.settings.margins');
                Route::post('/configuracoes/margens', [SettingController::class, 'updateMargins'])->name('admin.settings.margins.update');
                Route::get('/notificacoes', [NotificationController::class, 'index'])->name('admin.notifications.create');
                Route::post('/notificacoes/enviar', [NotificationController::class, 'send'])->name('admin.notifications.send');

                // Cupons de Desconto
                Route::get('/cupons', [App\Http\Controllers\Admin\CouponController::class, 'index'])->name('coupons.index');
                Route::post('/cupons', [App\Http\Controllers\Admin\CouponController::class, 'store'])->name('coupons.store');
                Route::put('/cupons/{coupon}', [App\Http\Controllers\Admin\CouponController::class, 'update'])->name('coupons.update');
                Route::delete('/cupons/{coupon}', [App\Http\Controllers\Admin\CouponController::class, 'destroy'])->name('coupons.destroy');
                Route::patch('/cupons/{coupon}/toggle', [App\Http\Controllers\Admin\CouponController::class, 'toggle'])->name('coupons.toggle');
            });
        });
    });
});

// Importante: as rotas do auth.php contêm a lógica de exibição do aviso de verificação
require __DIR__.'/auth.php';
