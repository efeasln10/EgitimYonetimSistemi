<?php
// Oturum başlatılır
session_start();
require_once 'baglanti.php';

// Kullanıcı girişi kontrolü
if (!isset($_SESSION['kullanici_id'])) {
    if ($_SESSION['rol'] !== 'egitmen') { header("Location: panel.php"); exit; }
    header("Location: index.php");
    exit;
}

// Formun POST ile alındığının kontrolü ve eğitim ID'sinin gönderilip gönderilmediğinin kontrolü
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['egitim_id'])) {
    
    $egitim_id = $_POST['egitim_id'];
    $aktif_kullanici_id = $_SESSION['kullanici_id'];

   
    $kontrol_sorgu = $db->prepare("SELECT egitim_id FROM egitimler WHERE egitim_id = ? AND kullanici_id = ?");
    $kontrol_sorgu->execute([$egitim_id, $aktif_kullanici_id]);
    
    if ($kontrol_sorgu->rowCount() > 0) {
        
        $silme_sorgu = $db->prepare("DELETE FROM egitimler WHERE egitim_id = ?");
        $silme_sorgu->execute([$egitim_id]);
        
    }
}

header("Location: panel.php");
exit;
?>