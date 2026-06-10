<?php
// Oturum başlatılır
session_start();
require_once 'baglanti.php';
$mesaj = '';

// Kullanıcı zaten giriş yapmışsa panele yönlendirilir
if(isset($_SESSION['kullanici_id'])){
    header("Location: panel.php");
    exit;
}

// Formun POST ile alınıp alınmadığının kontrolü
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $eposta = trim($_POST['eposta'] ?? '');
    $sifre = $_POST['sifre'] ?? '';

    // Formda boş alan kontrolü
    if(empty($eposta) || empty($sifre)){
        $mesaj = "<div class='notification is-danger is-light'>Lütfen e-posta ve şifre girin!</div>";
    }
    else{
        $sorgu = $db->prepare("SELECT kullanici_id, ad_soyad, sifre, rol FROM kullanicilar WHERE eposta = ?");
        $sorgu->execute([$eposta]);
        $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

        // Giriş bilgilerinin doğruluğunun kontrolü
        if ($kullanici && password_verify($sifre, $kullanici['sifre'])) {
            $_SESSION['kullanici_id'] = $kullanici['kullanici_id'];
            $_SESSION['ad_soyad'] = $kullanici['ad_soyad'];
            $_SESSION['rol'] = $kullanici['rol']; 
            
            header("Location: panel.php");
            exit;
        }
        else{
            $mesaj = "<div class='notification is-danger is-light'>E-posta veya şifreniz hatalı!</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - Kurumsal Eğitim Sistemi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
</head>
<body class="has-background-white-ter" style="min-height: 100vh;">

<section class="section">
    <div class="container mt-6">
        <div class="columns is-centered">
            <div class="column is-5-tablet is-4-desktop">
                <div class="card shadow-sm">
                    <div class="card-content">
                        <h3 class="title is-4 has-text-centered mb-5">Sisteme Giriş</h3>
                        
                        <?= $mesaj ?>

                        <form action="index.php" method="POST">
                            <div class="field">
                                <label class="label">Kurumsal E-posta Adresi</label>
                                <div class="control">
                                    <input class="input" type="email" id="eposta" name="eposta" required>
                                </div>
                            </div>
                            
                            <div class="field mb-5">
                                <label class="label">Şifre</label>
                                <div class="control">
                                    <input class="input" type="password" id="sifre" name="sifre" required>
                                </div>
                            </div>
                            
                            <div class="control">
                                <button type="submit" class="button is-primary is-fullwidth is-medium">Giriş Yap</button>
                            </div>
                        </form>
                        
                        <div class="has-text-centered mt-4">
                            <p>Henüz hesabınız yok mu? <a href="kayitolma.php" class="has-text-link">Kayıt Ol</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</body>
</html>