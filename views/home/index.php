<?php
/** @var \App\Core\View $self */
/** @var list<\App\Models\Post> $posts */
/** @var array|null $topLoader */
/** @var array $recentLoaders */
/** @var array|null $topDonators */
/** @var array $recentOrders */
/** @var int $page */
/** @var int $totalPages */
use App\Models\Setting;

$self->layout('app');

$heroTitle = Setting::get('hero_title');
$heroSubtitle = Setting::get('hero_subtitle');
$heroImage = Setting::get('hero_image');
$discordInvite = Setting::get('discord_invite', '#');
$discordMembers = Setting::get('discord_members', '0');
$gamingNight = Setting::get('gaming_night');
$featured = $posts[0] ?? null;
$rest = array_slice($posts, 1);
?>
<div class="max-w-6xl mx-auto px-4 mt-6">

    <!-- HERO -->
    <div class="relative rounded-3xl overflow-hidden shadow-xl min-h-[260px] flex">
        <img src="<?= e($heroImage) ?>" alt="" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/55 to-black/10"></div>
        <div class="relative p-8 md:p-10 max-w-xl text-white self-center">
            <span class="badge bg-brand-500 text-white">SEZON 3 AKTİF</span>
            <h1 class="text-3xl md:text-4xl font-black mt-3 leading-tight"><?= e($heroTitle) ?></h1>
            <p class="mt-3 text-white/85 font-semibold"><?= e($heroSubtitle) ?></p>
            <div class="flex flex-wrap gap-3 mt-5">
                <button data-copy="<?= e(Setting::get('server_ip')) ?>" class="btn-brand px-5 py-2.5 rounded-xl font-extrabold">▶ <span data-copy-label>Hemen Oyna</span></button>
                <a href="<?= url('magaza') ?>" class="bg-white/15 hover:bg-white/25 transition px-5 py-2.5 rounded-xl font-extrabold backdrop-blur">Mağaza →</a>
            </div>
        </div>
    </div>

    <!-- QUICK CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-5">
        <?php
        $quick = [
            ['👍', 'Oy Ver', 'Sunucumuza oy ver, ödül kazan', url('/'), 'bg-white'],
            ['🎮', 'Discord', 'Topluluğumuza Katıl', $discordInvite, 'text-white', '#5865F2'],
            ['❓', 'Destek', 'Destek talebi oluşturmak için tıkla', url('destek'), 'bg-white'],
        ];
        foreach ($quick as $q):
            $bg = isset($q[5]) ? 'style="background:' . $q[5] . '"' : '';
            $textClass = isset($q[5]) ? 'text-white' : '';
        ?>
        <a href="<?= e($q[3]) ?>" class="card p-4 flex items-center gap-3 hover:shadow-lg transition <?= $textClass ?>" <?= $bg ?>>
            <span class="w-11 h-11 rounded-xl grid place-items-center text-xl <?= isset($q[5]) ? 'bg-white/20' : 'btn-soft' ?>"><?= $q[0] ?></span>
            <span>
                <span class="block font-extrabold <?= isset($q[5]) ? '' : 'text-[#4a3a26]' ?>"><?= e($q[1]) ?></span>
                <span class="block text-xs <?= isset($q[5]) ? 'opacity-90' : 'text-[#a8906a]' ?>"><?= e($q[2]) ?></span>
            </span>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- BLOG + SIDEBAR -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-black text-[#4a3a26]">Blog</h2>
                <a href="<?= url('blog') ?>" class="text-sm font-extrabold text-brand-600 hover:text-brand-700">Tümünü Gör →</a>
            </div>

            <?php if ($featured !== null): ?>
            <a href="<?= url('blog/' . $featured->slug) ?>" class="card overflow-hidden grid sm:grid-cols-2 mb-5 hover:shadow-lg transition">
                <div class="relative h-48 sm:h-full bg-[#e9d9b6]">
                    <?php if ($featured->imageUrl()): ?>
                        <img src="<?= e($featured->imageUrl()) ?>" class="absolute inset-0 w-full h-full object-cover" alt="">
                    <?php else: ?>
                        <div class="absolute inset-0 grid place-items-center text-5xl">📰</div>
                    <?php endif; ?>
                    <span class="badge bg-brand-500 text-white absolute top-3 left-3">DUYURU</span>
                </div>
                <div class="p-5">
                    <div class="text-xs text-[#a8906a] font-bold"><?= e(date('d M Y H:i', strtotime($featured->created_at))) ?></div>
                    <h3 class="text-lg font-black text-[#4a3a26] mt-1"><?= e($featured->title) ?></h3>
                    <p class="text-sm text-[#8a755a] mt-2 line-clamp-3"><?= e($featured->excerpt ?? '') ?></p>
                    <span class="inline-block btn-brand px-4 py-2 rounded-xl font-extrabold text-sm mt-4">Devamını Oku</span>
                </div>
            </a>
            <?php endif; ?>

            <div class="grid sm:grid-cols-2 gap-5">
                <?php foreach ($rest as $post): ?>
                <a href="<?= url('blog/' . $post->slug) ?>" class="card overflow-hidden hover:shadow-lg transition">
                    <div class="relative h-40 bg-[#1f2433]">
                        <?php if ($post->imageUrl()): ?>
                            <img src="<?= e($post->imageUrl()) ?>" class="absolute inset-0 w-full h-full object-cover" alt="">
                        <?php else: ?>
                            <div class="absolute inset-0 grid place-items-center text-white/30 text-4xl">🖼️</div>
                        <?php endif; ?>
                        <span class="badge bg-brand-500 text-white absolute top-3 left-3">Duyuru</span>
                    </div>
                    <div class="p-4">
                        <div class="text-xs text-[#a8906a] font-bold"><?= e(date('d M Y H:i', strtotime($post->created_at))) ?></div>
                        <h3 class="font-black text-[#4a3a26] mt-1"><?= e($post->title) ?></h3>
                        <p class="text-sm text-[#8a755a] mt-1 line-clamp-2"><?= e($post->excerpt ?? '') ?></p>
                        <div class="flex items-center justify-between mt-3">
                            <span class="text-xs text-[#b08a4f] font-bold">👁 <?= $post->views ?> &nbsp; 💬 <?= $post->comments ?></span>
                            <span class="text-xs font-extrabold text-brand-600">Devamını Oku →</span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
            <div class="flex items-center justify-center gap-2 mt-6">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?= url('?sayfa=' . $i) ?>" class="w-9 h-9 grid place-items-center rounded-lg font-extrabold <?= $i === $page ? 'btn-brand' : 'btn-soft' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- SIDEBAR -->
        <aside class="space-y-5">
            <?php if ($topLoader !== null): ?>
            <div class="card p-4">
                <div class="text-xs font-extrabold text-[#b08a4f] tracking-wide mb-3 flex justify-between"><span>🏆 En Çok Kredi Yükleyen</span><span class="text-[#cbb488]">BU AY</span></div>
                <div class="btn-brand rounded-2xl p-4 flex items-center gap-3 text-white">
                    <img src="<?= e($topLoader['avatar'] ?? mc_avatar($topLoader['username'], 56)) ?>" class="w-12 h-12 rounded-xl" alt="">
                    <div>
                        <div class="font-black text-lg"><?= e($topLoader['username']) ?></div>
                        <div class="text-sm opacity-90"><?= credits($topLoader['total']) ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="card p-4">
                <div class="text-xs font-extrabold text-[#b08a4f] tracking-wide mb-3 flex justify-between"><span>💰 Son Kredi Yükleyenler</span><span class="text-[#cbb488]">BU AY</span></div>
                <div class="space-y-2">
                    <?php foreach ($recentLoaders as $row): ?>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <img src="<?= e($row['avatar'] ?? mc_avatar($row['username'], 32)) ?>" class="w-8 h-8 rounded-lg" alt="">
                            <span class="font-extrabold text-[#5b4a36] text-sm"><?= e($row['username']) ?></span>
                        </div>
                        <span class="badge bg-[#fbeccc] text-brand-700"><?= credits($row['amount']) ?></span>
                    </div>
                    <?php endforeach; ?>
                    <?php if ($recentLoaders === []): ?><p class="text-sm text-[#a8906a]">Henüz kayıt yok.</p><?php endif; ?>
                </div>
            </div>

            <?php if ($topDonators !== null): ?>
            <div class="card p-4">
                <div class="text-xs font-extrabold text-[#b08a4f] tracking-wide mb-3 flex justify-between"><span>👑 En Çok Bağış Yapanlar</span><span class="text-[#cbb488]">TÜM ZAMANLAR</span></div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <img src="<?= e($topDonators['avatar'] ?? mc_avatar($topDonators['username'], 32)) ?>" class="w-8 h-8 rounded-lg" alt="">
                        <span class="font-extrabold text-[#5b4a36] text-sm"><?= e($topDonators['username']) ?></span>
                    </div>
                    <span class="badge bg-[#fbeccc] text-brand-700"><?= credits($topDonators['total']) ?></span>
                </div>
            </div>
            <?php endif; ?>

            <div class="card p-4">
                <div class="text-xs font-extrabold text-[#b08a4f] tracking-wide mb-3">🛍️ Son Mağaza Alışverişleri</div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-[10px] text-[#b08a4f] text-left uppercase">
                            <th class="pb-2 font-extrabold">Kullanıcı</th>
                            <th class="pb-2 font-extrabold">Kategori</th>
                            <th class="pb-2 font-extrabold">Ürün</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f3e4c4]">
                        <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td class="py-2 font-bold text-[#5b4a36]"><?= e($order['username']) ?></td>
                            <td class="py-2 text-[#8a755a]"><?= e($order['category'] ?? '-') ?></td>
                            <td class="py-2 text-[#8a755a]"><?= e($order['product_name']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if ($recentOrders === []): ?>
                        <tr><td colspan="3" class="py-2 text-[#a8906a]">Henüz alışveriş yok.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <a href="<?= url('magaza') ?>" class="block text-center btn-soft mt-3 py-2 rounded-xl font-extrabold text-sm">Mağazaya Git</a>
            </div>

            <a href="<?= e($discordInvite) ?>" target="_blank" rel="noopener" class="block rounded-2xl p-5 text-white" style="background:#5865F2">
                <div class="flex items-center gap-2 font-black text-lg">🎮 Discord Topluluğu</div>
                <p class="text-sm opacity-90 mt-1">Haberler, destek ve eğlence için Discord'umuza katıl</p>
                <div class="mt-3 text-sm opacity-90">👥 <?= e($discordMembers) ?> üye çevrimiçi</div>
                <div class="mt-3 bg-white/15 rounded-xl text-center py-2 font-extrabold">Discord'a Katıl</div>
            </a>

            <?php if ($gamingNight !== ''): ?>
            <div class="btn-brand rounded-2xl p-5 text-white">
                <div class="font-black text-lg">⏰ Gaming Gecesi</div>
                <p class="text-sm opacity-90 mt-1">Gaming Gecesi'ne özel indirimli ürünler</p>
                <div class="grid grid-cols-4 gap-2 mt-4" data-countdown="<?= e(str_replace(' ', 'T', $gamingNight)) ?>">
                    <?php foreach (['d' => 'GÜN', 'h' => 'SAAT', 'm' => 'DK', 's' => 'SN'] as $k => $lbl): ?>
                    <div class="bg-white/15 rounded-xl py-2 text-center">
                        <div class="text-xl font-black" data-<?= $k ?>>00</div>
                        <div class="text-[10px] opacity-80"><?= $lbl ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <a href="<?= url('magaza') ?>" class="block bg-white/15 rounded-xl text-center py-2 font-extrabold mt-4">Gaming Gecesine Git</a>
            </div>
            <?php endif; ?>
        </aside>
    </div>
</div>
