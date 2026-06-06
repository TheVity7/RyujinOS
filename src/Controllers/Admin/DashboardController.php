<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Order;
use App\Models\Post;
use App\Models\Product;
use App\Models\User;

final class DashboardController extends Controller
{
    public function index(): string
    {
        return $this->view('admin/dashboard', [
            'title'        => 'Dashboard',
            'userCount'    => User::count(),
            'orderCount'   => Order::count(),
            'revenue'      => Order::revenue(),
            'productCount' => count(Product::all()),
            'postCount'    => count(Post::all()),
            'openTickets'  => (int) Database::scalar("SELECT COUNT(*) FROM support_tickets WHERE status != 'closed'"),
            'paidTotal'    => (float) Database::scalar("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status = 'paid'"),
            'recentOrders' => Order::all(8),
            'recentUsers'  => User::all(6),
        ]);
    }
}
