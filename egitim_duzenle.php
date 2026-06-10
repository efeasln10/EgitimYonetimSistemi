<?php
// Oturum başlatılır
session_start();
require_once 'baglanti.php';


if (!isset($_SESSION['kullanici_id'])) { 
    if ($_SESSION['rol'] !== 'egitmen') { header("Location: panel.php"); exit; }
    header("Location: index.php");
    exit;
}

$mesaj = '';
$aktif_kullanici_id = $_SESSION['kullanici_id'];
$egitim = null;

// Mevcut eğitim bilgilerini çekme
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $egitim_id = $_GET['id'];
    
    $sorgu = $db->prepare("SELECT * FROM egitimler WHERE egitim_id = ? AND kullanici_id = ?");
    $sorgu->execute([$egitim_id, $aktif_kullanici_id]);
    $egitim = $sorgu->fetch(PDO::FETCH_ASSOC);
    
    // Eğer eğitim yoksa panele yönlendirilir
    if (!$egitim) {
        header("Location: panel.php");
        exit;
    }
} else {
    header("Location: panel.php");
    exit;
}

// Bilgilerin düzenlenmesi 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $guncel_id = $_POST['egitim_id'];
    $egitim_ad = trim($_POST['egitim_ad'] ?? '');
    $egitmen_ad = trim($_POST['egitmen_ad'] ?? '');
    $kontenjan = trim($_POST['kontenjan'] ?? '');
    $tarih = $_POST['tarih'] ?? '';
    $durum = $_POST['durum'] ?? 'aktif';

    if (empty($egitim_ad) || empty($egitmen_ad) || empty($kontenjan) || empty($tarih)) {
        $mesaj = "<div class='notification is-danger is-light'>Lütfen tüm alanları eksiksiz doldurunuz.</div>";
    } elseif (!is_numeric($kontenjan) || $kontenjan < 1) {
        $mesaj = "<div class='notification is-danger is-light'>Kontenjan geçerli bir sayı olmalıdır.</div>";
    } else {
        $guncelle_sorgu = $db->prepare("UPDATE egitimler SET egitim_ad = ?, egitmen_ad = ?, kontenjan = ?, tarih = ?, durum = ? WHERE egitim_id = ? AND kullanici_id = ?");
        
        $sonuc = $guncelle_sorgu->execute([$egitim_ad, $egitmen_ad, $kontenjan, $tarih, $durum, $guncel_id, $aktif_kullanici_id]);
        
        if ($sonuc) {
            $mesaj = "<div class='notification is-success is-light'>Eğitim bilgileri başarıyla güncellendi! <a href='panel.php'><strong>Panele dön</strong></a></div>";
            
            // Verilerin güncellenmiş hali kaydedilir
            $egitim['egitim_ad'] = $egitim_ad;
            $egitim['egitmen_ad'] = $egitmen_ad;
            $egitim['kontenjan'] = $kontenjan;
            $egitim['tarih'] = $tarih;
            $egitim['durum'] = $durum;
        } else {
            $mesaj = "<div class='notification is-danger is-light'>Güncelleme sırasında bir hata oluştu.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eğitim Düzenle - Kurumsal Eğitim Sistemi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
</head>
<body class="has-background-white-ter" style="min-height: 100vh;">

<div class="has-background-dark py-3 px-4 mb-5">
    <div class="container is-flex is-justify-content-space-between is-align-items-center">
        <a class="title is-5 has-text-white mb-0" href="panel.php">Kurumsal Eğitim Portalı</a>
        <div class="is-flex is-align-items-center">
            <span class="has-text-light mr-4">Kullanıcı: <strong><?= htmlspecialchars($_SESSION['ad_soyad']) ?></strong></span>
            <a href="cikis.php" class="button is-light is-outlined is-small">Çıkış Yap</a>
        </div>
    </div>
</div>

<div class="container">
    <div class="columns is-centered">
        <div class="column is-8-tablet is-6-desktop">
            
            <div class="is-flex is-justify-content-space-between is-align-items-center mb-4">
                <h2 class="title is-4 has-text-grey-dark mb-0">Eğitimi Düzenle</h2>
                <a href="panel.php" class="button is-small is-dark is-outlined">Vazgeç ve Geri Dön</a>
            </div>

            <div class="card shadow-sm">
                <div class="card-content">
                    
                    <?= $mesaj ?>

                    <form action="egitim_duzenle.php?id=<?= $egitim['egitim_id'] ?>" method="POST">
                        
                        <input type="hidden" name="egitim_id" value="<?= $egitim['egitim_id'] ?>">
                        
                        <div class="field">
                            <label class="label">Eğitim Adı</label>
                            <div class="control">
                                <input class="input" type="text" id="egitim_ad" name="egitim_ad" value="<?= htmlspecialchars($egitim['egitim_ad']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="field">
                            <label class="label">Eğitmen Adı ve Soyadı</label>
                            <div class="control">
                                <input class="input" type="text" id="egitmen_ad" name="egitmen_ad" value="<?= htmlspecialchars($egitim['egitmen_ad']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="columns mb-0">
                            <div class="column pb-0">
                                <div class="field">
                                    <label class="label">Kontenjan (Kişi)</label>
                                    <div class="control">
                                        <input class="input" type="number" id="kontenjan" name="kontenjan" min="1" value="<?= htmlspecialchars($egitim['kontenjan']) ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="column pb-0">
                                <div class="field">
                                    <label class="label">Eğitim Tarihi</label>
                                    <div class="control">
                                        <input class="input" type="date" id="tarih" name="tarih" value="<?= $egitim['tarih'] ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="field mb-5">
                            <label class="label">Eğitim Durumu</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="durum" name="durum">
                                        <option value="Aktif" <?= ($egitim['durum'] == 'Aktif') ? 'selected' : '' ?>>Aktif (Kayıtlara Açık)</option>
                                        <option value="Aktif Değil" <?= ($egitim['durum'] == 'Aktif Değil') ? 'selected' : '' ?>>Aktif Değil (Kayıtlara Kapalı)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="control">
                            <button type="submit" class="button is-primary is-fullwidth">Değişiklikleri Kaydet</button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>