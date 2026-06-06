<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Core\Request;
use App\Models\Payment;
use App\Models\User;
use App\Services\ShopierService;

final class PaymentController extends Controller
{
    /**
     * Begin a payment. In Shopier test mode (or without merchant keys) we show
     * a local sandbox page so the full top-up flow is demonstrable; otherwise
     * the auto-submitting Shopier form is rendered.
     */
    public function redirect(Request $request, string $ref): string
    {
        $user = Auth::user();
        $payment = Payment::findByRef($ref);
        if ($payment === null || $payment->user_id !== $user->id) {
            return $this->notFound();
        }
        if ($payment->isPaid()) {
            redirect('odeme/sonuc/' . $ref);
        }

        $shopier = ShopierService::fromConfig();
        if ($shopier->isTestMode()) {
            return $this->view('payment/sandbox', [
                'title'   => 'Ödeme (Test)',
                'payment' => $payment,
            ]);
        }

        $form = $shopier->buildPaymentForm([
            'order_id'     => $payment->order_ref,
            'amount'       => $payment->amount,
            'buyer_name'   => $user->username,
            'email'        => $user->email ?? ('player+' . $user->id . '@ryujinos.net'),
            'product_name' => 'Kredi Yükleme - ' . credits($payment->credits),
        ]);

        return $this->view('payment/redirect', [
            'title'   => 'Ödemeye Yönlendiriliyorsunuz',
            'form'    => $form,
            'payment' => $payment,
        ]);
    }

    /**
     * Sandbox completion (test mode only): grant the credits and finish.
     */
    public function sandbox(Request $request, string $ref): void
    {
        $user = Auth::user();
        $payment = Payment::findByRef($ref);
        if ($payment === null || $payment->user_id !== $user->id) {
            Flash::error('Ödeme bulunamadı.');
            redirect('kredi-satin-al');
        }
        if (!ShopierService::fromConfig()->isTestMode()) {
            Flash::error('Sandbox yalnızca test modunda kullanılabilir.');
            redirect('odeme/' . $ref);
        }
        $this->grant($payment, 'TEST-' . $payment->order_ref);
        Flash::success(credits($payment->credits) . ' bakiyenize eklendi! (Test ödemesi)');
        redirect('odeme/sonuc/' . $ref);
    }

    /**
     * Shopier server-to-server callback (live mode).
     */
    public function callback(Request $request): string
    {
        $post = $_POST;
        $shopier = ShopierService::fromConfig();
        $ref = (string) ($post['platform_order_id'] ?? '');
        $payment = Payment::findByRef($ref);

        if ($payment === null) {
            http_response_code(404);
            return 'unknown order';
        }
        if (!$shopier->verifyCallback($post) || !$shopier->callbackSucceeded($post)) {
            $payment->markFailed();
            http_response_code(400);
            return 'verification failed';
        }
        if (!$payment->isPaid()) {
            $this->grant($payment, (string) ($post['payment_id'] ?? null));
        }
        return 'OK';
    }

    public function result(Request $request, string $ref): string
    {
        $payment = Payment::findByRef($ref);
        if ($payment === null) {
            return $this->notFound();
        }
        return $this->view('payment/result', [
            'title'   => 'Ödeme Sonucu',
            'payment' => $payment,
        ]);
    }

    /**
     * Mark the payment paid and credit the user's balance (idempotent).
     */
    private function grant(Payment $payment, ?string $paymentId): void
    {
        $payment->markPaid($paymentId);
        $user = User::find($payment->user_id);
        if ($user instanceof User) {
            $user->addBalance($payment->credits, 'purchase', 'Kredi yükleme (' . $payment->order_ref . ')');
        }
    }
}
