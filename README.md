# RyujinOS

Minecraft sunucuları için **CMS & otomasyon** web scripti. Oyun ile sitenin
birbiriyle senkron çalışmasını sağlar: oyuncular siteye kredi yükler, mağazadan
**VIP / spawner / anahtar** gibi ürünleri satın alır, ürünler oyun içine **RCON**
ile otomatik teslim edilir.

> Saf **PHP 8.3+** ile yazılmıştır — framework yok, Composer bağımlılığı yok.
> Arayüz **TailwindCSS (CDN)** ile, ödemeler **Shopier** ile çalışır.

## Özellikler

- **Oyun ↔ Site senkronizasyonu** — AuthMe / Velocity Auth eklenti tablosuyla
  ortak veritabanı. Oyuncu oyunda kayıt olunca sitede de hesabı olur (aynı
  kullanıcı adı + şifre). Üç mod: `native`, `authme`, `velocity`.
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
   `.env` içinde veritabanı, `AUTH_INTEGRATION`, `RCON_*` ve `SHOPIER_*`
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

### AuthMe / Velocity entegrasyonu

`AUTH_INTEGRATION=authme` (veya `velocity`) iken site, eklentinin oyuncu
tablosunu **kaynak** kabul eder; şifreler AuthMe formatında okunur/yazılır.
Yerel testte seeder, paylaşılan kimlik bilgileriyle bir demo eklenti tablosu
oluşturur, böylece "oyunda kayıt = site hesabı" akışı canlı sunucu olmadan
denenebilir. Üretimde bu tabloyu eklenti yönetir; siteyi aynı veritabanına
bağlamanız yeterlidir.

### Shopier

`SHOPIER_TEST_MODE=true` (veya boş API anahtarları) iken gerçek tahsilat
yapılmaz; ödeme akışı yerel bir sandbox ekranıyla tamamlanır. Canlı kullanım
için Shopier mağaza panelindeki API anahtarlarını girin ve test modunu kapatın.
Sunucudan sunucuya bildirim (callback) adresi: `/odeme/callback`.

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
