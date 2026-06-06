<?php
/** @var \App\Core\View $self */
/** @var array $orders */
use App\Models\Order;
$self->layout('admin');
?>
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-[#fbf3e2]">
            <tr class="text-left text-[#b08a4f]">
                <th class="px-4 py-3 font-extrabold">#</th>
                <th class="px-4 py-3 font-extrabold">Kullanıcı</th>
                <th class="px-4 py-3 font-extrabold">Ürün</th>
                <th class="px-4 py-3 font-extrabold">Tutar</th>
                <th class="px-4 py-3 font-extrabold">Durum</th>
                <th class="px-4 py-3 font-extrabold">Teslimat</th>
                <th class="px-4 py-3 font-extrabold">Tarih</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-[#f3e4c4]">
            <?php foreach ($orders as $o): ?>
            <tr class="hover:bg-[#fbf7ec]">
                <td class="px-4 py-3 text-[#a8906a]">#<?= $o['id'] ?></td>
                <td class="px-4 py-3 font-bold text-[#4a3a26]"><?= e($o['username']) ?></td>
                <td class="px-4 py-3 text-[#8a755a]"><?= e($o['product_name']) ?></td>
                <td class="px-4 py-3 font-black text-[#4a3a26]"><?= credits((float) $o['total']) ?></td>
                <td class="px-4 py-3"><span class="badge bg-[#fbeccc] text-brand-700"><?= e(Order::statusLabel($o['status'])) ?></span></td>
                <td class="px-4 py-3">
                    <span class="badge <?= $o['delivery'] === 'delivered' ? 'bg-green-100 text-green-700' : ($o['delivery'] === 'failed' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-700') ?>">
                        <?= $o['delivery'] === 'delivered' ? 'Teslim' : ($o['delivery'] === 'failed' ? 'Başarısız' : 'Bekliyor') ?>
                    </span>
                </td>
                <td class="px-4 py-3 text-[#a8906a] text-xs"><?= e(date('d.m.Y H:i', strtotime((string) $o['created_at']))) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if ($orders === []): ?>
            <tr><td colspan="7" class="px-4 py-8 text-center text-[#8a755a]">Henüz sipariş yok.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
