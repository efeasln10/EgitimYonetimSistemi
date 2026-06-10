<?php
require_once 'baglanti.php';  
$mesaj = '';

// Formun POST ile gönderilip gönderilmediğinin kontrolü
if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    // Değişkenlerin başındaki ve sonundaki boşlukları temizleme
    $ad_soyad = trim($_POST['ad_soyad'] ?? '');
    $eposta = trim($_POST['eposta'] ?? '');
    $sifre = $_POST['sifre'] ?? '';
    
    // Formda boş alan kontrolü
    if (empty($ad_soyad) || empty($eposta) || empty($sifre)) {    
        $mesaj = "<div class='notification is-danger is-light'>Lütfen tüm alanları doldurunuz!</div>";
    } 
    // E posta yazım şekli kontrolü
    elseif (!filter_var($eposta, FILTER_VALIDATE_EMAIL)) {   
        $mesaj = "<div class='notification is-danger is-light'>Lütfen geçerli bir e-posta adresi giriniz!</div>";
    } 
    else {
        $kontrol_sorgu = $db->prepare("SELECT kullanici_id FROM kullanicilar WHERE eposta = ?");  
        $kontrol_sorgu->execute([$eposta]);    
        // E postanın kayıtlı olup olmadığının kontrolü
        if ($kontrol_sorgu->rowCount() > 0) {   
            $mesaj = "<div class='notification is-warning is-light'>Bu e-posta zaten kayıtlı.</div>";
        }
         // Tüm gerekli şartlar sağlandığında kullanıcının kayıt bilgilerin veritabanına kaydedilmesi
         else {   
            $rol = $_POST['rol'] ?? 'kullanici';
            $hashli_sifre = password_hash($sifre, PASSWORD_DEFAULT);
            $ekle_sorgu = $db->prepare("INSERT INTO kullanicilar (ad_soyad, eposta, sifre, rol) VALUES (?, ?, ?, ?)");
            $ekleme_sonucu = $ekle_sorgu->execute([$ad_soyad, $eposta, $hashli_sifre, $rol]);
            
            if ($ekleme_sonucu) {
                $mesaj = "<div class='notification is-success is-light'>Kayıt işleminiz başarıyla tamamlandı!</div>";
            } else {
                $mesaj = "<div class='notification is-danger is-light'>Kayıt sırasında bir hata oluştu. Lütfen tekrar deneyin.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol - Kurumsal Eğitim Sistemi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
</head>
<body class="has-background-white-ter" style="min-height: 100vh;">

<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-5-tablet is-4-desktop">
                <div class="card shadow-sm">
                    <div class="card-content">
                        <h3 class="title is-4 has-text-centered mb-5">Sisteme Kayıt Ol</h3>
                        
                        <?= $mesaj ?>

                        <form action="kayitolma.php" method="POST">
                            <div class="field">
                                <label class="label">Ad Soyad</label>
                                <div class="control">
                                    <input class="input" type="text" id="ad_soyad" name="ad_soyad" required>
                                </div>
                            </div>
                            
                            <div class="field">
                                <label class="label">Kurumsal E-posta Adresi</label>
                                <div class="control">
                                    <input class="input" type="email" id="eposta" name="eposta" required>
                                </div>
                            </div>
                            
                            <div class="field">
                                <label class="label">Sistemdeki Rolünüz</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select id="rol" name="rol" required>
                                            <option value="kullanici" selected>Eğitim Alan (Personel)</option>
                                            <option value="egitmen">Eğitmen</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="field mb-5">
                                <label class="label">Şifre</label>
                                <div class="control">
                                    <input class="input" type="password" id="sifre" name="sifre" required>
                                </div>
                            </div>
                            
                            <div class="control">
                                <button type="submit" class="button is-primary is-fullwidth is-medium">Kayıt Ol</button>
                            </div>
                        </form>
                        
                        <div class="has-text-centered mt-4">
                            <p>Zaten hesabınız var mı? <a href="index.php" class="has-text-link">Giriş Yap</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</body>
</html>