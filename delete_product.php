<?php
// Startování session a kontrola, zda je uživatel admin
session_start();

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header('Location: uvod.php'); // Přesměrování na úvodní stránku, pokud není admin
    exit;
}

// Připojení k databázi
$servername = "localhost";
$username = "root";
$password = "";
$database = "E-shopapple";

$conn = new mysqli($servername, $username, $password, $database);

// Kontrola připojení
if ($conn->connect_error) {
    die("Připojení k databázi selhalo: " . $conn->connect_error);
}

// Získání ID produktu z URL
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // SQL dotaz pro odstranění produktu
    $sql = "DELETE FROM produkty WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Produkt byl úspěšně odstraněn.";
    } else {
        echo "Chyba při odstraňování produktu: " . $conn->error;
    }
} else {
    echo "Produkt ID není k dispozici.";
}

$conn->close();

// Přesměrování zpět na admin panel po chvíli
header('Refresh: 2; URL=admin.php');
exit;
?>
