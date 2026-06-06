<?php
/** @var \App\Core\View $self */
/** @var \App\Models\Product $product */
/** @var \App\Models\Category|null $category */
use App\Core\Auth;
$user = Auth::user();
$self->layout('app');
?>
<div class="max-w-5xl mx-auto px-4 mt-8">
    <nav class="text-sm text-[#a8906a] font-bold mb-4">
        <a href="<?= url('magaza') ?>" class="hover:text-brand-600">Mağaza</a>
        <?php if ($category): ?> / <a href="<?= url('magaza/' . $category->slug) ?>" class="hover:text-brand-600"><?= e($category->name) ?></a><?php endif; ?>
        / <span class="text-[#5b4a36]"><?= e($product->name) ?></span>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card overflow-hidden">
            <div class="relative h-72 bg-gradient-to-br from-[#2a3142] to-[#1a1f2c]">
                <img src="<?= e($product->imageUrl()) ?>" class="absolute inset-0 w-full h-full object-cover" alt="" onerror="this.style.display='none'">
                <div class="absolute inset-0 grid place-items-center text-white/40 text-7xl">📦</div>
            </div>
        </div>

        <div class="card p-6 flex flex-col">
            <?php if ($category): ?><span class="badge bg-[#fbeccc] text-brand-700 w-fit"><?= e($category->name) ?></span><?php endif; ?>
            <h1 class="text-2xl font-black text-[#4a3a26] mt-3"><?= e($product->name) ?></h1>

            <div class="flex items-baseline gap-2 mt-3">
                <span class="text-3xl font-black text-brand-600"><?= credits($product->effectivePrice()) ?></span>
                <?php if ($product->hasDiscount()): ?>
                    <span class="text-[#b8a47f] line-through"><?= credits($product->price) ?></span>
                <?php endif; ?>
            </div>

            <p class="text-[#8a755a] mt-4 leading-relaxed flex-1"><?= nl2br(e($product->description ?? '')) ?></p>

            <div class="mt-6">
                <?php if ($user === null): ?>
                    <a href="<?= url('giris') ?>" class="btn-brand block text-center py-3 rounded-xl font-extrabold">Satın almak için giriş yap</a>
                <?php else: ?>
                    <div class="flex items-center justify-between text-sm mb-3 bg-[#f6ecd9] rounded-xl px-4 py-2.5">
                        <span class="text-[#8a755a] font-bold">Bakiyen</span>
                        <span class="font-black text-brand-600"><?= credits($user->balance) ?></span>
                    </div>
                    <?php if ($user->balance < $product->effectivePrice()): ?>
                        <a href="<?= url('kredi-satin-al') ?>" class="btn-brand block text-center py-3 rounded-xl font-extrabold">Yetersiz bakiye — Kredi Yükle</a>
                    <?php else: ?>
                        <form method="post" action="<?= url('satin-al/' . $product->id) ?>"
                              onsubmit="return confirm('<?= e($product->name) ?> ürününü <?= credits($product->effectivePrice()) ?> karşılığında satın almak istediğine emin misin?');">
                            <?= csrf_field() ?>
                            <button class="btn-brand w-full py-3 rounded-xl font-extrabold">🛒 Kredi ile Satın Al</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
                <div class="text-xs text-[#a8906a] mt-3 text-center">Ürün, oyundaki hesabına otomatik olarak teslim edilir.</div>
            </div>
        </div>
    </div>
</div>
