<?php
/** @var \App\Core\View $self */
/** @var \App\Models\User $user */
/** @var array $orders */
/** @var array $transactions */
/** @var int $orderCount */
use App\Models\CreditTransaction;
use App\Models\Order;
$self->layout('app');
?>
<div class="max-w-6xl mx-auto px-4 mt-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <?php $self->partial('partials/profile-sidebar', ['user' => $user]); ?>

        <div class="lg:col-span-3 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="card p-5">
                    <div class="text-xs font-extrabold text-[#b08a4f]">BAKİYE</div>
                    <div class="text-2xl font-black text-brand-600 mt-1"><?= credits($user->balance) ?></div>
                </div>
                <div class="card p-5">
                    <div class="text-xs font-extrabold text-[#b08a4f]">TOPLAM SİPARİŞ</div>
                    <div class="text-2xl font-black text-[#4a3a26] mt-1"><?= $orderCount ?></div>
                </div>
                <div class="card p-5">
                    <div class="text-xs font-extrabold text-[#b08a4f]">ÜYELİK</div>
                    <div class="text-lg font-black text-[#4a3a26] mt-1"><?= e(date('d.m.Y', strtotime($user->created_at))) ?></div>
                </div>
            </div>

            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-black text-[#4a3a26]">Son Siparişler</h3>
                    <a href="<?= url('profil/siparisler') ?>" class="text-sm font-extrabold text-brand-600">Tümü →</a>
                </div>
                <?php if ($orders === []): ?>
                    <p class="text-[#8a755a] text-sm">Henüz sipariş yok. <a href="<?= url('magaza') ?>" class="text-brand-600 font-bold">Mağazaya göz at →</a></p>
                <?php else: ?>
                    <div class="divide-y divide-[#f3e4c4]">
                        <?php foreach ($orders as $o): ?>
                        <div class="flex items-center justify-between py-3">
                            <div>
                                <div class="font-bold text-[#5b4a36]"><?= e($o['product_name']) ?></div>
                                <div class="text-xs text-[#a8906a]"><?= e(date('d.m.Y H:i', strtotime($o['created_at']))) ?></div>
                            </div>
                            <div class="text-right">
                                <div class="font-black text-[#4a3a26]"><?= credits((float) $o['total']) ?></div>
                                <span class="badge <?= $o['delivery'] === 'delivered' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>"><?= e(Order::statusLabel($o['status'])) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-black text-[#4a3a26]">Son Kredi Hareketleri</h3>
                    <a href="<?= url('profil/kredi-gecmisi') ?>" class="text-sm font-extrabold text-brand-600">Tümü →</a>
                </div>
                <?php if ($transactions === []): ?>
                    <p class="text-[#8a755a] text-sm">Henüz kredi hareketi yok.</p>
                <?php else: ?>
                    <div class="divide-y divide-[#f3e4c4]">
                        <?php foreach ($transactions as $t): ?>
                        <div class="flex items-center justify-between py-3">
                            <div>
                                <div class="font-bold text-[#5b4a36]"><?= e($t['description'] ?: CreditTransaction::typeLabel($t['type'])) ?></div>
                                <div class="text-xs text-[#a8906a]"><?= e(date('d.m.Y H:i', strtotime($t['created_at']))) ?></div>
                            </div>
                            <div class="font-black <?= (float) $t['amount'] >= 0 ? 'text-green-600' : 'text-red-500' ?>">
                                <?= ((float) $t['amount'] >= 0 ? '+' : '') . credits((float) $t['amount']) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
