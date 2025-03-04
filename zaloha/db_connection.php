<?php
// Nastavení připojení k databázi
$host = 'dbs.spskladno.cz'; // Server databáze
$dbname = 'vyuka1'; // Název vaší databáze (nahraďte skutečným názvem)
$username = 'student1'; // Uživatelské jméno databáze (standardně "root" v XAMPP)
$password = 'spsnet'; // Heslo databáze (v XAMPP bývá prázdné)

try {
    // Vytvoření připojení pomocí PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Pokud připojení selže, zobrazí se chybová zpráva
    die('Chyba při připojení k databázi: ' . $e->getMessage());
}


