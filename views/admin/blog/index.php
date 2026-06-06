<?php
/** @var \App\Core\View $self */
/** @var list<\App\Models\Post> $posts */
$self->layout('admin');
?>
<div class="flex items-center justify-between mb-5">
    <p class="text-[#8a755a]"><?= count($posts) ?> yazı</p>
    <a href="<?= url('yonetim/blog/yeni') ?>" class="btn-brand px-5 py-2.5 rounded-xl font-extrabold text-white">+ Yeni Yazı</a>
</div>
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-[#fbf3e2]">
            <tr class="text-left text-[#b08a4f]">
                <th class="px-4 py-3 font-extrabold">Başlık</th>
                <th class="px-4 py-3 font-extrabold">Yazar</th>
                <th class="px-4 py-3 font-extrabold">Görüntülenme</th>
                <th class="px-4 py-3 font-extrabold">Durum</th>
                <th class="px-4 py-3 font-extrabold">Tarih</th>
                <th class="px-4 py-3 font-extrabold text-right">İşlem</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-[#f3e4c4]">
            <?php foreach ($posts as $p): ?>
            <tr class="hover:bg-[#fbf7ec]">
                <td class="px-4 py-3 font-bold text-[#4a3a26]"><?= e($p->title) ?></td>
                <td class="px-4 py-3 text-[#8a755a]"><?= e($p->author) ?></td>
                <td class="px-4 py-3 text-[#8a755a]"><?= $p->views ?></td>
                <td class="px-4 py-3"><span class="badge <?= $p->published ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500' ?>"><?= $p->published ? 'Yayında' : 'Taslak' ?></span></td>
                <td class="px-4 py-3 text-[#a8906a] text-xs"><?= e(date('d.m.Y', strtotime($p->created_at))) ?></td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                    <a href="<?= url('yonetim/blog/' . $p->id) ?>" class="text-brand-600 font-bold">Düzenle</a>
                    <form method="post" action="<?= url('yonetim/blog/' . $p->id . '/sil') ?>" class="inline" onsubmit="return confirm('Yazıyı silmek istediğinize emin misiniz?')">
                        <?= csrf_field() ?>
                        <button class="text-red-500 font-bold ml-2">Sil</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if ($posts === []): ?>
            <tr><td colspan="6" class="px-4 py-8 text-center text-[#8a755a]">Henüz yazı yok.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
