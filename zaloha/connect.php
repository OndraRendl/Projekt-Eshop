<?php
$host = 'dbs.spskladno.cz';
$dbname = 'vyuka1'; // Název tvé databáze
$username = 'student1';
$password = 'spsnet'; // Heslo, pokud je nastavené (výchozí pro XAMPP je prázdné)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Chyba při připojení k databázi: " . $e->getMessage());
}
?>
