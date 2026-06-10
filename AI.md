# Yapay Zekadan Alınan Yardımlar






## Alınan Yardımlar


**İstem:** php'de şifre hashleme fonksiyonu nasıl kullanılır

**Cevap:** PHP'de şifreleri güvenli bir şekilde hashlemek (şifrelemek) ve daha sonra doğrulamak için yerleşik password_hash() ve password_verify() fonksiyonları kullanılır.

Geçmişte sıklıkla kullanılan md5() veya sha1() gibi algoritmalar artık kesinlikle kullanılmamalıdır, çünkü bu yöntemler günümüzde saniyeler içinde kırılabilmektedir.

İşte bu fonksiyonların modern ve güvenli kullanım rehberi:

1. Şifreyi Hashleme (password_hash)
Yeni bir kullanıcı kayıt olduğunda veya şifresini değiştirdiğinde bu fonksiyonu kullanırsınız. Üretilen karmaşık hash değerini (genellikle 60 karakter uzunluğundadır) veritabanınıza kaydedersiniz.

PHP
<?php
// Kullanıcının girdiği düz metin şifre
$sifre = "GizliSifrem123!";

/* * PASSWORD_DEFAULT kullanımı en güvenli olanıdır. 
 * Zamanla PHP güncellendikçe, varsayılan algoritma (şu an bcrypt) 
 * otomatik olarak en güvenli yeni standarda geçer.
 */
$hashli_sifre = password_hash($sifre, PASSWORD_DEFAULT);

// Bu hash değerini veritabanınıza VARCHAR(255) türünde bir sütuna kaydedin.
echo "Oluşturulan Hash: " . $hashli_sifre;
?>
2. Şifreyi Doğrulama (password_verify)
Kullanıcı giriş yapmaya çalıştığında, formdan gelen düz metin şifreyi, veritabanınızda kayıtlı olan hash'li şifreyle karşılaştırmak için bu fonksiyonu kullanırsınız.

PHP
<?php
// Giriş formundan gelen şifre
$girilen_sifre = "GizliSifrem123!";

// Veritabanından kullanıcının hesabına ait çektiğiniz hash
// Örnek: $veritabanindan_gelen_hash = $kullanici['sifre_hash'];
$veritabanindan_gelen_hash = '$2y$10$YourGeneratedHashStringHere...';

if (password_verify($girilen_sifre, $veritabanindan_gelen_hash)) {
    // Şifre eşleşti! Oturumu başlatabilirsiniz.
    echo "Şifre doğru, giriş başarılı!";
} else {
    // Şifre yanlış.
    echo "Kullanıcı adı veya şifre hatalı!";
}
?>
Ekstra Bilgi: Algoritma Zorluğunu Artırma (Cost)
Eğer sunucunuz güçlü ise, brute-force (kaba kuvvet) saldırılarına karşı hash'in çözülme süresini uzatmak için cost (maliyet) değerini artırabilirsiniz. Varsayılan cost değeri genellikle 10'dur.

PHP
<?php
$sifre = "GizliSifrem123!";

// Cost değerini 12 yaparak işlemi biraz daha donanım zorlayıcı hale getiriyoruz
$ayarlar = [
    'cost' => 12,
];

$guclu_hash = password_hash($sifre, PASSWORD_BCRYPT, $ayarlar);
?>
Önemli İpuçları:

Veritabanınızda şifrelerin tutulacağı sütun uzunluğunu her ihtimale karşı VARCHAR(255) olarak ayarlamanız tavsiye edilir. Çünkü ileride yeni bir algoritma gelirse, üretilen hash string'i 60 karakterden daha uzun olabilir.

password_hash() fonksiyonu, hash'in içine "salt" (tuz) değerini otomatik olarak ekler ve yönetir. Sizin manuel olarak bir tuz oluşturup eklemenize gerek yoktur

**İstem:** php'de session ile oturum açma kapama işlemleri nasıl kontrol edilir

**Cevap:** PHP'de oturum (session) yönetimi, bir kullanıcının web sitenizdeki sayfalar arasında gezinirken kimliğinin "hatırlanmasını" sağlayan temel yapıdır. Oturum bilgileri sunucu tarafında güvenli bir şekilde saklanır.

