<?php
/** @var \App\Core\View $self */
/** @var \App\Models\User $user */
/** @var array $orders */
/** @var array $transactions */
use App\Models\CreditTransaction;
use App\Models\Order;
$self->layout('admin');
?>
<a href="<?= url('yonetim/kullanicilar') ?>" class="text-sm text-brand-600 font-bold">← Kullanıcılara dön</a>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-3">
    <div class="space-y-6">
        <div class="card p-6 text-center">
            <img src="<?= e($user->avatarUrl(96)) ?>" class="w-20 h-20 rounded-2xl mx-auto" alt="">
            <h2 class="font-black text-lg text-[#4a3a26] mt-3"><?= e($user->username) ?></h2>
            <div class="btn-brand rounded-2xl p-3 mt-3 text-white">
                <div class="text-xs opacity-90">Bakiye</div>
                <div class="text-2xl font-black"><?= credits($user->balance) ?></div>
            </div>
        </div>

        <div class="card p-6">
            <h3 class="font-black text-[#4a3a26] mb-3">Bilgileri Düzenle</h3>
            <form method="post" action="<?= url('yonetim/kullanicilar/' . $user->id) ?>" class="space-y-3">
                <?= csrf_field() ?>
                <div><label class="label">E-posta</label><input type="email" name="email" class="input" value="<?= e($user->email ?? '') ?>"></div>
                <div>
                    <label class="label">Rol</label>
                    <select name="role" class="input">
                        <option value="member" <?= !$user->isAdmin() ? 'selected' : '' ?>>Oyuncu</option>
                        <option value="admin" <?= $user->isAdmin() ? 'selected' : '' ?>>Yönetici</option>
                    </select>
                </div>
                <button class="btn-brand w-full py-2.5 rounded-xl font-extrabold text-white">Kaydet</button>
            </form>
        </div>

        <div class="card p-6">
            <h3 class="font-black text-[#4a3a26] mb-3">Kredi Düzenle</h3>
            <form method="post" action="<?= url('yonetim/kullanicilar/' . $user->id . '/kredi') ?>" class="space-y-3">
                <?= csrf_field() ?>
                <div><label class="label">Tutar (+ ekle / - düş)</label><input type="number" step="0.01" name="amount" class="input" placeholder="100 veya -50" required></div>
                <div><label class="label">Not</label><input type="text" name="note" class="input" placeholder="Açıklama"></div>
                <button class="btn-soft w-full py-2.5 rounded-xl font-extrabold">Bakiyeyi Güncelle</button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-6">
        <div class="card p-6">
            <h3 class="font-black text-[#4a3a26] mb-3">Siparişler</h3>
            <?php if ($orders === []): ?><p class="text-[#8a755a] text-sm">Sipariş yok.</p><?php else: ?>
            <table class="w-full text-sm"><tbody class="divide-y divide-[#f3e4c4]">
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td class="py-2 font-bold text-[#5b4a36]"><?= e($o['product_name']) ?></td>
                    <td class="py-2 text-right text-[#4a3a26]"><?= credits((float) $o['total']) ?></td>
                    <td class="py-2 text-right"><span class="badge bg-[#fbeccc] text-brand-700"><?= e(Order::statusLabel($o['status'])) ?></span></td>
                    <td class="py-2 text-right text-[#a8906a] text-xs"><?= e(date('d.m.Y', strtotime((string) $o['created_at']))) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody></table>
            <?php endif; ?>
        </div>

        <div class="card p-6">
            <h3 class="font-black text-[#4a3a26] mb-3">Kredi Hareketleri</h3>
            <?php if ($transactions === []): ?><p class="text-[#8a755a] text-sm">Hareket yok.</p><?php else: ?>
            <table class="w-full text-sm"><tbody class="divide-y divide-[#f3e4c4]">
                <?php foreach ($transactions as $t): ?>
                <tr>
                    <td class="py-2 text-[#5b4a36]"><?= e($t['description'] ?: CreditTransaction::typeLabel($t['type'])) ?></td>
                    <td class="py-2 text-right font-black <?= (float) $t['amount'] >= 0 ? 'text-green-600' : 'text-red-500' ?>"><?= ((float) $t['amount'] >= 0 ? '+' : '') . credits((float) $t['amount']) ?></td>
                    <td class="py-2 text-right text-[#a8906a] text-xs"><?= e(date('d.m.Y H:i', strtotime((string) $t['created_at']))) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody></table>
            <?php endif; ?>
        </div>
    </div>
</div>
