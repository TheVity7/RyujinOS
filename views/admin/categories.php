<?php
/** @var \App\Core\View $self */
/** @var list<\App\Models\Category> $categories */
$self->layout('admin');
?>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-[#fbf3e2]">
                <tr class="text-left text-[#b08a4f]">
                    <th class="px-4 py-3 font-extrabold">Kategori</th>
                    <th class="px-4 py-3 font-extrabold">Slug</th>
                    <th class="px-4 py-3 font-extrabold">Sıra</th>
                    <th class="px-4 py-3 font-extrabold text-right">İşlem</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#f3e4c4]">
                <?php foreach ($categories as $c): ?>
                <tr>
                    <td class="px-4 py-3 font-bold text-[#4a3a26]"><?= e($c->icon ? $c->icon . ' ' : '') ?><?= e($c->name) ?></td>
                    <td class="px-4 py-3 text-[#8a755a]"><?= e($c->slug) ?></td>
                    <td class="px-4 py-3 text-[#8a755a]"><?= $c->sort_order ?></td>
                    <td class="px-4 py-3 text-right">
                        <form method="post" action="<?= url('yonetim/kategoriler/' . $c->id . '/sil') ?>" class="inline" onsubmit="return confirm('Kategoriyi silmek istediğinize emin misiniz?')">
                            <?= csrf_field() ?>
                            <button class="text-red-500 font-bold">Sil</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if ($categories === []): ?>
                <tr><td colspan="4" class="px-4 py-8 text-center text-[#8a755a]">Henüz kategori yok.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card p-6 h-fit">
        <h3 class="font-black text-[#4a3a26] mb-4">Yeni Kategori</h3>
        <form method="post" action="<?= url('yonetim/kategoriler') ?>" class="space-y-3">
            <?= csrf_field() ?>
            <div><label class="label">Ad</label><input type="text" name="name" class="input" required></div>
            <div><label class="label">İkon (emoji)</label><input type="text" name="icon" class="input" placeholder="👑"></div>
            <div><label class="label">Açıklama</label><input type="text" name="description" class="input"></div>
            <div><label class="label">Sıralama</label><input type="number" name="sort_order" class="input" value="0"></div>
            <button class="btn-brand w-full py-2.5 rounded-xl font-extrabold text-white">Ekle</button>
        </form>
    </div>
</div>