Bir oturum açma (login), sayfaları koruma ve oturum kapatma (logout) işlemlerinin adım adım nasıl yapıldığını aşağıda bulabilirsiniz.

1. Her Şeyin Başı: session_start()
PHP'de session değişkenlerini okumak veya yazmak istediğiniz her sayfanın en başında oturumu başlatmanız gerekir.

Kritik Kural: session_start() fonksiyonu, sayfada hiçbir HTML veya boşluk çıktısı (echo, print veya HTML etiketleri) olmadan önce çağrılmalıdır.

PHP
<?php
// Sayfanın en üstü
session_start();
?>
2. Oturum Açma (Login İşlemi)
Kullanıcının formdan gönderdiği bilgileri veritabanınızdaki bilgilerle (örneğin daha önce bahsettiğimiz password_verify() ile) doğruladıktan sonra, kullanıcıya ait bazı temel bilgileri session'a kaydedersiniz.

PHP
<?php
session_start();

// ... veritabanı bağlantısı ve şifre doğrulama işlemleri (password_verify vs.) ...

// Şifre doğruysa:
if ($sifre_dogru) {
    // Güvenlik: Oturum sabitleme (session fixation) saldırılarını önlemek için 
    // başarılı girişlerde session ID'sini yenilemek çok önemlidir.
    session_regenerate_id(true);

    // Kullanıcıya ait bilgileri session dizisine kaydediyoruz
    $_SESSION['oturum_acik'] = true;
    $_SESSION['kullanici_id'] = $kullanici['id'];
    $_SESSION['kullanici_adi'] = $kullanici['username'];

    // Giriş başarılı, kullanıcıyı panele yönlendir
    header("Location: panel.php");
    exit; // Yönlendirmeden sonra kodun çalışmaya devam etmesini engelle
} else {
    echo "Hatalı giriş!";
}
?>
3. Sayfaları Korumak (Oturum Kontrolü)
Kullanıcının giriş yaptıktan sonra görebileceği (örneğin profil veya yönetim paneli) sayfalara doğrudan link ile girilmesini engellemek için, sayfanın başında session kontrolü yapılır.

PHP
<?php
// panel.php (Sadece giriş yapanların göreceği sayfa)
session_start();

// 'oturum_acik' değişkeni yoksa veya true değilse kullanıcı giriş yapmamıştır
if (!isset($_SESSION['oturum_acik']) || $_SESSION['oturum_acik'] !== true) {
    // Giriş yapmamış kişiyi login sayfasına geri gönder
    header("Location: login.php");
    exit;
}

// Buradan sonraki kodlar sadece giriş yapmış kullanıcılara çalışır
echo "Hoş geldin, " . $_SESSION['kullanici_adi'];
?>
4. Oturum Kapatma (Logout İşlemi)
Kullanıcı "Çıkış Yap" butonuna tıkladığında, sunucudaki session verilerini temizlemeniz ve oturumu yok etmeniz gerekir.

PHP
<?php
// logout.php
session_start();

// 1. Session dizisindeki tüm verileri boşalt
$_SESSION = array();

// 2. Eğer session tarayıcıda bir çerez (cookie) ile tutuluyorsa, o çerezi de sil (Opsiyonel ama önerilir)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Oturumu tamamen yok et
session_destroy();

// 4. Kullanıcıyı ana sayfaya veya tekrar login sayfasına yönlendir
header("Location: login.php");
exit;
?>
Özetle İşleyiş:

Başarıyla doğrulanan kullanıcı için $_SESSION içine bayrak (true) bırakılır.

Korunan sayfalarda bu bayrağın varlığı (isset()) kontrol edilir. Yoksa kapıdan çevrilir (header()).

Çıkış yapıldığında bu bayraklar ve oturum dosyası sunucudan silinir (session_destroy()).


**İstem** bir sql sorgusu php koduna nasıl dahil edilir

**Cevap** PHP'de bir SQL sorgusunu çalıştırmanın en modern, güvenli ve esnek yolu PDO (PHP Data Objects) eklentisini kullanmaktır. Eskiden kullanılan mysql_query() gibi fonksiyonlar tamamen kullanımdan kaldırılmıştır ve güvenlik açıkları nedeniyle kesinlikle kullanılmamalıdır.

