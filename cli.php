<?php

declare(strict_types=1);

/**
 * RyujinOS command line tool.
 *
 *   php cli.php migrate        Create database tables
 *   php cli.php seed           Insert demo data + admin account
 *   php cli.php fresh          Drop everything, migrate and seed
 *   php cli.php key            Generate an APP_KEY
 */

require __DIR__ . '/bootstrap.php';

use App\Core\Migrator;
use App\Core\Seeder;

$command = $argv[1] ?? 'help';

switch ($command) {
    case 'migrate':
        Migrator::migrate();
        echo "Veritabanı tabloları oluşturuldu.\n";
        break;

    case 'seed':
        Seeder::run();
        echo "Demo veriler eklendi. Admin: admin / admin123\n";
        break;

    case 'fresh':
        Migrator::dropAll();
        Migrator::migrate();
        Seeder::run();
        echo "Veritabanı sıfırlandı, tablolar oluşturuldu ve demo veriler eklendi.\n";
        echo "Admin girişi: admin / admin123\n";
        break;

    case 'key':
        echo 'APP_KEY=' . bin2hex(random_bytes(24)) . "\n";
        break;

    default:
        echo "Kullanım: php cli.php [migrate|seed|fresh|key]\n";
        break;
}
