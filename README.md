# RyujinOS

Minecraft sunucuları için **CMS & otomasyon** web scripti. Oyun ile sitenin
birbiriyle senkron çalışmasını sağlar: oyuncular siteye kredi yükler, mağazadan
**VIP / spawner / anahtar** gibi ürünleri satın alır, ürünler oyun içine **RCON**
ile otomatik teslim edilir.

> Saf **PHP 8.3+** ile yazılmıştır — framework yok, Composer bağımlılığı yok.
> Arayüz **TailwindCSS (CDN)** ile, ödemeler **Shopier** ile çalışır.

## Özellikler

- **Oyun ↔ Site senkronizasyonu** — oyun (AuthMe tarzı) ve site hesapları tek
  `Accounts` tablosunda birleşir. Oyuncu oyunda kayıt olunca sitede de hesabı
  olur (aynı kullanıcı adı + şifre, AuthMe uyumlu hash).
- **Kredi sistemi** — Shopier ile bakiye yükleme (test modunda sandbox akışı),
  kredi geçmişi, işlem kayıtları.
- **Mağaza** — kategoriler, ürünler, indirim, stok, öne çıkanlar. Satın alımda
  RCON komutları (`{player}` yer tutucusu) sunucuda çalıştırılır.
- **Kullanıcı profili** — bakiye, sipariş geçmişi, kredi geçmişi, e-posta/avatar
  düzenleme, şifre değiştirme.
- **Yönetici paneli** — dashboard, ürün/kategori/blog CRUD, kullanıcı yönetimi
  (rol + manuel kredi düzenleme), siparişler, site ayarları.
- **Blog & Destek talepleri** — yayın yönetimi, biletli destek sistemi.
- **Anasayfa** — hero, blog vitrini, sunucu durumu (Server List Ping), istatistik
  kenar çubuğu. LeaderOS benzeri turuncu/krem Minecraft teması.

## Teknoloji

- PHP 8.3+ (PDO, `mbstring`, `openssl`, `curl`, `sockets`)
- MySQL / MariaDB (üretim) — yerel test için SQLite de desteklenir
- TailwindCSS (CDN), Nunito fontu
- Özel router, view renderer (layout/section), PDO katmanı, oturum tabanlı auth + CSRF

## Kurulum

1. **Bağımlılıklar:** PHP 8.3+ ve MySQL/MariaDB.

2. **Yapılandırma:**
   ```bash
   cp .env.example .env
   ```
   `.env` içinde veritabanı, `AUTHME_HASH`, `RCON_*` ve `SHOPIER_*`
   değerlerini doldurun. `APP_KEY` üretmek için:
   ```bash
   php cli.php key
   ```

3. **Veritabanı:** Boş bir veritabanı oluşturun (örn. `ryujinos`) ve şemayı kurun:
   ```bash
   php cli.php migrate      # tabloları oluşturur
   php cli.php seed         # demo veri + admin hesabı (admin / admin123)
   # veya tek komutta sıfırla:
   php cli.php fresh
   ```

4. **Çalıştır:**
   ```bash
   php -S 127.0.0.1:8000 -t public
   ```
   `http://localhost:8000` adresini açın. Yönetim paneli: `/yonetim`
   (varsayılan giriş: **admin / admin123**).

### Tek tablo (AuthMe ile paylaşımlı) hesap sistemi

Oyun (AuthMe tarzı) ve site hesapları **tek bir `Accounts` tablosunda** birleşir;
bir oyuncunun kimlik bilgileri ile site verisi (kredi, rol, profil) aynı satırda
tutulur. Böylece "oyunda kayıt = site hesabı" (ve tersi) otomatik çalışır.

Şifreler `AUTHME_HASH` ile seçilen AuthMe uyumlu formatta saklanır (varsayılan
`SHA256`), böylece aynı kullanıcı adı + şifre hem oyunda hem sitede geçerlidir.
Üretimde oyun eklentisi ile siteyi **aynı veritabanı ve `Accounts` tablosuna**
bağlamanız yeterlidir.

| Kolon | Açıklama |
|-------|----------|
| `id`, `uuid`, `username`, `realname`, `email`, `password` | Hesap kimliği + AuthMe uyumlu şifre |
| `credit` | Site kredisi (eski `balance`) |
| `role` | `member` \| `admin` |
| `avatar`, `two_factor` | Site profili |
| `isVerified`, `creationIP`, `creationDate` | Hesap durumu/kaydı |
| `last_login_ip`, `last_login_at`, `updated_at` | Site oturum bilgisi |

