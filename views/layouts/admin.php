<?php
/** @var \App\Core\View $self */
/** @var string $content */
use App\Core\Auth;
use App\Core\Flash;
use App\Models\Setting;

$siteName = Setting::get('site_name', 'RyujinOS');
$pageTitle = $title ?? 'Yönetim';
$flash = Flash::pull();
$user = Auth::user();
$path = (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$nav = [
    ['/yonetim', '📊', 'Dashboard'],
    ['/yonetim/urunler', '📦', 'Ürünler'],
    ['/yonetim/kategoriler', '🗂️', 'Kategoriler'],
    ['/yonetim/kullanicilar', '👥', 'Kullanıcılar'],
    ['/yonetim/siparisler', '🧾', 'Siparişler'],
    ['/yonetim/blog', '📰', 'Blog'],
    ['/yonetim/ayarlar', '⚙️', 'Ayarlar'],
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — <?= e($siteName) ?> Yönetim</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: {
            50:'#fff7ec',100:'#feecd0',200:'#fdd5a0',300:'#fbb866',400:'#f89a3a',
            500:'#f5921e',600:'#e07c0a',700:'#b9610c',800:'#934c11',900:'#774011' } },
            fontFamily: { sans: ['Nunito','ui-sans-serif','system-ui','sans-serif'] } } } }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="icon" href="https://mc-heads.net/avatar/MHF_Steve/32">
</head>
<body class="bg-[#f7efdf] min-h-screen">
<div class="flex min-h-screen">
    <!-- sidebar -->
    <aside class="w-64 bg-[#2a2118] text-white flex-col hidden lg:flex">
        <div class="p-5 border-b border-white/10">
            <a href="<?= url('/') ?>" class="flex items-center gap-2">
                <img src="https://mc-heads.net/avatar/MHF_Steve/36" class="rounded-lg" alt="">
                <span class="font-black text-lg"><?= e($siteName) ?></span>
            </a>
            <span class="text-xs text-brand-400 font-bold">Yönetim Paneli</span>
        </div>
        <nav class="flex-1 p-3 space-y-1">
            <?php foreach ($nav as [$href, $icon, $label]):
                $active = $href === '/yonetim' ? $path === $href : str_starts_with($path, $href);
            ?>
            <a href="<?= url(ltrim($href, '/')) ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-sm transition <?= $active ? 'bg-brand-500 text-white' : 'text-white/70 hover:bg-white/10' ?>">
                <span><?= $icon ?></span><?= e($label) ?>
            </a>
            <?php endforeach; ?>
        </nav>
        <div class="p-3 border-t border-white/10">
            <a href="<?= url('profil') ?>" class="flex items-center gap-2 px-3 py-2 text-sm text-white/70 hover:text-white">← Siteye Dön</a>
            <form method="post" action="<?= url('cikis') ?>"><?= csrf_field() ?>
                <button class="w-full text-left flex items-center gap-2 px-3 py-2 text-sm text-red-300 hover:text-red-200">⏻ Çıkış Yap</button>
            </form>
        </div>
    </aside>

    <!-- main -->
    <div class="flex-1 min-w-0">
        <header class="bg-white border-b border-[#ecdcbb] px-6 py-4 flex items-center justify-between sticky top-0 z-20">
            <h1 class="font-black text-[#4a3a26] text-lg"><?= e($pageTitle) ?></h1>
            <div class="flex items-center gap-3">
                <img src="<?= e($user->avatarUrl(32)) ?>" class="w-8 h-8 rounded-lg" alt="">
                <span class="font-bold text-sm text-[#4a3a26]"><?= e($user->username) ?></span>
            </div>
        </header>

        <?php if ($flash !== []): ?>
        <div class="px-6 mt-4 space-y-2">
            <?php foreach ($flash as $type => $messages): foreach ($messages as $message):
                $colors = ['success'=>'bg-green-50 text-green-700 border-green-200','error'=>'bg-red-50 text-red-600 border-red-200','info'=>'bg-blue-50 text-blue-600 border-blue-200'][$type] ?? 'bg-gray-50 text-gray-700 border-gray-200';
            ?>
            <div data-flash class="border <?= $colors ?> rounded-xl px-4 py-3 text-sm font-bold"><?= e($message) ?></div>
            <?php endforeach; endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="p-6"><?= $content ?></div>
    </div>
</div>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
