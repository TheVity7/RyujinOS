<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Category;
use App\Models\CreditTransaction;
use App\Models\Order;
use App\Models\Post;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\AuthMeService;

/**
 * Seeds the database with demo data plus a default admin account.
 */
final class Seeder
{
    public static function run(): void
    {
        self::settings();
        $admin = self::users();
        $categories = self::categories();
        self::products($categories);
        self::posts();
        self::demoActivity($admin);
    }

    private static function settings(): void
    {
        $defaults = [
            'site_name'        => 'RyujinOS',
            'site_description' => 'RyujinOS Minecraft Sunucusu, Towny, Skyblock, TrapPVP ve Arena modlarını bir araya getiren, mobil uyumlu ve 1.19.4 üzeri tüm sürümleri destekleyen aktif bir oyuncu topluluğudur.',
            'server_ip'        => 'play.ryujinos.net',
            'discord_invite'   => 'https://discord.gg/ryujinos',
            'discord_members'  => '906',
            'hero_title'       => "Towny'de Kasabanı Kur ve Yönet!",
            'hero_subtitle'    => 'Ekonomi, ticaret ve kasaba sistemiyle dolu hayatta kalma deneyimini, arkadaşlarınla birlikte kasabanı büyüt!',
            'hero_image'       => 'https://images.unsplash.com/photo-1607513746994-51f730a44832?q=80&w=1200&auto=format&fit=crop',
            'announcement'     => "RyujinOS'da %50'ye varan indirimler sizi bekliyor!",
            'gaming_night'     => date('Y-m-d', strtotime('next wednesday')) . ' 20:00:00',
            'social_facebook'  => '#',
            'social_instagram' => '#',
            'social_x'         => '#',
            'social_youtube'   => '#',
            'social_tiktok'    => '#',
        ];
        foreach ($defaults as $key => $value) {
            if (Setting::get($key, '__none__') === '__none__') {
                Setting::set($key, $value);
            }
        }
    }

    private static function users(): User
    {
        $admin = User::findByUsername('admin');
        if (!$admin instanceof User) {
            $admin = User::create([
                'username' => 'admin',
                'realname' => 'admin',
                'email'    => 'admin@ryujinos.net',
                'password' => AuthMeService::hash('admin123'),
                'role'     => 'admin',
                'balance'  => 8572.00,
            ]);
        }

        $players = [
            'loky1454' => 'nodernetinfos@gmail.com',
            'Notch'    => 'notch@example.com',
            'Steve'    => 'steve@example.com',
        ];
        foreach ($players as $name => $email) {
            if (!User::findByUsername($name) instanceof User) {
                User::create([
                    'username' => $name,
                    'realname' => $name,
                    'email'    => $email,
                    'password' => AuthMeService::hash('player123'),
                    'balance'  => 0,
                ]);
            }
        }
        return $admin;
    }

    /** @return array<string,int> */
    private static function categories(): array
    {
        $data = [
            ['VIP Paketleri', 'vip', 'Sunucumuzdaki tüm VIP rütbeleri', 'crown', 1],
            ['Anahtarlar', 'anahtarlar', 'Sandık anahtarları ve kasalar', 'key', 2],
            ['Spawnerlar', 'spawnerlar', 'Mob spawnerları', 'cube', 3],
            ['Krediler', 'krediler', 'Bakiye yükleme paketleri', 'coins', 4],
        ];
        $ids = [];
        foreach ($data as [$name, $slug, $desc, $icon, $order]) {
            $existing = Category::findBySlug($slug);
            $ids[$slug] = $existing?->id ?? Category::create([
                'name' => $name, 'slug' => $slug, 'description' => $desc, 'icon' => $icon, 'sort_order' => $order,
            ]);
        }
        return $ids;
    }

