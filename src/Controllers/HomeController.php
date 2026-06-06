<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\CreditTransaction;
use App\Models\Order;
use App\Models\Post;
use App\Models\User;

final class HomeController extends Controller
{
    public function index(Request $request): string
    {
        $page = max(1, $request->int('sayfa', 1));
        $perPage = 5;

        $monthStart = date('Y-m-01 00:00:00');

        return $this->view('home/index', [
            'title'         => 'Anasayfa',
            'posts'         => Post::published($perPage, ($page - 1) * $perPage),
            'page'          => $page,
            'totalPages'    => max(1, (int) ceil(Post::publishedCount() / $perPage)),
            'topLoader'     => User::topCreditLoaders(1, $monthStart)[0] ?? null,
            'recentLoaders' => CreditTransaction::recentLoads(4),
            'topDonators'   => User::topCreditLoaders(1)[0] ?? null,
            'recentOrders'  => Order::recent(5),
        ]);
    }
}
