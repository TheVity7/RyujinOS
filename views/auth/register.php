<?php
/** @var \App\Core\View $self */
use App\Core\Flash;
$self->layout('app');
$old = Flash::old();
?>
<div class="max-w-md mx-auto px-4 py-12">
    <div class="card p-8">
        <div class="text-center mb-6">
            <img src="https://mc-heads.net/avatar/MHF_Steve/56" class="w-14 h-14 rounded-xl mx-auto" alt="">
            <h1 class="text-2xl font-black text-[#4a3a26] mt-3">Kayıt Ol</h1>
            <p class="text-sm text-[#8a755a] mt-1">Hemen ücretsiz hesap oluştur</p>
        </div>
        <form method="post" action="<?= url('kayit') ?>" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label class="label">Kullanıcı Adı</label>
                <input class="input" name="username" value="<?= e($old['username'] ?? '') ?>" placeholder="3-16 karakter" autofocus>
            </div>
            <div>
                <label class="label">E-posta <span class="text-[#b8a47f] font-normal">(opsiyonel)</span></label>
                <input class="input" type="email" name="email" value="<?= e($old['email'] ?? '') ?>" placeholder="ornek@mail.com">
            </div>
            <div>
                <label class="label">Şifre</label>
                <input class="input" type="password" name="password" placeholder="En az 6 karakter">
            </div>
            <div>
                <label class="label">Şifre (Tekrar)</label>
                <input class="input" type="password" name="password_confirm" placeholder="••••••••">
            </div>
            <button class="btn-brand w-full py-3 rounded-xl font-extrabold">Kayıt Ol</button>
        </form>
        <p class="text-center text-sm text-[#8a755a] mt-5">
            Zaten hesabın var mı? <a href="<?= url('giris') ?>" class="font-extrabold text-brand-600 hover:text-brand-700">Giriş Yap</a>
        </p>
    </div>
</div>
