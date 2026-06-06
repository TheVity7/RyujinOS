<?php
/** @var \App\Core\View $self */
$self->layout('app');
?>
<div class="max-w-3xl mx-auto px-4 py-20 text-center">
    <div class="text-7xl font-black text-brand-500">404</div>
    <h1 class="text-2xl font-black text-[#4a3a26] mt-4">Sayfa Bulunamadı</h1>
    <p class="text-[#8a755a] mt-2">Aradığınız sayfa taşınmış veya hiç var olmamış olabilir.</p>
    <a href="<?= url('/') ?>" class="inline-block btn-brand px-6 py-3 rounded-xl font-extrabold mt-6">Anasayfaya Dön</a>
</div>
