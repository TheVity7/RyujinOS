<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Request;
use App\Models\Payment;

final class CreditController extends Controller
{
    /**
     * Predefined credit packages. amount = TL charged, credits = granted.
     *
     * @return list<array{amount:float,credits:float,bonus:int,popular:bool}>
     */
    public static function packages(): array
    {
        return [
            ['amount' => 25.0,  'credits' => 25.0,  'bonus' => 0,  'popular' => false],
            ['amount' => 50.0,  'credits' => 55.0,  'bonus' => 10, 'popular' => false],
            ['amount' => 100.0, 'credits' => 115.0, 'bonus' => 15, 'popular' => true],
            ['amount' => 250.0, 'credits' => 300.0, 'bonus' => 20, 'popular' => false],
            ['amount' => 500.0, 'credits' => 625.0, 'bonus' => 25, 'popular' => false],
        ];
    }

    public function index(): string
    {
        return $this->view('credit/index', [
            'title'    => 'Kredi Yükle',
            'user'     => Auth::user(),
            'packages' => self::packages(),
        ]);
    }

    public function checkout(Request $request): void
    {
        $this->verifyCsrf($request);
        $user = Auth::user();
        $index = $request->int('package', -1);
        $packages = self::packages();

        if (!isset($packages[$index])) {
            Flash::error('Geçersiz paket seçimi.');
            redirect('kredi-satin-al');
        }
        $package = $packages[$index];

        $payment = Payment::create([
            'user_id'   => $user->id,
            'order_ref' => 'RYU-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(4))),
            'amount'    => $package['amount'],
            'credits'   => $package['credits'],
            'provider'  => 'shopier',
            'status'    => 'pending',
        ]);

        redirect('odeme/' . $payment->order_ref);
    }
}
