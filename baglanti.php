<?php

$host = "";
$dbname = "";
$username = ""; 
$password = "";     

try {
    
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    
    
} catch (PDOException $e) {
    
    die("Veritabanına Bağlanılamadı: " . $e->getMessage());
}
?>