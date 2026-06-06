<?php
/** @var \App\Core\View $self */
/** @var \App\Models\Payment $payment */
$self->layout('app');
?>
<div class="max-w-lg mx-auto px-4 mt-12">
    <div class="card p-8 text-center">
        <div class="text-5xl">🧪</div>
        <h1 class="text-2xl font-black text-[#4a3a26] mt-3">Test Ödeme Ekranı</h1>
        <p class="text-[#8a755a] mt-2 text-sm">Shopier test modu aktif. Gerçek ödeme alınmaz; aşağıdaki buton ödemeyi başarıyla tamamlanmış kabul eder.</p>

        <div class="bg-[#fbf3e2] rounded-2xl p-5 mt-5 text-left space-y-2">
            <div class="flex justify-between"><span class="text-[#8a755a]">Sipariş No</span><span class="font-bold text-[#4a3a26]"><?= e($payment->order_ref) ?></span></div>
            <div class="flex justify-between"><span class="text-[#8a755a]">Tutar</span><span class="font-bold text-[#4a3a26]"><?= money($payment->amount) ?></span></div>
            <div class="flex justify-between"><span class="text-[#8a755a]">Kazanılacak Kredi</span><span class="font-black text-brand-600"><?= credits($payment->credits) ?></span></div>
        </div>

        <div class="flex gap-3 mt-6">
            <a href="<?= url('kredi-satin-al') ?>" class="btn-soft flex-1 py-3 rounded-xl font-extrabold">İptal</a>
            <a href="<?= url('odeme/sandbox/' . $payment->order_ref) ?>" class="btn-brand flex-1 py-3 rounded-xl font-extrabold text-white">Ödemeyi Tamamla</a>
        </div>
    </div>
</div>
