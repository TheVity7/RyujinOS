<?php
/** @var \App\Core\View $self */
/** @var \App\Models\Post|null $post */
$self->layout('admin');
$p = $post;
$action = $p ? url('yonetim/blog/' . $p->id) : url('yonetim/blog/yeni');
?>
<a href="<?= url('yonetim/blog') ?>" class="text-sm text-brand-600 font-bold">← Yazılara dön</a>
<form method="post" action="<?= $action ?>" class="card p-6 mt-3 space-y-4 max-w-3xl">
    <?= csrf_field() ?>
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="label">Başlık</label>
            <input type="text" name="title" class="input" required value="<?= e($p->title ?? '') ?>">
        </div>
        <div>
            <label class="label">Slug (opsiyonel)</label>
            <input type="text" name="slug" class="input" value="<?= e($p->slug ?? '') ?>" placeholder="otomatik">
        </div>
    </div>
    <div>
        <label class="label">Görsel URL</label>
        <input type="url" name="image" class="input" value="<?= e($p->image ?? '') ?>" placeholder="https://...">
    </div>
    <div>
        <label class="label">Özet</label>
        <textarea name="excerpt" rows="2" class="input"><?= e($p->excerpt ?? '') ?></textarea>
    </div>
    <div>
        <label class="label">İçerik</label>
        <textarea name="body" rows="10" class="input" required><?= e($p->body ?? '') ?></textarea>
    </div>
    <label class="flex items-center gap-2 font-bold text-[#5b4a36]">
        <input type="checkbox" name="published" value="1" class="accent-brand-500" <?= (!$p || $p->published) ? 'checked' : '' ?>> Yayında
    </label>
    <button type="submit" class="btn-brand px-6 py-2.5 rounded-xl font-extrabold text-white"><?= $p ? 'Güncelle' : 'Yayınla' ?></button>
</form>
