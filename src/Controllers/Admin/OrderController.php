<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Order;

final class OrderController extends Controller
{
    public function index(): string
    {
        return $this->view('admin/orders', [
            'title'  => 'Siparişler',
            'orders' => Order::all(200),
        ]);
    }
}
