<?php
/** @var \App\Core\View $self */
/** @var \App\Models\User $user */
/** @var \App\Models\SupportTicket $ticket */
/** @var array $replies */
use App\Models\SupportTicket;
$self->layout('app');
?>
<div class="max-w-6xl mx-auto px-4 mt-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <?php $self->partial('partials/profile-sidebar', ['user' => $user]); ?>
        <div class="lg:col-span-3 space-y-5">
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <a href="<?= url('destek') ?>" class="text-sm text-brand-600 font-bold">← Taleplere dön</a>
                        <h3 class="font-black text-[#4a3a26] text-lg mt-1">#<?= $ticket->id ?> · <?= e($ticket->subject) ?></h3>
                    </div>
                    <span class="badge <?= $ticket->status === 'answered' ? 'bg-green-100 text-green-700' : ($ticket->status === 'closed' ? 'bg-gray-200 text-gray-600' : 'bg-yellow-100 text-yellow-700') ?>"><?= e(SupportTicket::statusLabel($ticket->status)) ?></span>
                </div>
            </div>

            <!-- original message -->
            <div class="card p-5">
                <div class="flex items-center gap-2 mb-2">
                    <img src="<?= e($user->avatarUrl(32)) ?>" class="w-7 h-7 rounded-lg" alt="">
                    <span class="font-bold text-[#4a3a26]"><?= e($user->username) ?></span>
                    <span class="text-xs text-[#a8906a]"><?= e(date('d.m.Y H:i', strtotime($ticket->created_at))) ?></span>
                </div>
                <p class="text-[#5b4a36] whitespace-pre-line"><?= e($ticket->message) ?></p>
            </div>

            <!-- replies -->
            <?php foreach ($replies as $r): ?>
            <div class="card p-5 <?= $r['is_staff'] ? 'border-l-4 border-brand-500 bg-[#fff7ea]' : '' ?>">
                <div class="flex items-center gap-2 mb-2">
                    <img src="<?= e(mc_avatar((string) $r['username'], 32)) ?>" class="w-7 h-7 rounded-lg" alt="">
                    <span class="font-bold text-[#4a3a26]"><?= e($r['username']) ?></span>
                    <?php if ($r['is_staff']): ?><span class="badge bg-brand-500 text-white">Yetkili</span><?php endif; ?>
                    <span class="text-xs text-[#a8906a]"><?= e(date('d.m.Y H:i', strtotime($r['created_at']))) ?></span>
                </div>
                <p class="text-[#5b4a36] whitespace-pre-line"><?= e($r['message']) ?></p>
            </div>
            <?php endforeach; ?>

            <?php if ($ticket->status !== 'closed'): ?>
            <div class="card p-5">
                <form method="post" action="<?= url('destek/' . $ticket->id) ?>" class="space-y-3">
                    <?= csrf_field() ?>
                    <label class="label">Yanıt Yaz</label>
                    <textarea name="message" rows="3" class="input" required placeholder="Mesajınızı yazın..."></textarea>
                    <button type="submit" class="btn-brand px-6 py-2.5 rounded-xl font-extrabold text-white">Gönder</button>
                </form>
            </div>
            <?php else: ?>
            <div class="card p-5 text-center text-[#8a755a]">Bu talep kapatılmıştır.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
