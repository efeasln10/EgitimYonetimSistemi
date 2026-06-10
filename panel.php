<?php
// Oturum başlatılır
session_start();
require_once 'baglanti.php';

// Kullanıcı giriş kontorlü
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: index.php");
    exit;
}

$aktif_kullanici_id = $_SESSION['kullanici_id'];
$aktif_kullanici_ad = $_SESSION['ad_soyad'];

// Tüm bilgiler veritabanından çekilir
$sorgu = $db->query("SELECT * FROM egitimler ORDER BY eklenme_tarihi DESC");
$egitimler = $sorgu->fetchAll(PDO::FETCH_ASSOC);

$katildigi_egitimler = [];
if ($_SESSION['rol'] == 'kullanici') {
    $kayit_sorgu = $db->prepare("SELECT egitim_id FROM egitim_kayitlari WHERE kullanici_id = ?");
    $kayit_sorgu->execute([$aktif_kullanici_id]);
    $katildigi_egitimler = $kayit_sorgu->fetchAll(PDO::FETCH_COLUMN); 
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eğitim Paneli - Kurumsal Eğitim Sistemi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
</head>
<body class="has-background-white-ter" style="min-height: 100vh;">

<div class="has-background-dark py-3 px-4 mb-5">
    <div class="container is-flex is-justify-content-space-between is-align-items-center">
        <a class="title is-5 has-text-white mb-0" href="panel.php">Kurumsal Eğitim Portalı</a>
        <div class="is-flex is-align-items-center">
            <span class="has-text-white mr-4">Kullanıcı: <strong class="has-text-white"><?= htmlspecialchars($aktif_kullanici_ad) ?></strong></span>
            <a href="cikis.php" class="button is-light is-outlined is-small">Çıkış Yap</a>
        </div>
    </div>
</div>

<div class="container px-3">
    
    <div class="is-flex is-justify-content-space-between is-align-items-center mb-4">
        <h2 class="title is-4 has-text-grey-dark mb-0">Eğitim Listesi</h2>
        <?php if ($_SESSION['rol'] == 'egitmen'): ?>
        <a href="egitim_ekle.php" class="button is-primary">
            + Yeni Eğitim Ekle
        </a>
        <?php endif; ?>
    </div>

    <div class="card border-0">
        <div class="card-content p-0">
            <div class="table-container mb-0">
                <table class="table is-fullwidth is-hoverable is-striped is-narrow mb-0">
                    <thead class="has-background-light">
                        <tr>
                            <th class="pl-4">Eğitim Adı</th>
                            <th>Eğitmen</th>
                            <th>Tarih</th>
                            <th>Kontenjan</th>
                            <th>Durum</th>
                            <th class="has-text-right pr-4">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($egitimler) > 0): ?>
                            <?php foreach ($egitimler as $egitim): ?>
                                <tr>
                                    <td class="pl-4 has-text-weight-medium has-text-dark align-middle"><?= htmlspecialchars($egitim['egitim_ad']) ?></td>
                                    <td class="align-middle"><?= htmlspecialchars($egitim['egitmen_ad']) ?></td>
                                    <td class="align-middle"><?= date('d.m.Y', strtotime($egitim['tarih'])) ?></td>
                                    <td class="align-middle"><?= htmlspecialchars($egitim['kontenjan']) ?> Kişi</td>
                                    <td class="align-middle">
                                        <?php if ($egitim['durum'] == 'Aktif'): ?>
                                            <span class="tag is-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="tag is-light">Aktif Değil</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="has-text-right pr-4 align-middle">
                                        <?php if ($_SESSION['rol'] == 'egitmen' && $egitim['kullanici_id'] == $aktif_kullanici_id): ?>
                                            <div class="is-flex is-justify-content-flex-end" style="gap: 5px;">
                                                <a href="egitim_duzenle.php?id=<?= $egitim['egitim_id'] ?>" class="button is-small is-outlined is-dark">Düzenle</a>
                                                <form action="egitim_sil.php" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                                                    <input type="hidden" name="egitim_id" value="<?= $egitim['egitim_id'] ?>">
                                                    <button type="submit" class="button is-small is-outlined is-danger">Sil</button>
                                                </form>
                                            </div>
                                        <?php elseif ($_SESSION['rol'] == 'kullanici'): ?>
                                            <?php if (in_array($egitim['egitim_id'], $katildigi_egitimler)): ?>
                                                <button class="button is-small is-success" disabled>
                                                    Katıldınız
                                                </button>
                                            <?php else: ?>
                                                <form action="egitime_katil.php" method="POST">
                                                    <input type="hidden" name="egitim_id" value="<?= $egitim['egitim_id'] ?>">
                                                    <button type="submit" class="button is-small is-info" <?= ($egitim['durum'] == 'pasif' || $egitim['durum'] == 'Aktif Değil') ? 'disabled' : '' ?>>
                                                        Eğitime Katıl
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="has-text-centered py-6 has-text-grey">
                                    Henüz listelenecek bir eğitim bulunmuyor.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>