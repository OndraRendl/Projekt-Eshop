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
$database = "eshop";

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
            background: linear-gradient(to bottom, #f4f4f4, #eaeaea);
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #333;
            color: white;
            padding: 20px 15px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        header h1 {
            margin: 0;
            font-size: 2rem;
        }

        nav {
            display: flex;
            justify-content: center;
            background-color: #444;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
            background: linear-gradient(to right, #666, #444);
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        nav a:hover {
            background: linear-gradient(to right, #444, #222);
            transform: translateY(-3px);
        }

        .container {
            margin: 40px auto;
            max-width: 1200px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            overflow: hidden;
            border-radius: 10px;
        }

        .product-table th, .product-table td {
            padding: 12px 15px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .product-table th {
            background: linear-gradient(to right, #333, #555);
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }

        .product-table tr:hover {
            background-color: #f4f4f4;
        }

        .product-table td img {
            width: 80px;
            height: auto;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .actions button {
            background-color: #FF4136;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .actions button:hover {
            background-color: #ff6347;
        }

        footer {
            text-align: center;
            padding: 15px;
            background-color: #333;
            color: white;
            margin-top: 30px;
            font-size: 0.9rem;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
        }

        footer p {
            margin: 0;
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
        <h2>Seznam produktů na skladě</h2>

        <?php
        if ($result->num_rows > 0) {
            echo '<table class="product-table">';
            echo '<tr>
                    <th>ID</th>
                    <th>Název produktu</th>
                    <th>Obrázek</th>
                    <th>Cena</th>
                    <th>Počet kusů na skladě</th>
                    <th>Akce</th>
                  </tr>';

            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                echo '<td>' . htmlspecialchars($row['nazev']) . '</td>';
                echo '<td><img src="' . htmlspecialchars($row['obrazek']) . '" alt="Produkt"></td>';
                echo '<td>' . number_format($row['cena'], 2, ',', ' ') . ' Kč</td>';
                echo '<td>' . htmlspecialchars($row['skladem']) . ' ks</td>';
                echo '<td class="actions">
                        <form method="post" action="delete_product.php">
                            <input type="hidden" name="id" value="' . htmlspecialchars($row['id']) . '">
                            <button type="submit">Odstranit</button>
                        </form>
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


