<?php
/** @var \App\Core\View $self */
/** @var \App\Models\Payment $payment */
$self->layout('app');
$paid = $payment->isPaid();
?>
<div class="max-w-lg mx-auto px-4 mt-12">
    <div class="card p-8 text-center">
        <div class="text-6xl"><?= $paid ? '✅' : '⏳' ?></div>
        <h1 class="text-2xl font-black text-[#4a3a26] mt-3">
            <?= $paid ? 'Ödeme Başarılı!' : 'Ödeme Beklemede' ?>
        </h1>
        <?php if ($paid): ?>
            <p class="text-[#8a755a] mt-2"><?= credits($payment->credits) ?> hesabınıza eklendi.</p>
        <?php else: ?>
            <p class="text-[#8a755a] mt-2">Ödemeniz henüz tamamlanmadı. Ödeme yaptıysanız birkaç dakika içinde yansıyacaktır.</p>
        <?php endif; ?>

        <div class="bg-[#fbf3e2] rounded-2xl p-5 mt-5 text-left space-y-2">
            <div class="flex justify-between"><span class="text-[#8a755a]">Sipariş No</span><span class="font-bold text-[#4a3a26]"><?= e($payment->order_ref) ?></span></div>
            <div class="flex justify-between"><span class="text-[#8a755a]">Tutar</span><span class="font-bold text-[#4a3a26]"><?= money($payment->amount) ?></span></div>
        </div>

        <div class="flex gap-3 mt-6">
            <a href="<?= url('profil/kredi-gecmisi') ?>" class="btn-soft flex-1 py-3 rounded-xl font-extrabold">Kredi Geçmişi</a>
            <a href="<?= url('magaza') ?>" class="btn-brand flex-1 py-3 rounded-xl font-extrabold text-white">Mağazaya Git</a>
        </div>
    </div>
</div>
