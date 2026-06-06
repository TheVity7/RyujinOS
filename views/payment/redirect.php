<?php
/** @var \App\Core\View $self */
/** @var string $form */
/** @var \App\Models\Payment $payment */
$self->layout('app');
?>
<div class="max-w-lg mx-auto px-4 mt-12">
    <div class="card p-8 text-center">
        <div class="text-5xl animate-pulse">💳</div>
        <h1 class="text-2xl font-black text-[#4a3a26] mt-3">Shopier'e Yönlendiriliyorsunuz...</h1>
        <p class="text-[#8a755a] mt-2 text-sm">Sipariş <?= e($payment->order_ref) ?> · <?= money($payment->amount) ?></p>
        <p class="text-[#a8906a] mt-4 text-xs">Otomatik yönlendirilmezseniz lütfen bekleyin.</p>
    </div>
</div>
<?= $form ?>
