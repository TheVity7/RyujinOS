<?php
/** @var \App\Core\View $self */
/** @var list<\App\Models\Post> $posts */
/** @var int $page */
/** @var int $totalPages */
$self->layout('app');
?>
<div class="max-w-6xl mx-auto px-4 mt-8">
    <div class="card p-6 mb-6">
        <h1 class="text-2xl font-black text-[#4a3a26]">Blog & Duyurular</h1>
        <p class="text-[#8a755a] mt-1">Sunucu güncellemeleri, etkinlikler ve haberler.</p>
    </div>

    <?php if ($posts === []): ?>
        <div class="card p-10 text-center text-[#8a755a]">Henüz yazı yok.</div>
    <?php else: ?>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php foreach ($posts as $post): ?>
            <a href="<?= url('blog/' . $post->slug) ?>" class="card overflow-hidden hover:shadow-lg transition flex flex-col">
                <div class="relative h-40 bg-[#1f2433]">
                    <?php if ($post->imageUrl()): ?>
                        <img src="<?= e($post->imageUrl()) ?>" class="absolute inset-0 w-full h-full object-cover" alt="">
                    <?php else: ?>
                        <div class="absolute inset-0 grid place-items-center text-4xl">📰</div>
                    <?php endif; ?>
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <div class="text-xs text-[#a8906a] font-bold"><?= e(date('d M Y', strtotime($post->created_at))) ?> · 👁 <?= $post->views ?></div>
                    <h3 class="text-lg font-black text-[#4a3a26] mt-1"><?= e($post->title) ?></h3>
                    <p class="text-sm text-[#8a755a] mt-2 line-clamp-3 flex-1"><?= e($post->excerpt ?? '') ?></p>
                    <span class="inline-block text-brand-600 font-extrabold text-sm mt-3">Devamını Oku →</span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="flex justify-center gap-2 mt-8">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?= url('blog?sayfa=' . $i) ?>" class="w-10 h-10 grid place-items-center rounded-xl font-extrabold <?= $i === $page ? 'btn-brand text-white' : 'card text-[#5b4a36]' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
