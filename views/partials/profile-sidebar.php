<?php
/** @var \App\Models\User $user */
$path = (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$items = [
    ['/profil', '👤', 'Genel Bakış'],
    ['/profil/siparisler', '📦', 'Siparişlerim'],
    ['/profil/kredi-gecmisi', '💰', 'Kredi Geçmişi'],
    ['/profil/duzenle', '✏️', 'Profili Düzenle'],
    ['/profil/guvenlik', '🔒', 'Güvenlik'],
];
?>
<aside class="lg:col-span-1 space-y-5">
    <div class="card p-5 text-center">
        <img src="<?= e($user->avatarUrl(96)) ?>" class="w-20 h-20 rounded-2xl mx-auto shadow" alt="">
        <h2 class="font-black text-lg text-[#4a3a26] mt-3"><?= e($user->username) ?></h2>
        <span class="badge bg-[#fbeccc] text-brand-700"><?= e($user->roleLabel()) ?></span>
        <div class="btn-brand rounded-2xl p-3 mt-4 text-white">
            <div class="text-xs opacity-90">Bakiye</div>
            <div class="text-2xl font-black"><?= credits($user->balance) ?></div>
        </div>
        <a href="<?= url('kredi-satin-al') ?>" class="block btn-soft mt-3 py-2 rounded-xl font-extrabold text-sm">+ Kredi Yükle</a>
    </div>

    <div class="card p-3">
        <nav class="space-y-1">
            <?php foreach ($items as [$href, $icon, $label]): ?>
                <a href="<?= url(ltrim($href, '/')) ?>" class="flex items-center gap-2 nav-link <?= $path === $href ? 'active' : '' ?>">
                    <span><?= $icon ?></span><?= e($label) ?>
                </a>
            <?php endforeach; ?>
            <?php if ($user->isAdmin()): ?>
                <a href="<?= url('yonetim') ?>" class="flex items-center gap-2 nav-link text-brand-600"><span>⚙️</span>Yönetim Paneli</a>
            <?php endif; ?>
        </nav>
    </div>
</aside>
