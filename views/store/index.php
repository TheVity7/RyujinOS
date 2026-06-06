<?php
/** @var \App\Core\View $self */
/** @var list<\App\Models\Category> $categories */
/** @var array<int,list<\App\Models\Product>> $grouped */
/** @var list<\App\Models\Product> $featured */
$self->layout('app');
?>
<div class="max-w-6xl mx-auto px-4 mt-8">
    <div class="card p-6 mb-6">
        <h1 class="text-2xl font-black text-[#4a3a26]">Mağaza</h1>
        <p class="text-[#8a755a] mt-1">Kredi ile VIP, anahtar, spawner ve daha fazlasını satın al. Ürünler oyuna anında teslim edilir.</p>
    </div>

    <?php if ($categories === []): ?>
        <div class="card p-10 text-center text-[#8a755a]">Henüz ürün eklenmemiş.</div>
    <?php endif; ?>

    <?php foreach ($categories as $category): ?>
        <?php $products = $grouped[$category->id] ?? []; ?>
        <?php if ($products === []) continue; ?>
        <section class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-black text-[#4a3a26]">
                    <?= e($category->name) ?>
                </h2>
                <a href="<?= url('magaza/' . $category->slug) ?>" class="text-sm font-extrabold text-brand-600 hover:text-brand-700">Tümünü Gör →</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <?php foreach (array_slice($products, 0, 4) as $product): ?>
                    <?php $self->partial('partials/product-card', ['product' => $product]); ?>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
</div>
