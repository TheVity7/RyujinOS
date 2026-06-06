<?php
/** @var \App\Core\View $self */
/** @var array $groups */
/** @var array<string,string> $settings */
$self->layout('admin');
?>
<form method="post" action="<?= url('yonetim/ayarlar') ?>" class="space-y-6 max-w-3xl">
    <?= csrf_field() ?>
    <?php foreach ($groups as $groupName => $fields): ?>
    <div class="card p-6">
        <h3 class="font-black text-[#4a3a26] mb-4"><?= e($groupName) ?></h3>
        <div class="space-y-4">
            <?php foreach ($fields as $key => [$label, $type]): ?>
            <div>
                <label class="label"><?= e($label) ?></label>
                <?php if ($type === 'textarea'): ?>
                    <textarea name="<?= e($key) ?>" rows="3" class="input"><?= e($settings[$key] ?? '') ?></textarea>
                <?php else: ?>
                    <input type="text" name="<?= e($key) ?>" class="input" value="<?= e($settings[$key] ?? '') ?>">
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <button type="submit" class="btn-brand px-8 py-3 rounded-xl font-extrabold text-white">Ayarları Kaydet</button>
</form>
