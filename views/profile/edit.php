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
                <h3 class="font-black text-[#4a3a26] text-lg mb-4">Profili Düzenle</h3>
                <form method="post" action="<?= url('profil/duzenle') ?>" class="space-y-4">
                    <?= csrf_field() ?>
                    <div>
                        <label class="label">Kullanıcı Adı</label>
                        <input type="text" class="input bg-[#f6ecd8] cursor-not-allowed" value="<?= e($user->username) ?>" disabled>
                        <p class="text-xs text-[#a8906a] mt-1">Kullanıcı adı oyun hesabınızla eşleştiği için değiştirilemez.</p>
                    </div>
                    <div>
                        <label class="label">E-posta</label>
                        <input type="email" name="email" class="input" value="<?= e($user->email ?? '') ?>" placeholder="ornek@mail.com">
                    </div>
                    <div>
                        <label class="label">Avatar URL (opsiyonel)</label>
                        <input type="url" name="avatar" class="input" value="<?= e($user->avatar ?? '') ?>" placeholder="https://...">
                        <p class="text-xs text-[#a8906a] mt-1">Boş bırakırsanız Minecraft kafa görseliniz kullanılır.</p>
                    </div>
                    <button type="submit" class="btn-brand px-6 py-2.5 rounded-xl font-extrabold text-white">Kaydet</button>
                </form>
            </div>
        </div>
    </div>
</div>
