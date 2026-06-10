<?php
// Oturum başlatılır
session_start();
require_once 'baglanti.php';

// Giriş yapılmamışsa veya rol kullanıcı değilse panele yönlendirir
if (!isset($_SESSION['kullanici_id']) || $_SESSION['rol'] !== 'kullanici') {
    header("Location: panel.php");
    exit;
}

// Formun POST ile gönderilip gönderilmediğinin kontrolü 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['egitim_id'])) {
    
    $egitim_id = $_POST['egitim_id'];
    $kullanici_id = $_SESSION['kullanici_id'];

    $kontrol_sorgu = $db->prepare("SELECT durum FROM egitimler WHERE egitim_id = ?");
    $kontrol_sorgu->execute([$egitim_id]);
    $egitim = $kontrol_sorgu->fetch(PDO::FETCH_ASSOC);
    // Eğitimin varlığının ve aktif olup olmadığının kontrolü 
    if ($egitim && $egitim['durum'] == 'Aktif') {
        
        $kayit_kontrol = $db->prepare("SELECT kayit_id FROM egitim_kayitlari WHERE kullanici_id = ? AND egitim_id = ?");
        $kayit_kontrol->execute([$kullanici_id, $egitim_id]);
        
        if ($kayit_kontrol->rowCount() == 0) {
            $ekle_sorgu = $db->prepare("INSERT INTO egitim_kayitlari (kullanici_id, egitim_id) VALUES (?, ?)");
            $ekle_sorgu->execute([$kullanici_id, $egitim_id]);
        }
    }
}

header("Location: panel.php");
exit;
?>