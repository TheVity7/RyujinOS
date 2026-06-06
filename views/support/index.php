<?php
/** @var \App\Core\View $self */
/** @var \App\Models\User $user */
/** @var list<\App\Models\SupportTicket> $tickets */
use App\Models\SupportTicket;
$self->layout('app');
?>
<div class="max-w-6xl mx-auto px-4 mt-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <?php $self->partial('partials/profile-sidebar', ['user' => $user]); ?>
        <div class="lg:col-span-3 space-y-6">
            <div class="card p-6">
                <h3 class="font-black text-[#4a3a26] text-lg mb-4">Yeni Destek Talebi</h3>
                <form method="post" action="<?= url('destek') ?>" class="space-y-4">
                    <?= csrf_field() ?>
                    <div>
                        <label class="label">Konu</label>
                        <input type="text" name="subject" class="input" required placeholder="Örn: Satın aldığım VIP gelmedi">
                    </div>
                    <div>
                        <label class="label">Mesaj</label>
                        <textarea name="message" rows="4" class="input" required placeholder="Sorununuzu detaylıca açıklayın..."></textarea>
                    </div>
                    <button type="submit" class="btn-brand px-6 py-2.5 rounded-xl font-extrabold text-white">Talep Oluştur</button>
                </form>
            </div>

            <div class="card p-6">
                <h3 class="font-black text-[#4a3a26] text-lg mb-4">Taleplerim</h3>
                <?php if ($tickets === []): ?>
                    <p class="text-[#8a755a]">Henüz destek talebiniz yok.</p>
                <?php else: ?>
                    <div class="divide-y divide-[#f3e4c4]">
                        <?php foreach ($tickets as $t): ?>
                        <a href="<?= url('destek/' . $t->id) ?>" class="flex items-center justify-between py-3 hover:bg-[#fbf3e2] -mx-2 px-2 rounded-lg transition">
                            <div>
                                <div class="font-bold text-[#5b4a36]">#<?= $t->id ?> · <?= e($t->subject) ?></div>
                                <div class="text-xs text-[#a8906a]"><?= e(date('d.m.Y H:i', strtotime($t->created_at))) ?></div>
                            </div>
                            <span class="badge <?= $t->status === 'answered' ? 'bg-green-100 text-green-700' : ($t->status === 'closed' ? 'bg-gray-200 text-gray-600' : 'bg-yellow-100 text-yellow-700') ?>"><?= e(SupportTicket::statusLabel($t->status)) ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
