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

// Načtení produktů z databáze
$sql = "SELECT * FROM produkty";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Správa produktů</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #333;
            color: white;
            padding: 15px;
            text-align: center;
        }

        header h1 {
            margin: 0;
        }

        nav {
            display: flex;
            justify-content: center;
            background-color: #444;
            padding: 10px;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        nav a:hover {
            background-color: #666;
        }

        .container {
            margin: 30px;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .product-table th, .product-table td {
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .product-table th {
            background-color: #333;
            color: white;
        }

        .product-table td img {
            width: 80px;
            height: auto;
            border-radius: 5px;
        }

        .actions a {
            color: #FF4136;
            text-decoration: none;
            margin: 0 5px;
            font-weight: bold;
        }

        .actions a:hover {
            text-decoration: underline;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: white;
            margin-top: 30px;
        }
    </style>
</head>
<body>

    <header>
        <h1>Správa produktů</h1>
    </header>

    <nav>
        <a href="uvod.php">Domů</a>
        <a href="obchod.php">Obchod</a>
    </nav>

    <div class="container">
        <h2>Seznam produktů</h2>

        <?php
        if ($result->num_rows > 0) {
            echo '<table class="product-table">';
            echo '<tr>
                    <th>ID</th>
                    <th>Název produktu</th>
                    <th>Obrázek</th>
                    <th>Cena</th>
                    <th>Akce</th>
                  </tr>';

            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                echo '<td>' . htmlspecialchars($row['nazev']) . '</td>';
                echo '<td><img src="' . htmlspecialchars($row['obrazek']) . '" alt="Produkt"></td>';
                echo '<td>' . number_format($row['cena'], 2, ',', ' ') . ' Kč</td>';
                echo '<td class="actions">
                        <a href="delete_product.php?id=' . $row['id'] . '">Odstranit</a>
                      </td>';
                echo '</tr>';
            }

            echo '</table>';
        } else {
            echo '<p>Žádné produkty v databázi.</p>';
        }

        $conn->close();
        ?>
    </div>

    <footer>
        <p>© 2025 | E-shopapple | Všechna práva vyhrazena</p>
    </footer>

</body>
</html>

