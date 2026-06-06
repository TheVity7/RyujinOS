<?php
/** @var \App\Core\View $self */
/** @var \App\Models\Product|null $product */
/** @var list<\App\Models\Category> $categories */
$self->layout('admin');
$p = $product;
$action = $p ? url('yonetim/urunler/' . $p->id) : url('yonetim/urunler/yeni');
?>
<a href="<?= url('yonetim/urunler') ?>" class="text-sm text-brand-600 font-bold">← Ürünlere dön</a>
<form method="post" action="<?= $action ?>" class="card p-6 mt-3 space-y-4 max-w-3xl">
    <?= csrf_field() ?>
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="label">Ürün Adı</label>
            <input type="text" name="name" class="input" required value="<?= e($p->name ?? '') ?>">
        </div>
        <div>
            <label class="label">Slug (opsiyonel)</label>
            <input type="text" name="slug" class="input" value="<?= e($p->slug ?? '') ?>" placeholder="otomatik">
        </div>
        <div>
            <label class="label">Kategori</label>
            <select name="category_id" class="input">
                <?php foreach ($categories as $c): ?>
                <option value="<?= $c->id ?>" <?= ($p && $p->category_id === $c->id) ? 'selected' : '' ?>><?= e($c->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="label">Görsel URL</label>
            <input type="url" name="image" class="input" value="<?= e($p->image ?? '') ?>" placeholder="https://...">
        </div>
        <div>
            <label class="label">Fiyat (Kredi)</label>
            <input type="number" step="0.01" min="0" name="price" class="input" required value="<?= e((string) ($p->price ?? '')) ?>">
        </div>
        <div>
            <label class="label">İndirimli Fiyat (0 = yok)</label>
            <input type="number" step="0.01" min="0" name="sale_price" class="input" value="<?= e((string) ($p->sale_price ?? 0)) ?>">
        </div>
        <div>
            <label class="label">Stok (-1 = sınırsız)</label>
            <input type="number" name="stock" class="input" value="<?= e((string) ($p->stock ?? -1)) ?>">
        </div>
        <div>
            <label class="label">Sıralama</label>
            <input type="number" name="sort_order" class="input" value="<?= e((string) ($p->sort_order ?? 0)) ?>">
        </div>
    </div>
    <div>
        <label class="label">Açıklama</label>
        <textarea name="description" rows="3" class="input"><?= e($p->description ?? '') ?></textarea>
    </div>
    <div>
        <label class="label">RCON Komutları (her satıra bir komut, {player} kullanın)</label>
        <textarea name="commands" rows="3" class="input font-mono text-sm" placeholder="lp user {player} parent add vip&#10;give {player} diamond 5"><?= e($p->commands ?? '') ?></textarea>
    </div>
    <div class="flex gap-6">
        <label class="flex items-center gap-2 font-bold text-[#5b4a36]">
            <input type="checkbox" name="is_active" value="1" class="accent-brand-500" <?= (!$p || $p->is_active) ? 'checked' : '' ?>> Aktif
        </label>
        <label class="flex items-center gap-2 font-bold text-[#5b4a36]">
            <input type="checkbox" name="featured" value="1" class="accent-brand-500" <?= ($p && $p->featured) ? 'checked' : '' ?>> Öne Çıkar
        </label>
    </div>
    <button type="submit" class="btn-brand px-6 py-2.5 rounded-xl font-extrabold text-white"><?= $p ? 'Güncelle' : 'Oluştur' ?></button>
</form>
