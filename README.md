# Kurumsal Eğitim Yönetim Sistemi 

---

##  Uygulama Ne İşe Yarar? 

Kurumsal Eğitim Yönetim Sistemi; bir şirket, kurum veya eğitim merkezindeki **Eğitim Yönetim** sistemini basit ve net bir platformla yönetir. Platformun temel amaçları şunlardır:

1. **Eğitim Planlamasının Merkezileştirilmesi:** Kurumdaki eğitimlerin başlık, eğitmen, tarih ve kontenjan gibi kritik bilgilerinin tek bir noktada toplanmasını sağlar.
2. **Rol Yetkilendirmesi:** Kurum personelini "Eğitmen" ve "Eğitim Alan (Personel)" olarak ikiye ayırarak, herkesin sadece kendi yetki alanındaki işlemleri görmesini sağlar.
3. **Kontenjan ve Kayıt Takibi:** Personelin sistemdeki eğitimlere tek tıkla kaydolmasını sağlar, kayıt durumlarını anlık olarak takip eder ve çift kayıtların önüne geçer.
4. **Veri Güvenliği ve Doğruluğu:** Kurumsal verilerin bütünlüğünü korumak adına tüm veri ekleme, silme ve güncelleme işlemlerini modern güvenlik protokolleri (şifreleme, SQL injection koruması) ile yürütür.

---

##  Sistem Nasıl Çalışır? 

Uygulama, **PHP** ve **MySQL** veritabanı altyapısı üzerinde, ilişkisel veritabanı modeli mantığıyla çalışır. Sistemin arka plandaki çalışma mekanizması şu aşamalara dayanır:

### 1. Kimlik Doğrulama ve Güvenli Oturum Yönetimi (Session & Auth)
- **Kayıt Aşaması:** Kullanıcı sisteme kaydolurken şifresi doğrudan veritabanına yazılmaz. PHP'nin `password_hash()` fonksiyonu kullanılarak kriptografik olarak geri döndürülemez bir şekilde şifrelenir (hash'lenir). Seçtiği rol (`egitmen` veya `kullanici`) veritabanındaki `rol` sütununa işlenir.
- **Giriş Aşaması:** Giriş formuna yazılan şifre, `password_verify()` fonksiyonu ile veritabanındaki hash'lenmiş şifreyle karşılaştırılır. Doğrulama başarılıysa `session_start()` ile güvenli bir PHP oturumu başlatılır.
- **Hafıza Yönetimi:** Kullanıcının `kullanici_id`, `ad_soyad` ve `rol` bilgileri `$_SESSION` küresel dizisinde saklanır. Bu sayede tarayıcı kapatılmadığı sürece sistem kullanıcının kim olduğunu ve yetkisini hatırlar.

### 2. Role Bağlı Arayüz ve Yetki Kontrolü 
Sistemin kalbi, `panel.php` ve form sayfalarında çalışan rol kontrol mekanizmasıdır. Sayfa yüklendiğinde PHP, oturumdaki `$_SESSION['rol']` değerini okur:
- **Eğitmen Yetkisi:** Eğer rol `egitmen` ise, PHP motoru HTML tablosunun üstündeki **"+ Yeni Eğitim Ekle"** butonunu aktif hale getirir. Tablodaki eğitim listesinde, sadece eğitmenin kendi `kullanici_id` değeri ile eşleşen eğitimlerin sağında **"Düzenle"** ve **"Sil"** butonları ekrana basılır. Eğitmen, başka bir eğitmenin açtığı eğitimi kesinlikle değiştiremez veya silemez.
- **Eğitim Alan Yetkisi:** Eğer rol `kullanici` ise, sistem eğitim yönetim butonlarını tamamen gizler. Bunun yerine, veritabanından çekilen eğitimlerin sağında dinamik bir **"Eğitime Katıl"** butonu gösterir.

### 3. Kayıt Mekanizması 
Kullanıcı bir eğitimin yanındaki "Eğitime Katıl" butonuna bastığında süreç şu adımlarla işler:
1. Form, POST metodu ile `egitime_katil.php` dosyasına gizli bir `egitim_id` gönderir.
2. Arka uçta (Backend) ilk olarak kullanıcının rolünün `kullanici` olup olmadığı ve eğitimin durumunun veritabanında gerçekten **"Aktif"** olup olmadığı doğrulanır.
3. Çift kaydı önlemek için `egitim_kayitlari` tablosunda o `kullanici_id` ve `egitim_id` çiftine ait daha önce atılmış bir kayıt var mı diye kontrol edilir (`rowCount() == 0`).
4. Eğer koşullar sağlanıyorsa, `egitim_kayitlari` köprü tablosuna (Junction Table) yeni bir satır eklenir.
5. `panel.php` sayfasına dönüldüğünde, PHP'nin `in_array()` fonksiyonu kullanıcının katıldığı eğitim listesini tarar ve daha önce katıldığı eğitimler için mavi "Eğitime Katıl" butonu yerine, tıklanamayan yeşil bir **"Katıldınız"** rozeti basar.

## Uygulama Görselleri



## Projeye Katkıda Bulunanlar

- **Efe Aslan** Bursa Teknik Üniversitesi / Bilgisayar Mühendisliği  24360859073
- **Mustafa Mert** Bursa Teknik Üniversitesi / Bilgisayar Mühendisliği  24360859050

---
