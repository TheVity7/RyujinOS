<?php
/** @var \App\Core\View $self */
/** @var \App\Models\User $user */
/** @var array $transactions */
use App\Models\CreditTransaction;
$self->layout('app');
?>
<div class="max-w-6xl mx-auto px-4 mt-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <?php $self->partial('partials/profile-sidebar', ['user' => $user]); ?>
        <div class="lg:col-span-3">
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-black text-[#4a3a26] text-lg">Kredi Geçmişi</h3>
                    <a href="<?= url('kredi-satin-al') ?>" class="btn-brand px-4 py-2 rounded-xl font-extrabold text-white text-sm">+ Kredi Yükle</a>
                </div>
                <?php if ($transactions === []): ?>
                    <p class="text-[#8a755a]">Henüz kredi hareketiniz yok.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-[#b08a4f] border-b border-[#f0ddb8]">
                                    <th class="py-2 font-extrabold">İşlem</th>
                                    <th class="py-2 font-extrabold">Tür</th>
                                    <th class="py-2 font-extrabold">Tutar</th>
                                    <th class="py-2 font-extrabold">Tarih</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#f3e4c4]">
                                <?php foreach ($transactions as $t): ?>
                                <tr>
                                    <td class="py-3 font-bold text-[#5b4a36]"><?= e($t['description'] ?: CreditTransaction::typeLabel($t['type'])) ?></td>
                                    <td class="py-3"><span class="badge bg-[#fbeccc] text-brand-700"><?= e(CreditTransaction::typeLabel($t['type'])) ?></span></td>
                                    <td class="py-3 font-black <?= (float) $t['amount'] >= 0 ? 'text-green-600' : 'text-red-500' ?>">
                                        <?= ((float) $t['amount'] >= 0 ? '+' : '') . credits((float) $t['amount']) ?>
                                    </td>
                                    <td class="py-3 text-[#a8906a]"><?= e(date('d.m.Y H:i', strtotime($t['created_at']))) ?></td>
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
