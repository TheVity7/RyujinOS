<?php
/** @var \App\Core\View $self */
/** @var array $users */
/** @var string $search */
$self->layout('admin');
?>
<form method="get" action="<?= url('yonetim/kullanicilar') ?>" class="mb-5 flex gap-2 max-w-md">
    <input type="text" name="q" value="<?= e($search) ?>" class="input" placeholder="Kullanıcı adı veya e-posta ara...">
    <button class="btn-brand px-5 rounded-xl font-extrabold text-white">Ara</button>
</form>
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-[#fbf3e2]">
            <tr class="text-left text-[#b08a4f]">
                <th class="px-4 py-3 font-extrabold">Kullanıcı</th>
                <th class="px-4 py-3 font-extrabold">E-posta</th>
                <th class="px-4 py-3 font-extrabold">Rol</th>
                <th class="px-4 py-3 font-extrabold">Bakiye</th>
                <th class="px-4 py-3 font-extrabold">Kayıt</th>
                <th class="px-4 py-3 font-extrabold text-right">İşlem</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-[#f3e4c4]">
            <?php foreach ($users as $u): ?>
            <tr class="hover:bg-[#fbf7ec]">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <img src="<?= e(mc_avatar((string) $u['username'], 28)) ?>" class="w-7 h-7 rounded-lg" alt="">
                        <span class="font-bold text-[#4a3a26]"><?= e($u['username']) ?></span>
                    </div>
                </td>
                <td class="px-4 py-3 text-[#8a755a]"><?= e($u['email'] ?? '—') ?></td>
                <td class="px-4 py-3"><span class="badge <?= $u['role'] === 'admin' ? 'bg-brand-100 text-brand-700' : 'bg-gray-100 text-gray-600' ?>"><?= $u['role'] === 'admin' ? 'Yönetici' : 'Oyuncu' ?></span></td>
                <td class="px-4 py-3 font-black text-brand-600"><?= credits((float) $u['balance']) ?></td>
                <td class="px-4 py-3 text-[#8a755a]"><?= e(date('d.m.Y', strtotime((string) $u['created_at']))) ?></td>
                <td class="px-4 py-3 text-right"><a href="<?= url('yonetim/kullanicilar/' . $u['id']) ?>" class="text-brand-600 font-bold">Yönet</a></td>
            </tr>
            <?php endforeach; ?>
            <?php if ($users === []): ?>
            <tr><td colspan="6" class="px-4 py-8 text-center text-[#8a755a]">Kullanıcı bulunamadı.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