    private static function products(array $categories): void
    {
        if (Product::all() !== []) {
            return;
        }
        $products = [
            ['vip', 'VIP', 'vip', '7 günlük VIP rütbesi. Renkli sohbet, /fly ve özel kit.', 49.90, 0, "lp user {player} parent addtemp vip 7d\nbroadcast &a{player} &fVIP satın aldı!", true],
            ['vip', 'MVIP', 'mvip', '30 günlük MVIP rütbesi. Tüm VIP avantajları + ekstra home.', 99.90, 79.90, "lp user {player} parent addtemp mvip 30d", true],
            ['vip', 'VIP+', 'vip-plus', 'Süresiz VIP+ rütbesi.', 149.90, 0, "lp user {player} parent add vipplus", false],
            ['anahtarlar', 'Nadir Anahtar', 'nadir-anahtar', '5 adet nadir sandık anahtarı.', 24.90, 0, "crates give {player} rare 5", true],
            ['anahtarlar', 'Efsanevi Anahtar', 'efsanevi-anahtar', '3 adet efsanevi sandık anahtarı.', 59.90, 49.90, "crates give {player} legendary 3", true],
            ['spawnerlar', 'Zombi Spawner', 'zombi-spawner', '1 adet zombi spawnerı.', 39.90, 0, "give {player} spawner 1", true],
            ['spawnerlar', 'Iron Golem Spawner', 'iron-golem-spawner', '1 adet demir golem spawnerı.', 89.90, 0, "give {player} spawner 1", false],
        ];
        $i = 0;
        foreach ($products as [$cat, $name, $slug, $desc, $price, $sale, $cmd, $featured]) {
            Product::create([
                'category_id' => $categories[$cat] ?? 0,
                'name' => $name, 'slug' => $slug, 'description' => $desc,
                'price' => $price, 'sale_price' => $sale, 'commands' => $cmd,
                'stock' => -1, 'is_active' => 1, 'featured' => $featured ? 1 : 0, 'sort_order' => $i++,
            ]);
        }
    }

    private static function posts(): void
    {
        if (Post::all() !== []) {
            return;
        }
        $posts = [
            ['Yeni Sezon Başladı!', 'yeni-sezon', 'Sezon 3 tüm sürprizleriyle açıldı, hemen katıl!', "Yeni sezonumuz tüm haritalar, yeni eşyalar ve etkinliklerle başladı. Sunucuya giriş yaparak ödüllerini topla!"],
            ['Hafta Sonu Etkinliği', 'hafta-sonu-etkinligi', 'Bu hafta sonu çift XP ve özel ödüller seni bekliyor.', "Cumartesi ve Pazar günleri boyunca tüm modlarda çift XP kazanacaksınız."],
            ['Mağaza İndirimleri', 'magaza-indirimleri', 'Seçili ürünlerde %50 indirim fırsatını kaçırma.', "VIP paketleri ve anahtarlarda büyük indirimler mağazamızda."],
            ['Discord Topluluğumuza Katıl', 'discord-katil', 'Güncel duyurular ve etkinlikler için Discord sunucumuza bekleriz.', "Discord sunucumuzda 900+ aktif üyemizle sohbet edebilir, etkinliklere katılabilirsin."],
        ];
        foreach ($posts as [$title, $slug, $excerpt, $body]) {
            Post::create([
                'title' => $title, 'slug' => $slug, 'excerpt' => $excerpt,
                'body' => $body, 'author' => 'admin', 'published' => 1,
            ]);
        }
    }

    private static function demoActivity(User $admin): void
    {
        if (CreditTransaction::forUser($admin->id) !== []) {
            return;
        }
        foreach ([6999, 450, 1000, 123] as $amount) {
            CreditTransaction::record($admin->id, (float) $amount, 'purchase', 'Kredi yükleme');
        }
        Order::create([
            'user_id' => $admin->id, 'product_id' => 0, 'product_name' => 'MVIP',
            'total' => 79.90, 'status' => 'delivered', 'delivery' => 'delivered',
        ]);
        Order::create([
            'user_id' => $admin->id, 'product_id' => 0, 'product_name' => 'Nadir Anahtar',
            'total' => 24.90, 'status' => 'delivered', 'delivery' => 'delivered',
        ]);
    }
}
