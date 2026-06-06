<?php
/** @var \App\Core\View $self */
/** @var \App\Models\User $user */
/** @var array $orders */
use App\Models\Order;
$self->layout('app');
?>
<div class="max-w-6xl mx-auto px-4 mt-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <?php $self->partial('partials/profile-sidebar', ['user' => $user]); ?>
        <div class="lg:col-span-3">
            <div class="card p-6">
                <h3 class="font-black text-[#4a3a26] text-lg mb-4">Siparişlerim</h3>
                <?php if ($orders === []): ?>
                    <p class="text-[#8a755a]">Henüz sipariş vermediniz. <a href="<?= url('magaza') ?>" class="text-brand-600 font-bold">Mağazaya göz at →</a></p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-[#b08a4f] border-b border-[#f0ddb8]">
                                    <th class="py-2 font-extrabold">Ürün</th>
                                    <th class="py-2 font-extrabold">Tutar</th>
                                    <th class="py-2 font-extrabold">Durum</th>
                                    <th class="py-2 font-extrabold">Teslimat</th>
                                    <th class="py-2 font-extrabold">Tarih</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#f3e4c4]">
                                <?php foreach ($orders as $o): ?>
                                <tr>
                                    <td class="py-3 font-bold text-[#5b4a36]"><?= e($o['product_name']) ?></td>
                                    <td class="py-3 text-[#5b4a36]"><?= credits((float) $o['total']) ?></td>
                                    <td class="py-3"><span class="badge bg-[#fbeccc] text-brand-700"><?= e(Order::statusLabel($o['status'])) ?></span></td>
                                    <td class="py-3">
                                        <span class="badge <?= $o['delivery'] === 'delivered' ? 'bg-green-100 text-green-700' : ($o['delivery'] === 'failed' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-700') ?>">
                                            <?= $o['delivery'] === 'delivered' ? 'Teslim Edildi' : ($o['delivery'] === 'failed' ? 'Başarısız' : 'Beklemede') ?>
                                        </span>
                                    </td>
                                    <td class="py-3 text-[#a8906a]"><?= e(date('d.m.Y H:i', strtotime($o['created_at']))) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
