<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Flash;
use App\Core\Request;
use App\Models\Setting;

final class SettingController extends Controller
{
    /** Editable settings grouped for the admin form. */
    private const FIELDS = [
        'Genel' => [
            'site_name'        => ['Site Adı', 'text'],
            'site_description' => ['Site Açıklaması', 'textarea'],
            'server_ip'        => ['Sunucu IP', 'text'],
            'announcement'     => ['Duyuru Çubuğu', 'text'],
        ],
        'Anasayfa (Hero)' => [
            'hero_title'    => ['Hero Başlık', 'text'],
            'hero_subtitle' => ['Hero Alt Başlık', 'textarea'],
            'hero_image'    => ['Hero Görsel URL', 'text'],
            'gaming_night'  => ['Oyun Gecesi (Y-m-d H:i:s)', 'text'],
        ],
        'Discord & Sosyal' => [
            'discord_invite'   => ['Discord Davet', 'text'],
            'discord_members'  => ['Discord Üye Sayısı', 'text'],
            'social_facebook'  => ['Facebook', 'text'],
            'social_instagram' => ['Instagram', 'text'],
            'social_x'         => ['X (Twitter)', 'text'],
            'social_youtube'   => ['YouTube', 'text'],
            'social_tiktok'    => ['TikTok', 'text'],
        ],
    ];

    public function index(): string
    {
        return $this->view('admin/settings', [
            'title'    => 'Ayarlar',
            'groups'   => self::FIELDS,
            'settings' => Setting::all(),
        ]);
    }

    public function update(Request $request): void
    {
        $this->verifyCsrf($request);
        foreach (self::FIELDS as $fields) {
            foreach ($fields as $key => $_meta) {
                Setting::set($key, $request->string($key));
            }
        }
        Flash::success('Ayarlar kaydedildi.');
        redirect('yonetim/ayarlar');
    }
}
