<?php
/** @var \App\Models\Product $product */
?>
<div class="card overflow-hidden flex flex-col hover:shadow-lg transition">
    <a href="<?= url('urun/' . $product->slug) ?>" class="block relative h-40 bg-gradient-to-br from-[#2a3142] to-[#1a1f2c]">
        <img src="<?= e($product->imageUrl()) ?>" class="absolute inset-0 w-full h-full object-cover" alt="" onerror="this.style.display='none'">
        <div class="absolute inset-0 grid place-items-center text-white/40 text-5xl">📦</div>
        <?php if ($product->hasDiscount()): ?>
            <span class="badge bg-red-500 text-white absolute top-3 right-3">İNDİRİM</span>
        <?php endif; ?>
        <?php if ($product->featured): ?>
            <span class="badge bg-brand-500 text-white absolute top-3 left-3">ÖNE ÇIKAN</span>
        <?php endif; ?>
    </a>
    <div class="p-4 flex flex-col flex-1">
        <h3 class="font-black text-[#4a3a26]"><?= e($product->name) ?></h3>
        <p class="text-sm text-[#8a755a] mt-1 line-clamp-2 flex-1"><?= e($product->description ?? '') ?></p>
        <div class="flex items-center justify-between mt-4">
            <div>
                <?php if ($product->hasDiscount()): ?>
                    <span class="text-xs text-[#b8a47f] line-through"><?= credits($product->price) ?></span>
                <?php endif; ?>
                <div class="font-black text-brand-600 text-lg"><?= credits($product->effectivePrice()) ?></div>
            </div>
            <a href="<?= url('urun/' . $product->slug) ?>" class="btn-brand px-4 py-2 rounded-xl font-extrabold text-sm">İncele</a>
        </div>
    </div>
</div>
