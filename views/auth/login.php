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
            <h1 class="text-2xl font-black text-[#4a3a26] mt-3">Giriş Yap</h1>
            <p class="text-sm text-[#8a755a] mt-1">Oyun hesabınla siteye giriş yap</p>
        </div>
        <form method="post" action="<?= url('giris') ?>" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label class="label">Kullanıcı Adı</label>
                <input class="input" name="username" value="<?= e($old['username'] ?? '') ?>" placeholder="Minecraft kullanıcı adın" autofocus>
            </div>
            <div>
                <label class="label">Şifre</label>
                <input class="input" type="password" name="password" placeholder="••••••••">
            </div>
            <button class="btn-brand w-full py-3 rounded-xl font-extrabold">Giriş Yap</button>
        </form>
        <p class="text-center text-sm text-[#8a755a] mt-5">
            Hesabın yok mu? <a href="<?= url('kayit') ?>" class="font-extrabold text-brand-600 hover:text-brand-700">Kayıt Ol</a>
        </p>
        <div class="mt-4 text-xs text-[#a8906a] bg-[#f6ecd9] rounded-xl p-3 leading-relaxed">
            <strong>AuthMe/Velocity:</strong> Oyunda kayıtlı oyuncular aynı kullanıcı adı ve şifreyle giriş yapabilir.
        </div>
    </div>
</div>
