<?php

declare(strict_types=1);

use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\OrderController as AdminOrderController;
use App\Controllers\Admin\PostController as AdminPostController;
use App\Controllers\Admin\ProductController as AdminProductController;
use App\Controllers\Admin\SettingController as AdminSettingController;
use App\Controllers\Admin\UserController as AdminUserController;
use App\Controllers\AuthController;
use App\Controllers\BlogController;
use App\Controllers\CreditController;
use App\Controllers\HomeController;
use App\Controllers\PaymentController;
use App\Controllers\ProfileController;
use App\Controllers\StoreController;
use App\Controllers\SupportController;
use App\Core\Router;

return static function (Router $r): void {
    // ----------------------------------------------------------------- public
    $r->get('/', [HomeController::class, 'index']);

    $r->get('/blog', [BlogController::class, 'index']);
    $r->get('/blog/{slug}', [BlogController::class, 'show']);

    $r->get('/magaza', [StoreController::class, 'index']);
    $r->get('/magaza/{slug}', [StoreController::class, 'category']);
    $r->get('/urun/{slug}', [StoreController::class, 'product']);

    // ------------------------------------------------------------------- auth
    $r->group(['guest'], static function (Router $r): void {
        $r->get('/giris', [AuthController::class, 'showLogin']);
        $r->post('/giris', [AuthController::class, 'login']);
        $r->get('/kayit', [AuthController::class, 'showRegister']);
        $r->post('/kayit', [AuthController::class, 'register']);
    });
    $r->post('/cikis', [AuthController::class, 'logout']);

    // -------------------------------------------------------------- member area
    $r->group(['auth'], static function (Router $r): void {
        $r->get('/profil', [ProfileController::class, 'index']);
        $r->get('/profil/duzenle', [ProfileController::class, 'edit']);
        $r->post('/profil/duzenle', [ProfileController::class, 'update']);
        $r->get('/profil/guvenlik', [ProfileController::class, 'security']);
        $r->post('/profil/guvenlik', [ProfileController::class, 'updatePassword']);
        $r->get('/profil/siparisler', [ProfileController::class, 'orders']);
        $r->get('/profil/kredi-gecmisi', [ProfileController::class, 'credits']);

        $r->get('/kredi-satin-al', [CreditController::class, 'index']);
        $r->post('/kredi-satin-al', [CreditController::class, 'checkout']);

        $r->post('/satin-al/{id}', [StoreController::class, 'buy']);

        $r->get('/destek', [SupportController::class, 'index']);
        $r->post('/destek', [SupportController::class, 'store']);
        $r->get('/destek/{id}', [SupportController::class, 'show']);
        $r->post('/destek/{id}', [SupportController::class, 'reply']);
    });

    // ---------------------------------------------------------------- payments
    $r->get('/odeme/{ref}', [PaymentController::class, 'redirect'], ['auth']);
    $r->post('/odeme/callback', [PaymentController::class, 'callback']);
    $r->get('/odeme/sonuc/{ref}', [PaymentController::class, 'result']);
    // Sandbox completion used when Shopier test mode is on.
    $r->get('/odeme/sandbox/{ref}', [PaymentController::class, 'sandbox'], ['auth']);

    // ------------------------------------------------------------------- admin
    $r->group(['auth', 'admin'], static function (Router $r): void {
        $r->get('/yonetim', [DashboardController::class, 'index']);

        $r->get('/yonetim/urunler', [AdminProductController::class, 'index']);
        $r->get('/yonetim/urunler/yeni', [AdminProductController::class, 'create']);
        $r->post('/yonetim/urunler/yeni', [AdminProductController::class, 'store']);
        $r->get('/yonetim/urunler/{id}', [AdminProductController::class, 'edit']);
        $r->post('/yonetim/urunler/{id}', [AdminProductController::class, 'update']);
        $r->post('/yonetim/urunler/{id}/sil', [AdminProductController::class, 'destroy']);

        $r->get('/yonetim/kategoriler', [AdminProductController::class, 'categories']);
        $r->post('/yonetim/kategoriler', [AdminProductController::class, 'storeCategory']);
        $r->post('/yonetim/kategoriler/{id}/sil', [AdminProductController::class, 'destroyCategory']);

        $r->get('/yonetim/kullanicilar', [AdminUserController::class, 'index']);
        $r->get('/yonetim/kullanicilar/{id}', [AdminUserController::class, 'edit']);
        $r->post('/yonetim/kullanicilar/{id}', [AdminUserController::class, 'update']);
        $r->post('/yonetim/kullanicilar/{id}/kredi', [AdminUserController::class, 'adjustCredit']);

        $r->get('/yonetim/siparisler', [AdminOrderController::class, 'index']);

        $r->get('/yonetim/blog', [AdminPostController::class, 'index']);
        $r->get('/yonetim/blog/yeni', [AdminPostController::class, 'create']);
        $r->post('/yonetim/blog/yeni', [AdminPostController::class, 'store']);
        $r->get('/yonetim/blog/{id}', [AdminPostController::class, 'edit']);
        $r->post('/yonetim/blog/{id}', [AdminPostController::class, 'update']);
        $r->post('/yonetim/blog/{id}/sil', [AdminPostController::class, 'destroy']);

        $r->get('/yonetim/ayarlar', [AdminSettingController::class, 'index']);
        $r->post('/yonetim/ayarlar', [AdminSettingController::class, 'update']);
    });
};
