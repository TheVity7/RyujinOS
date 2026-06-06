<?php
/** @var \App\Core\View $self */
/** @var \App\Models\Category $category */
/** @var list<\App\Models\Product> $products */
/** @var list<\App\Models\Category> $categories */
$self->layout('app');
?>
<div class="max-w-6xl mx-auto px-4 mt-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <aside class="lg:col-span-1">
            <div class="card p-4">
                <div class="text-xs font-extrabold text-[#b08a4f] tracking-wide mb-3">KATEGORİLER</div>
                <nav class="space-y-1">
                    <?php foreach ($categories as $c): ?>
                        <a href="<?= url('magaza/' . $c->slug) ?>" class="block nav-link <?= $c->id === $category->id ? 'active' : '' ?>"><?= e($c->name) ?></a>
                    <?php endforeach; ?>
                </nav>
            </div>
        </aside>

        <div class="lg:col-span-3">
            <div class="card p-6 mb-6">
                <h1 class="text-2xl font-black text-[#4a3a26]"><?= e($category->name) ?></h1>
                <?php if ($category->description): ?>
                    <p class="text-[#8a755a] mt-1"><?= e($category->description) ?></p>
                <?php endif; ?>
            </div>

            <?php if ($products === []): ?>
                <div class="card p-10 text-center text-[#8a755a]">Bu kategoride ürün yok.</div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <?php foreach ($products as $product): ?>
                        <?php $self->partial('partials/product-card', ['product' => $product]); ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
