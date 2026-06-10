CREATE DATABASE Kurumsal_Egitim_Yonetim_Sistemi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE Kurumsal_Egitim_Yonetim_Sistemi;

CREATE TABLE kullanicilar (

    kullanici_id INT AUTO_INCREMENT PRIMARY KEY,
    ad_soyad VARCHAR(50) NOT NULL,
    eposta VARCHAR(50) NOT NULL,
    sifre VARCHAR(255) NOT NULL,
    
    
);

CREATE TABLE egitimler (
   
    egitim_id INT AUTO_INCREMENT PRIMARY KEY,
    kullanici_id INT NOT NULL,
    egitim_ad VARCHAR(100) NOT NULL,
    egitmen_ad VARCHAR(100) NOT NULL,
    durum ENUM('Aktif', 'Aktif Değil') DEFAULT 'Aktif',
    kontenjan INT NOT NULL,
    tarih DATE NOT NULL,
    eklenme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kullanici_id) REFERENCES kullanicilar(kullanici_id) ON DELETE CASCADE
    
);