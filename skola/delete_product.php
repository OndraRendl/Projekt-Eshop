<?php
// Startování session a kontrola, zda je uživatel admin
session_start();

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header('Location: uvod.php'); // Přesměrování na úvodní stránku, pokud není admin
    exit;
}

// Připojení k databázi
$host = "dbs.spskladno.cz";
$username = "student1";
$password = "spsnet";
$database = "vyuka1";

$conn = new mysqli($host, $username, $password, $database);

// Kontrola připojení
if ($conn->connect_error) {
    die("Připojení k databázi selhalo: " . $conn->connect_error);
}

// Získání ID produktu z POST
if (isset($_POST['id'])) {
    $id = (int) $_POST['id']; // Zajištění bezpečnosti hodnoty ID

    // SQL dotaz pro odstranění produktu s použitím prepared statement
    $sql = "DELETE FROM produkty WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Přiřazení hodnoty ID k parametru v dotazu
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "Produkt byl úspěšně odstraněn.";
        } else {
            echo "Chyba při odstraňování produktu: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Chyba při přípravě dotazu: " . $conn->error;
    }
} else {
    echo "Produkt ID není k dispozici.";
}

$conn->close();

// Přesměrování zpět na admin panel po chvíli
header('Refresh: 2; URL=admin.php');
exit;
?>
