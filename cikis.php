<?php
// O anki oturuma erişmek için oturumu başlatıyoruz
session_start();

// 2. Oturum değişkenlerini boş bir diziye eşitleyerek temizliyoruz
$_SESSION = array();

// 3. Sunucudaki oturumu yok ediyoruz.
session_destroy();

// 4. Çıkış yapıldıktan sonra tekrar giriş sayfasına yönlendiriyoruz.
header("Location: index.php");
exit;
?>