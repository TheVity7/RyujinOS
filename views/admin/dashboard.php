<?php
/** @var \App\Core\View $self */
/** @var int $userCount, $orderCount, $productCount, $postCount, $openTickets */
/** @var float $revenue, $paidTotal */
/** @var array $recentOrders, $recentUsers */
use App\Models\Order;
$self->layout('admin');
$cards = [
    ['👥', 'Kullanıcılar', number_format($userCount), 'bg-blue-50 text-blue-600'],
    ['🧾', 'Siparişler', number_format($orderCount), 'bg-purple-50 text-purple-600'],
    ['💰', 'Kredi Cirosu', credits($revenue), 'bg-green-50 text-green-600'],
    ['💳', 'Tahsilat (TL)', money($paidTotal), 'bg-brand-50 text-brand-600'],
    ['📦', 'Ürünler', number_format($productCount), 'bg-amber-50 text-amber-600'],
    ['🎫', 'Açık Talepler', number_format($openTickets), 'bg-red-50 text-red-600'],
];
?>
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($cards as $c): ?>
    <div class="card p-5">
        <div class="w-11 h-11 rounded-xl grid place-items-center text-xl <?= $c[3] ?>"><?= $c[0] ?></div>
        <div class="text-2xl font-black text-[#4a3a26] mt-3"><?= $c[2] ?></div>
        <div class="text-sm text-[#a8906a] font-bold"><?= e($c[1]) ?></div>
    </div>
    <?php endforeach; ?>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">
    <div class="card p-6">
        <h3 class="font-black text-[#4a3a26] mb-4">Son Siparişler</h3>
        <?php if ($recentOrders === []): ?>
            <p class="text-[#8a755a] text-sm">Henüz sipariş yok.</p>
        <?php else: ?>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-[#f3e4c4]">
                <?php foreach ($recentOrders as $o): ?>
                <tr>
                    <td class="py-2 font-bold text-[#5b4a36]"><?= e($o['username']) ?></td>
                    <td class="py-2 text-[#8a755a]"><?= e($o['product_name']) ?></td>
                    <td class="py-2 text-right font-black text-[#4a3a26]"><?= credits((float) $o['total']) ?></td>
                    <td class="py-2 text-right"><span class="badge bg-[#fbeccc] text-brand-700"><?= e(Order::statusLabel($o['status'])) ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <div class="card p-6">
        <h3 class="font-black text-[#4a3a26] mb-4">Yeni Kullanıcılar</h3>
        <div class="space-y-3">
            <?php foreach ($recentUsers as $u): ?>
            <div class="flex items-center gap-3">
                <img src="<?= e(mc_avatar((string) $u['username'], 32)) ?>" class="w-8 h-8 rounded-lg" alt="">
                <div class="flex-1">
                    <div class="font-bold text-[#4a3a26] text-sm"><?= e($u['username']) ?></div>
                    <div class="text-xs text-[#a8906a]"><?= e(date('d.m.Y', strtotime((string) $u['created_at']))) ?></div>
                </div>
                <span class="font-black text-brand-600 text-sm"><?= credits((float) $u['balance']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
