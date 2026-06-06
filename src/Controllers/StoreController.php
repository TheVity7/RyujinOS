<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Flash;
use App\Core\Request;
use App\Models\Category;
use App\Models\CreditTransaction;
use App\Models\Order;
use App\Models\Product;
use App\Services\RconService;

final class StoreController extends Controller
{
    public function index(): string
    {
        $categories = Category::all();
        $grouped = [];
        foreach ($categories as $category) {
            $grouped[$category->id] = Product::byCategory($category->id);
        }
        return $this->view('store/index', [
            'title'      => 'Mağaza',
            'categories' => $categories,
            'grouped'    => $grouped,
            'featured'   => Product::featured(3),
        ]);
    }

    public function category(Request $request, string $slug): string
    {
        $category = Category::findBySlug($slug);
        if ($category === null) {
            return $this->notFound();
        }
        return $this->view('store/category', [
            'title'      => $category->name,
            'category'   => $category,
            'products'   => Product::byCategory($category->id),
            'categories' => Category::all(),
        ]);
    }

    public function product(Request $request, string $slug): string
    {
        $product = Product::findBySlug($slug);
        if ($product === null || !$product->is_active) {
            return $this->notFound();
        }
        return $this->view('store/product', [
            'title'    => $product->name,
            'product'  => $product,
            'category' => Category::find($product->category_id),
        ]);
    }

    public function buy(Request $request, string $id): void
    {
        $this->verifyCsrf($request);
        $user = Auth::user();
        $product = Product::find((int) $id);

        if ($product === null || !$product->is_active) {
            Flash::error('Ürün bulunamadı.');
            $this->back();
        }
        if (!$product->inStock()) {
            Flash::error('Bu ürün stokta yok.');
            $this->back();
        }

        $price = $product->effectivePrice();
        if ($user->balance < $price) {
            Flash::error('Yetersiz bakiye. Lütfen kredi yükleyin.');
            redirect('kredi-satin-al');
        }

        // Charge credits and create the order atomically.
        Database::beginTransaction();
        try {
            $user->addBalance(-$price, 'spend', 'Mağaza: ' . $product->name);
            $orderId = Order::create([
                'user_id'      => $user->id,
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'quantity'     => 1,
                'total'        => $price,
                'status'       => 'completed',
                'delivery'     => 'pending',
            ]);
            $product->decrementStock();
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollBack();
            error_log((string) $e);
            Flash::error('Satın alma sırasında bir hata oluştu.');
            $this->back();
        }

        // Deliver via RCON (best effort).
        $delivered = $this->deliver($product, $user->username);
        Order::setDelivery($orderId, $delivered ? 'delivered' : 'failed');

        if ($delivered) {
            Flash::success($product->name . ' satın alındı ve oyuna teslim edildi!');
        } else {
            Flash::info($product->name . ' satın alındı. Ürün, oyuna giriş yaptığında teslim edilecek.');
        }
        redirect('profil/siparisler');
    }

    private function deliver(Product $product, string $username): bool
    {
        if (trim($product->commands) === '') {
            return true;
        }
        try {
            $rcon = RconService::fromConfig();
            if (!$rcon->connect()) {
                return false;
            }
            $ok = $rcon->runCommands($product->commands, ['{player}' => $username, '{username}' => $username]);
            $rcon->disconnect();
            return $ok;
        } catch (\Throwable $e) {
            error_log('RCON delivery failed: ' . $e->getMessage());
            return false;
        }
    }
}
