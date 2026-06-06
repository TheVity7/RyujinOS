<?php
/** @var \App\Core\View $self */
/** @var \App\Models\User $user */
$self->layout('app');
?>
<div class="max-w-6xl mx-auto px-4 mt-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <?php $self->partial('partials/profile-sidebar', ['user' => $user]); ?>
        <div class="lg:col-span-3">
            <div class="card p-6">
                <h3 class="font-black text-[#4a3a26] text-lg mb-1">Şifre Değiştir</h3>
                <p class="text-sm text-[#8a755a] mb-4">Bu şifre hem sitede hem de oyun içi girişte (AuthMe/Velocity) geçerlidir.</p>
                <form method="post" action="<?= url('profil/guvenlik') ?>" class="space-y-4 max-w-md">
                    <?= csrf_field() ?>
                    <div>
                        <label class="label">Mevcut Şifre</label>
                        <input type="password" name="current_password" class="input" required>
                    </div>
                    <div>
                        <label class="label">Yeni Şifre</label>
                        <input type="password" name="new_password" class="input" required minlength="6">
                    </div>
                    <div>
                        <label class="label">Yeni Şifre (Tekrar)</label>
                        <input type="password" name="new_password_confirm" class="input" required minlength="6">
                    </div>
                    <button type="submit" class="btn-brand px-6 py-2.5 rounded-xl font-extrabold text-white">Şifreyi Güncelle</button>
                </form>
            </div>
        </div>
    </div>
</div>