Bir SQL sorgusunu PHP'ye dahil etmek ve çalıştırmak temel olarak 3 aşamadan oluşur: Bağlantı, Hazırlık (Prepare) ve Çalıştırma (Execute).

İşte adım adım kullanımı:

1. Veritabanına Bağlanma
Öncelikle veritabanı ile PHP arasında bir köprü kurmanız gerekir. Bu işlemi genellikle baglanti.php gibi ayrı bir dosyada yapıp diğer sayfalara dahil ederiz (require veya include ile).

PHP
<?php
$host = 'localhost';
$dbname = 'veritabani_adim';
$kullanici = 'root';
$sifre = '';

// Karakter setini utf8mb4 yaparak Türkçe karakter sorunlarını önlüyoruz
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    // PDO nesnesini oluşturarak bağlantıyı açıyoruz
    $db = new PDO($dsn, $kullanici, $sifre);
    
    // Hata modunu istisnalar (exceptions) fırlatacak şekilde ayarlıyoruz
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // Bağlantı başarısız olursa hatayı ekrana bas
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>
2. Veri Çekme (SELECT Sorgusu)
Dışarıdan gelen verileri (örneğin kullanıcının girdiği bir ID veya arama terimi) asla doğrudan SQL metninin içine yazmamalısınız. Bu sizi doğrudan SQL Injection saldırılarına açık hale getirir. Bunun yerine "Hazırlıklı İfadeler" (Prepared Statements) kullanırız.

PHP
<?php
// baglanti.php'yi dahil ettiğinizi varsayıyoruz
// include 'baglanti.php';

$aranan_rol = 'admin';

// 1. SQL sorgusunu hazırlıyoruz. Değişken gelecek yere :rol gibi bir parametre yazıyoruz.
$sql = "SELECT id, kullanici_adi, eposta FROM kullanicilar WHERE rol = :rol";
$sorgu = $db->prepare($sql);

// 2. Sorguyu parametrelere gerçek değerlerini göndererek çalıştırıyoruz.
$sorgu->execute(['rol' => $aranan_rol]);

// 3. Gelen verileri dizi (array) olarak alıyoruz. 
// FETCH_ASSOC, verileri sadece sütun isimleriyle (ilişkisel dizi) getirir.
$kullanicilar = $sorgu->fetchAll(PDO::FETCH_ASSOC);

// Verileri ekrana yazdırma
foreach ($kullanicilar as $kisi) {
    echo $kisi['kullanici_adi'] . " - " . $kisi['eposta'] . "<br>";
}
?>
3. Veri Ekleme, Güncelleme veya Silme (INSERT, UPDATE, DELETE)
SELECT dışındaki işlemlerde veri çekmeyeceğimiz için fetchAll() kullanmamıza gerek yoktur. Sadece işlemi hazırlayıp çalıştırmamız yeterlidir.

Örnek bir INSERT (Veri Ekleme) işlemi:

PHP
<?php
$yeni_kullanici = "Ahmet";
$yeni_eposta = "ahmet@ornek.com";

$sql = "INSERT INTO kullanicilar (kullanici_adi, eposta) VALUES (:kadi, :eposta)";
$sorgu = $db->prepare($sql);

// Parametreleri dizi olarak gönderip çalıştırıyoruz
$sonuc = $sorgu->execute([
    'kadi' => $yeni_kullanici,
    'eposta' => $yeni_eposta
]);

if ($sonuc) {
    // İşlem başarılıysa son eklenen ID'yi alabiliriz
    $eklenen_id = $db->lastInsertId();
    echo "Kayıt başarılı! Yeni ID: " . $eklenen_id;
} else {
    echo "Kayıt eklenirken bir sorun oluştu.";
}
?>
Neden prepare() ve execute() Kullanıyoruz?
Bu yöntemle SQL kodunun iskeletini (prepare) ve içine yerleşecek verileri (execute) ayrı ayrı sunucuya gönderirsiniz. Veritabanı, gönderdiğiniz verileri "çalıştırılabilir kod" olarak değil, sadece "metin" olarak kabul eder. Bu sayede kötü niyetli bir kullanıcı formlara veritabanınızı silecek SQL komutları yazsa bile, o komutlar çalıştırılmaz, sadece bir metin olarak kaydedilir.


