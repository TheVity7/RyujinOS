<?php
/** @var \App\Core\View $self */
/** @var string $content */
use App\Core\Auth;
use App\Core\Flash;
use App\Models\Setting;

$siteName = Setting::get('site_name', 'RyujinOS');
$pageTitle = $title ?? $siteName;
$flash = Flash::pull();
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — <?= e($siteName) ?></title>
    <meta name="description" content="<?= e(Setting::get('site_description')) ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff7ec', 100: '#feecd0', 200: '#fdd5a0', 300: '#fbb866',
                            400: '#f89a3a', 500: '#f5921e', 600: '#e07c0a', 700: '#b9610c',
                            800: '#934c11', 900: '#774011',
                        },
                        discord: '#5865F2',
                    },
                    fontFamily: { sans: ['Nunito', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="icon" href="https://mc-heads.net/avatar/MHF_Steve/32">
</head>
<body>
<?php $self->partial('partials/header', ['user' => $user, 'siteName' => $siteName]); ?>

<?php if ($flash !== []): ?>
<div class="max-w-6xl mx-auto px-4 mt-4 space-y-2">
    <?php foreach ($flash as $type => $messages): ?>
        <?php foreach ($messages as $message): ?>
            <?php
            $colors = [
                'success' => 'bg-green-50 text-green-700 border-green-200',
                'error'   => 'bg-red-50 text-red-600 border-red-200',
                'info'    => 'bg-blue-50 text-blue-600 border-blue-200',
            ][$type] ?? 'bg-gray-50 text-gray-700 border-gray-200';
            ?>
            <div data-flash class="border <?= $colors ?> rounded-xl px-4 py-3 text-sm font-bold flex items-center gap-2">
                <?= e($message) ?>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<main class="relative z-10">
    <?= $content ?>
</main>

<?php $self->partial('partials/footer', ['siteName' => $siteName]); ?>

<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
