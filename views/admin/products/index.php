<?php
/** @var \App\Core\View $self */
/** @var list<\App\Models\Product> $products */
/** @var array<int,string> $categories */
$self->layout('admin');
?>
<div class="flex items-center justify-between mb-5">
    <p class="text-[#8a755a]"><?= count($products) ?> ürün</p>
    <a href="<?= url('yonetim/urunler/yeni') ?>" class="btn-brand px-5 py-2.5 rounded-xl font-extrabold text-white">+ Yeni Ürün</a>
</div>
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-[#fbf3e2]">
            <tr class="text-left text-[#b08a4f]">
                <th class="px-4 py-3 font-extrabold">Ürün</th>
                <th class="px-4 py-3 font-extrabold">Kategori</th>
                <th class="px-4 py-3 font-extrabold">Fiyat</th>
                <th class="px-4 py-3 font-extrabold">Stok</th>
                <th class="px-4 py-3 font-extrabold">Durum</th>
                <th class="px-4 py-3 font-extrabold text-right">İşlem</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-[#f3e4c4]">
            <?php foreach ($products as $p): ?>
            <tr class="hover:bg-[#fbf7ec]">
                <td class="px-4 py-3 font-bold text-[#4a3a26]"><?= e($p->name) ?></td>
                <td class="px-4 py-3 text-[#8a755a]"><?= e($categories[$p->category_id] ?? '—') ?></td>
                <td class="px-4 py-3 text-[#4a3a26]">
                    <?= credits($p->effectivePrice()) ?>
                    <?php if ($p->hasDiscount()): ?><span class="line-through text-[#bba] text-xs ml-1"><?= credits($p->price) ?></span><?php endif; ?>
                </td>
                <td class="px-4 py-3 text-[#8a755a]"><?= $p->stock === -1 ? '∞' : $p->stock ?></td>
                <td class="px-4 py-3">
                    <span class="badge <?= $p->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500' ?>"><?= $p->is_active ? 'Aktif' : 'Pasif' ?></span>
                    <?php if ($p->featured): ?><span class="badge bg-brand-100 text-brand-700">Öne Çıkan</span><?php endif; ?>
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                    <a href="<?= url('yonetim/urunler/' . $p->id) ?>" class="text-brand-600 font-bold">Düzenle</a>
                    <form method="post" action="<?= url('yonetim/urunler/' . $p->id . '/sil') ?>" class="inline" onsubmit="return confirm('Bu ürünü silmek istediğinize emin misiniz?')">
                        <?= csrf_field() ?>
                        <button class="text-red-500 font-bold ml-2">Sil</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if ($products === []): ?>
            <tr><td colspan="6" class="px-4 py-8 text-center text-[#8a755a]">Henüz ürün yok.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
