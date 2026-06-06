<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Shopier payment gateway integration.
 *
 * Builds the auto-submitting payment form (api_pay4) and verifies the
 * callback signature. When test mode is enabled no real request is made and
 * the credit purchase can be completed through a local sandbox callback so
 * the whole flow is demonstrable without live merchant keys.
 *
 * @see https://www.shopier.com  (Merchant panel -> API)
 */
final class ShopierService
{
    private const API_URL = 'https://www.shopier.com/ShowProduct/api_pay4.php';
    private const MODULE_VERSION = '1.0.0';

    public function __construct(
        private readonly string $apiKey,
        private readonly string $apiSecret,
        private readonly bool $testMode,
    ) {
    }

    public static function fromConfig(): self
    {
        return new self(
            (string) config('shopier.api_key'),
            (string) config('shopier.api_secret'),
            (bool) config('shopier.test_mode'),
        );
    }

    public function isTestMode(): bool
    {
        return $this->testMode || $this->apiKey === '' || $this->apiSecret === '';
    }

    /**
     * Build the HTML for an auto-submitting Shopier payment form.
     *
     * @param array{order_id:string, amount:float, buyer_name:string, buyer_surname?:string, email:string, phone?:string, product_name:string} $payment
     */
    public function buildPaymentForm(array $payment): string
    {
        $randomNr = (string) random_int(100000, 999999);
        $orderId = $payment['order_id'];
        $amount = number_format($payment['amount'], 2, '.', '');
        $currency = '0'; // 0 = TRY

        $signatureData = $randomNr . $orderId . $amount . $currency;
        $signature = base64_encode(hash_hmac('sha256', $signatureData, $this->apiSecret, true));

        $fields = [
            'API_key'             => $this->apiKey,
            'website_index'       => '1',
            'platform_order_id'   => $orderId,
            'product_name'        => $payment['product_name'],
            'product_type'        => '1',
            'buyer_name'          => $payment['buyer_name'],
            'buyer_surname'       => $payment['buyer_surname'] ?? '-',
            'buyer_email'         => $payment['email'],
            'buyer_account_age'   => '0',
            'buyer_id_nr'         => $orderId,
            'buyer_phone'         => $payment['phone'] ?? '',
            'billing_address'     => '-',
            'billing_city'        => '-',
            'billing_country'     => '-',
            'billing_postcode'    => '-',
            'shipping_address'    => '-',
            'shipping_city'       => '-',
            'shipping_country'    => '-',
            'shipping_postcode'   => '-',
            'total_order_value'   => $amount,
            'currency'            => $currency,
            'platform'            => '0',
            'is_in_frame'         => '0',
            'current_language'    => '0',
            'modul_version'       => self::MODULE_VERSION,
            'random_nr'           => $randomNr,
            'signature'           => $signature,
        ];

        $inputs = '';
        foreach ($fields as $name => $value) {
            $inputs .= sprintf(
                '<input type="hidden" name="%s" value="%s">' . "\n",
                htmlspecialchars($name, ENT_QUOTES),
                htmlspecialchars((string) $value, ENT_QUOTES)
            );
        }

        return sprintf(
            '<form id="shopier_payment_form" method="post" action="%s">%s</form>'
            . '<script>document.getElementById("shopier_payment_form").submit();</script>',
            self::API_URL,
            $inputs
        );
    }

    /**
     * Verify a Shopier callback signature.
     *
     * @param array<string,mixed> $post
     */
    public function verifyCallback(array $post): bool
    {
        $randomNr = (string) ($post['random_nr'] ?? '');
        $orderId = (string) ($post['platform_order_id'] ?? '');
        $received = (string) ($post['signature'] ?? '');
        if ($randomNr === '' || $orderId === '' || $received === '') {
            return false;
        }
        $expected = base64_encode(hash_hmac('sha256', $randomNr . $orderId, $this->apiSecret, true));
        return hash_equals($expected, $received);
    }

    public function callbackSucceeded(array $post): bool
    {
        return strtolower((string) ($post['status'] ?? '')) === 'success';
    }
}
