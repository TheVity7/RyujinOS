<?php
/** @var string $siteName */
use App\Models\Setting;
use App\Models\User;
use App\Services\MinecraftService;

$serverIp = Setting::get('server_ip', 'play.ryujinos.net');
$status = MinecraftService::status();
$registered = User::count();
$discordMembers = Setting::get('discord_members', '0');
$version = $status['version'] ?: '1.21.x';
?>
<footer class="relative z-10 mt-12">
    <div class="max-w-6xl mx-auto px-4">
        <div class="rounded-3xl overflow-hidden shadow-lg">
            <div class="btn-brand px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3 text-white">
                    <img src="https://mc-heads.net/avatar/MHF_Steve/40" class="w-9 h-9 rounded-lg" alt="">
                    <div>
                        <div class="font-black tracking-wide"><?= e(strtoupper($serverIp)) ?></div>
                        <div class="text-xs opacity-90">IP'yi kopyalamak için tıkla</div>
                    </div>
                </div>
                <button type="button" data-copy="<?= e($serverIp) ?>" class="bg-white/20 hover:bg-white/30 transition w-10 h-10 rounded-xl grid place-items-center text-white">📋</button>
            </div>
            <div class="bg-white grid grid-cols-2 md:grid-cols-4 divide-x divide-[#f3e4c4]">
                <?php
                $stats = [
                    [$status['players'], 'ÇEVRİMİÇİ OYUNCU'],
                    [$registered, 'KAYITLI OYUNCU'],
                    [$discordMembers, 'DISCORD AKTİF'],
                    [$version, 'SÜRÜM'],
                ];
                foreach ($stats as [$value, $label]): ?>
                    <div class="py-5 text-center">
                        <div class="text-2xl font-black text-[#4a3a26]"><?= e((string) $value) ?></div>
                        <div class="text-[11px] font-extrabold text-[#b08a4f] tracking-wider mt-1"><?= e($label) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 py-12">
            <div class="md:col-span-1">
                <div class="flex items-center gap-2 mb-3">
                    <img src="https://mc-heads.net/avatar/MHF_Steve/32" class="w-8 h-8 rounded-lg" alt="">
                    <span class="font-black text-lg text-[#4a3a26]"><?= e($siteName) ?></span>
                </div>
                <p class="text-sm text-[#8a755a] leading-relaxed"><?= e(Setting::get('site_description')) ?></p>
            </div>
            <div>
                <h4 class="font-black text-[#4a3a26] mb-3">Hızlı Menü</h4>
                <ul class="space-y-2 text-sm text-[#8a755a] font-bold">
                    <li><a href="<?= url('/') ?>" class="hover:text-brand-600">Anasayfa</a></li>
                    <li><a href="<?= url('magaza') ?>" class="hover:text-brand-600">Mağaza</a></li>
                    <li><a href="<?= url('blog') ?>" class="hover:text-brand-600">Blog</a></li>
                    <li><a href="<?= url('destek') ?>" class="hover:text-brand-600">Destek</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-black text-[#4a3a26] mb-3">Sosyal Medya</h4>
                <ul class="space-y-2 text-sm text-[#8a755a] font-bold">
                    <li><a href="<?= e(Setting::get('social_facebook', '#')) ?>" class="hover:text-brand-600">Facebook</a></li>
                    <li><a href="<?= e(Setting::get('social_instagram', '#')) ?>" class="hover:text-brand-600">Instagram</a></li>
                    <li><a href="<?= e(Setting::get('social_youtube', '#')) ?>" class="hover:text-brand-600">YouTube</a></li>
                    <li><a href="<?= e(Setting::get('discord_invite', '#')) ?>" class="hover:text-brand-600">Discord</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-black text-[#4a3a26] mb-3">Bağlantılar</h4>
                <ul class="space-y-2 text-sm text-[#8a755a] font-bold">
                    <li><a href="#" class="hover:text-brand-600">Kurallar</a></li>
                    <li><a href="#" class="hover:text-brand-600">Hizmet Şartları</a></li>
                    <li><a href="#" class="hover:text-brand-600">Gizlilik Politikası</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-[#f0ddb8] py-6 flex flex-col md:flex-row items-center justify-between gap-3 text-sm text-[#a8906a]">
            <span>Tüm hakları saklıdır. © <?= date('Y') ?> <?= e($siteName) ?></span>
            <span class="flex items-center gap-2">
                <span class="badge bg-[#fbeccc] text-brand-700">Powered by RyujinOS</span>
            </span>
        </div>
    </div>
</footer>
