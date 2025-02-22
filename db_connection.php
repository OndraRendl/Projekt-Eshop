<?php
// Nastavení připojení k databázi
$host = 'localhost'; // Server databáze
$dbname = 'eshop'; // Název vaší databáze (nahraďte skutečným názvem)
$username = 'root'; // Uživatelské jméno databáze (standardně "root" v XAMPP)
$password = ''; // Heslo databáze (v XAMPP bývá prázdné)

try {
    // Vytvoření připojení pomocí PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Pokud připojení selže, zobrazí se chybová zpráva
    die('Chyba při připojení k databázi: ' . $e->getMessage());
}