### Shopier

`SHOPIER_TEST_MODE=true` (veya boş API anahtarları) iken gerçek tahsilat
yapılmaz; ödeme akışı yerel bir sandbox ekranıyla tamamlanır. Canlı kullanım
için Shopier mağaza panelindeki API anahtarlarını girin ve test modunu kapatın.
Sunucudan sunucuya bildirim (callback) adresi: `/odeme/callback`.

## Plesk / paylaşımlı hosting kurulumu

1. **Dosyaları yükleyin.** Tüm proje dosyalarını domain klasörüne çıkarın
   (örn. `httpdocs/`). FTP/Dosya Yöneticisi ile `.env` ve `.htaccess` dahil
   **gizli dosyaları da** yüklediğinizden emin olun.

2. **PHP sürümü.** Plesk → *PHP Settings* bölümünden **PHP 8.3+** seçin.
   Gerekli eklentiler: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `sockets`.

3. **Document Root (önerilen yöntem).** Plesk → *Hosting Settings* →
   **Document Root** değerini `public` klasörüne ayarlayın
   (örn. `httpdocs/public`). Böylece `.env`, `src/`, `database.sql` gibi
   dosyalar web'den erişilemez olur. `public/.htaccess` temiz URL
   yönlendirmesini yapar.
   - *Document Root'u değiştiremiyorsanız:* dosyaları olduğu gibi bırakın;
     kökteki `.htaccess` istekleri otomatik olarak `public/` içine yönlendirir
     ve hassas dosyaları korur (yine de Document Root'u `public` yapmak en
     güvenli yöntemdir).

4. **Veritabanı.** Plesk → *Databases* → yeni bir MySQL veritabanı ve kullanıcı
   oluşturun. phpMyAdmin'i açın, veritabanını seçin ve **`database.sql`**
   dosyasını *Import* edin (şema + demo veri + `admin / admin123` gelir).

5. **`.env` ayarları.** `.env.example`'ı `.env` olarak kopyalayıp düzenleyin:
   - `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://alanadiniz.com`
   - `APP_KEY` (32+ karakter rastgele) — yerelde `php cli.php key` ile üretip
     yapıştırabilirsiniz.
   - `DB_*` → Plesk'te oluşturduğunuz veritabanı bilgileri.
   - `AUTHME_HASH` → oyun sunucunuzun AuthMe şifre hash formatı (ortak `Accounts` tablosu).
   - `RCON_*` → ürün teslimi için sunucunuzun RCON bilgileri.
   - `SHOPIER_*` → canlı tahsilat için API anahtarları, `SHOPIER_TEST_MODE=false`.

6. **İzinler.** `storage/` klasörünün **yazılabilir** olduğundan emin olun
   (Plesk Dosya Yöneticisi → izinler ya da `chmod -R 775 storage`).

7. **Test.** `https://alanadiniz.com` açın; yönetim paneli `/yonetim`
   (**admin / admin123** — giriş yaptıktan sonra şifreyi değiştirin).

> **Güvenlik:** Canlıya geçince `admin` şifresini değiştirin, `APP_DEBUG=false`
> bırakın ve mümkünse SSL (Let's Encrypt) etkinleştirin.

## CLI komutları

| Komut | Açıklama |
|-------|----------|
| `php cli.php migrate` | Tabloları oluşturur |
| `php cli.php seed` | Demo veri + admin hesabı ekler |
| `php cli.php fresh` | Tüm tabloları silip yeniden kurar ve seed eder |
| `php cli.php key` | Yeni `APP_KEY` üretir |

## Dizin yapısı

```
public/          # Front controller (index.php) + statik varlıklar
src/Core/        # Router, View, Database, Auth, Session, CSRF, helpers...
src/Models/      # User, Product, Order, Payment, Post, SupportTicket...
src/Services/    # AuthMe hash, RCON, Server List Ping, Shopier
src/Controllers/ # Public + Admin controller'ları
views/           # Layout, partial ve sayfa şablonları (saf PHP)
config/          # config.php (.env'den okur)
cli.php          # Komut satırı aracı
```

## Notlar

- `.env` ve `storage/` içerikleri versiyonlanmaz.
- Gerçek RCON/Shopier kimlik bilgileri olmadan da tüm akışlar (kredi yükleme,
  satın alma, teslimat denemesi) yerelde uçtan uca denenebilir.
