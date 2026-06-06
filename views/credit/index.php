<?php
/** @var \App\Core\View $self */
/** @var \App\Models\User $user */
/** @var array $packages */
$self->layout('app');
?>
<div class="max-w-6xl mx-auto px-4 mt-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <?php $self->partial('partials/profile-sidebar', ['user' => $user]); ?>
        <div class="lg:col-span-3">
            <div class="card p-6 mb-6">
                <h1 class="text-2xl font-black text-[#4a3a26]">Kredi Yükle</h1>
                <p class="text-[#8a755a] mt-1">Bir paket seç, Shopier ile güvenle öde. Krediler anında hesabına eklenir.</p>
            </div>

            <form method="post" action="<?= url('kredi-satin-al') ?>">
                <?= csrf_field() ?>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <?php foreach ($packages as $i => $p): ?>
                    <label class="card p-5 cursor-pointer relative block hover:shadow-lg transition has-[:checked]:ring-2 has-[:checked]:ring-brand-500">
                        <input type="radio" name="package" value="<?= $i ?>" class="absolute top-4 right-4 accent-brand-500" <?= $p['popular'] ? 'checked' : '' ?>>
                        <?php if ($p['popular']): ?><span class="badge bg-brand-500 text-white absolute -top-2 left-4">EN POPÜLER</span><?php endif; ?>
                        <div class="text-3xl font-black text-brand-600 mt-2"><?= credits($p['credits']) ?></div>
                        <?php if ($p['bonus'] > 0): ?>
                            <div class="text-sm font-extrabold text-green-600 mt-1">+%<?= $p['bonus'] ?> Bonus</div>
                        <?php endif; ?>
                        <div class="mt-4 pt-4 border-t border-[#f0ddb8] flex items-center justify-between">
                            <span class="text-[#8a755a] text-sm">Tutar</span>
                            <span class="font-black text-[#4a3a26]"><?= money($p['amount']) ?></span>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="btn-brand px-8 py-3 rounded-xl font-extrabold text-white mt-6">Ödemeye Geç →</button>
            </form>
        </div>
    </div>
</div>
