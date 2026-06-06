<?php
/** @var \App\Core\View $self */
/** @var \App\Models\Post $post */
/** @var list<\App\Models\Post> $recent */
$self->layout('app');
?>
<div class="max-w-6xl mx-auto px-4 mt-8">
    <div class="text-sm text-[#a8906a] mb-4">
        <a href="<?= url('/') ?>" class="hover:text-brand-600">Anasayfa</a> /
        <a href="<?= url('blog') ?>" class="hover:text-brand-600">Blog</a> /
        <span class="text-[#5b4a36]"><?= e($post->title) ?></span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <article class="lg:col-span-2 card overflow-hidden">
            <?php if ($post->imageUrl()): ?>
                <img src="<?= e($post->imageUrl()) ?>" class="w-full h-64 object-cover" alt="">
            <?php endif; ?>
            <div class="p-6 md:p-8">
                <div class="text-xs text-[#a8906a] font-bold">
                    <?= e(date('d M Y H:i', strtotime($post->created_at))) ?> · ✍ <?= e($post->author) ?> · 👁 <?= $post->views ?>
                </div>
                <h1 class="text-2xl md:text-3xl font-black text-[#4a3a26] mt-2"><?= e($post->title) ?></h1>
                <div class="prose prose-sm max-w-none mt-5 text-[#5b4a36] leading-relaxed">
                    <?= nl2br(e($post->body)) ?>
                </div>
            </div>
        </article>

        <aside class="space-y-5">
            <div class="card p-5">
                <h3 class="font-black text-[#4a3a26] mb-3">Son Yazılar</h3>
                <div class="space-y-3">
                    <?php foreach ($recent as $r): ?>
                    <a href="<?= url('blog/' . $r->slug) ?>" class="flex gap-3 items-center group">
                        <div class="w-14 h-14 rounded-xl bg-[#e9d9b6] overflow-hidden shrink-0 grid place-items-center text-xl">
                            <?php if ($r->imageUrl()): ?><img src="<?= e($r->imageUrl()) ?>" class="w-full h-full object-cover" alt=""><?php else: ?>📰<?php endif; ?>
                        </div>
                        <div>
                            <div class="font-bold text-sm text-[#4a3a26] group-hover:text-brand-600 line-clamp-2"><?= e($r->title) ?></div>
                            <div class="text-xs text-[#a8906a]"><?= e(date('d.m.Y', strtotime($r->created_at))) ?></div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>
    </div>
</div>
