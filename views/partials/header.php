<?php
/** @var \App\Models\User|null $user */
/** @var string $siteName */
use App\Models\Setting;

$serverIp = Setting::get('server_ip', 'play.ryujinos.net');
$discordInvite = Setting::get('discord_invite', '#');
$discordMembers = Setting::get('discord_members', '0');
$announcement = Setting::get('announcement', '');
$path = (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$isActive = static fn (string $p): string => ($p === '/' ? $path === '/' : str_starts_with($path, $p)) ? 'active' : '';

$navItems = [
    ['/', 'Anasayfa'],
    ['/magaza', 'Mağaza'],
    ['/blog', 'Blog'],
    ['/destek', 'Destek'],
    ['/kredi-satin-al', 'Kredi Satın Al'],
];
?>
<header class="relative z-20">
    <div class="cloud-deco">
        <svg class="absolute -top-6 left-10 w-40 opacity-60" viewBox="0 0 200 60" fill="#fff5dd"><ellipse cx="60" cy="40" rx="60" ry="20"/><ellipse cx="120" cy="35" rx="50" ry="22"/></svg>
        <svg class="absolute top-2 right-16 w-52 opacity-50" viewBox="0 0 200 60" fill="#fff5dd"><ellipse cx="70" cy="40" rx="70" ry="22"/><ellipse cx="140" cy="33" rx="55" ry="24"/></svg>
    </div>

    <?php if ($announcement !== ''): ?>
    <div class="relative pt-4 flex justify-center px-4">
        <div class="card !rounded-full px-5 py-2 flex items-center gap-2 text-sm font-bold">
            <span class="badge bg-brand-500 text-white">YENİ</span>
            <span class="text-[#6b5942]"><?= e($announcement) ?></span>
            <a href="<?= url('magaza') ?>" class="text-brand-600 hover:text-brand-700">Keşfet →</a>
        </div>
    </div>
    <?php endif; ?>

    <div class="relative max-w-6xl mx-auto px-4 pt-5">
        <div class="flex items-center justify-between gap-4">
            <button type="button" data-copy="<?= e($serverIp) ?>"
                    class="card px-4 py-2.5 flex items-center gap-3 text-left hover:shadow-lg transition">
                <span class="w-9 h-9 rounded-xl btn-brand grid place-items-center text-white">▶</span>
                <span>
                    <span class="block font-extrabold text-[#5b4a36] leading-tight" data-copy-label><?= e($serverIp) ?></span>
                    <span class="block text-xs text-[#a8906a]">IP Adresini Kopyala</span>
                </span>
            </button>

            <a href="<?= url('/') ?>" class="hidden sm:block">
                <img src="https://mc-heads.net/avatar/MHF_Steve/64" alt="logo" class="w-14 h-14 rounded-xl shadow-md">
            </a>

            <a href="<?= e($discordInvite) ?>" target="_blank" rel="noopener"
               class="px-4 py-2.5 rounded-2xl text-white flex items-center gap-3" style="background:#5865F2">
                <span class="text-2xl">🎮</span>
                <span>
                    <span class="block font-extrabold leading-tight">Discord</span>
                    <span class="block text-xs opacity-90">Topluluğumuza Katıl</span>
                </span>
                <span class="badge bg-white/20"><?= e($discordMembers) ?></span>
            </a>
        </div>

        <nav class="card mt-5 px-3 py-2.5 flex items-center justify-between gap-2">
            <div class="hidden lg:flex items-center gap-1">
                <?php foreach ($navItems as [$href, $label]): ?>
                    <a href="<?= url(ltrim($href, '/')) ?>" class="nav-link <?= $isActive($href) ?>"><?= e($label) ?></a>
                <?php endforeach; ?>
                <div class="relative">
                    <button type="button" data-dropdown="oyunlar" class="nav-link flex items-center gap-1">Oyunlar ▾</button>
                    <div data-dropdown-menu="oyunlar" class="hidden absolute mt-2 w-44 card p-2 z-30">
                        <a href="<?= url('magaza') ?>" class="block nav-link">Towny</a>
                        <a href="<?= url('magaza') ?>" class="block nav-link">SkyBlock</a>
                        <a href="<?= url('magaza') ?>" class="block nav-link">TrapPVP</a>
                    </div>
                </div>
            </div>

            <button type="button" data-mobile-toggle class="lg:hidden nav-link">☰ Menü</button>

            <div class="flex items-center gap-2">
                <?php if ($user instanceof \App\Models\User): ?>
                    <a href="<?= url('kredi-satin-al') ?>" class="hidden sm:flex btn-soft px-3 py-2 rounded-xl font-extrabold items-center gap-2">
                        🛒 <span class="text-brand-600"><?= e(number_format($user->balance, 0, ',', '.')) ?></span>
                    </a>
                    <div class="relative">
                        <button type="button" data-dropdown="user" class="card px-3 py-2 flex items-center gap-2">
                            <img src="<?= e($user->avatarUrl(32)) ?>" class="w-7 h-7 rounded-lg" alt="">
                            <span class="text-left">
                                <span class="block text-xs text-[#a8906a] leading-none"><?= e($user->roleLabel()) ?></span>
                                <span class="block font-extrabold text-[#5b4a36] leading-tight"><?= e($user->username) ?></span>
                            </span>
                        </button>
                        <div data-dropdown-menu="user" class="hidden absolute right-0 mt-2 w-52 card p-2 z-30">
                            <a href="<?= url('profil') ?>" class="block nav-link">Profilim</a>
                            <a href="<?= url('profil/siparisler') ?>" class="block nav-link">Siparişlerim</a>
                            <a href="<?= url('profil/kredi-gecmisi') ?>" class="block nav-link">Kredi Geçmişi</a>
                            <?php if ($user->isAdmin()): ?>
                                <a href="<?= url('yonetim') ?>" class="block nav-link text-brand-600">Yönetim Paneli</a>
                            <?php endif; ?>
                            <form method="post" action="<?= url('cikis') ?>" class="mt-1">
                                <?= csrf_field() ?>
                                <button class="w-full text-left nav-link text-red-500">Çıkış Yap</button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= url('giris') ?>" class="btn-soft px-4 py-2 rounded-xl font-extrabold flex items-center gap-1">👤 Giriş Yap</a>
                    <a href="<?= url('kayit') ?>" class="btn-brand px-4 py-2 rounded-xl font-extrabold">Kayıt Ol</a>
                <?php endif; ?>
            </div>
        </nav>

        <div id="mobile-nav" class="hidden lg:hidden card mt-2 p-2">
            <?php foreach ($navItems as [$href, $label]): ?>
                <a href="<?= url(ltrim($href, '/')) ?>" class="block nav-link <?= $isActive($href) ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</header>
