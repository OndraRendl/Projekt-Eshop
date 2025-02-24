<?php
session_start();

// Připojení k databázi
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "eshop";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Připojení selhalo: " . $conn->connect_error);
}

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header('Location: login.html'); // Přesměrování na přihlášení, pokud není admin
    exit();
}

// Načtení produktů z databáze
$query = "SELECT * FROM produkty";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Správa produktů</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 20%;
            background-color: #333;
            color: white;
            padding: 20px;
            box-sizing: border-box;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #555;
        }

        .sidebar .divider {
            border-top: 1px solid #555;
            margin: 20px 0;
        }

        .content {
            margin-left: 20%;
            width: 80%;
            padding: 20px;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
            font-weight: 500;
            text-align: left;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        footer {
            padding: 20px;
            background-color: #333;
            color: #bbb;
            font-size: 0.9em;
            text-align: center;
            margin-top: 40px;
        }

        footer a {
            color: #bbb;
            text-decoration: none;
            margin: 0 10px;
        }

        footer a:hover {
            color: #fff;
        }

        .status-icon {
            font-size: 1.5em;
            vertical-align: middle;
            margin-right: 8px;
        }
        
        .sidebar a.active {
            background-color:#28a745; 
            color: white;
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
    </style>
</head>
<body>

<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>E-shop Apple</h2>
        <div class="divider"></div>
        <p>Uživatelské jméno: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
        <div class="divider"></div>
        <a href="uvod.php">Úvod</a>
        <a href="obchod.php">Obchod</a>
        <a href="kontakt.php">Kontakt</a>
        <a href="kosik.php">Košík 🛒</a>
        <div class="divider"></div>
        <a href="moje_udaje.php">Můj účet</a>
        <a href="orders.php">Moje objednávky</a>
        <a href="server.php?action=logout" class="logout-btn">Odhlásit se</a>
        <a href="javascript:void(0);" onclick="confirmDelete()">Odstranit účet</a>
        <div class="divider"></div>
        <?php if (isset($_SESSION['username']) && $_SESSION['username'] === 'admin'): ?>
        <a href="admin.php" class="active">Správa produktů</a>
    <?php endif; ?>
    </div>

    <!-- Content -->
    <div class="content">
        <h2>Seznam produktů</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Název produktu</th>
                <th>Obrázek</th>
                <th>Cena</th>
                <th>Počet na skladě</th>
                <th>Akce</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['nazev']) ?></td>
                    <td><img src="<?= htmlspecialchars($row['obrazek']) ?>" alt="Produkt" style="width: 80px; height: auto;"></td>
                    <td><?= number_format($row['cena'], 2, ',', ' ') ?> Kč</td>
                    <td><?= htmlspecialchars($row['skladem']) ?> ks</td>
                    <td class="actions">
                        <form method="post" action="delete_product.php">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                            <button type="submit">Odstranit</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

<footer>
    <p>© 2025 | <a href="obchodnipodminky.html">Obchodní podmínky</a> | <a href="pravidla.html">Pravidla ochrany soukromí</a></p>
    <p>Email: info@store.cz | Telefon: 777 666 555</p>
</footer>

</body>
</html>



