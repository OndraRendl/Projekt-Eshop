<?php
session_start();

require_once 'db_connection.php'; // Zde připojte soubor s připojením k databázi

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Připojení selhalo: " . $conn->connect_error);
}

if (!isset($_SESSION['username'])) {
    header('Location: login.html'); // Přesměrování na přihlášení, pokud není přihlášen
    exit();
}

// Získání objednávek podle username
$username = $_SESSION['username'];
$query = "SELECT * FROM orders WHERE username = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moje objednávky</title>
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
    </style>
</head>
<body>

<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>E-shop Apple</h2>
        <div class="divider"></div>
        <p>Uživatel: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
        <div class="divider"></div>
        <a href="uvod.php">Úvod</a>
        <a href="obchod.php">Obchod</a>
        <a href="kontakt.php">Kontakt</a>
        <a href="kosik.php">Košík 🛒</a>
        <div class="divider"></div>
        <a href="moje_udaje.php">Můj účet</a>
        <a href="orders.php" class="active">Moje objednávky</a>
        <a href="server.php?action=logout" class="logout-btn">Odhlásit se</a>
        <a href="javascript:void(0);" onclick="confirmDelete()">Odstranit účet</a>
        
        <?php if (isset($_SESSION['username']) && $_SESSION['username'] === 'admin'): ?>
        <div class="divider"></div>
        <a href="admin.php">Správa produktů</a>
    <?php endif; ?>
    </div>

    <!-- Content -->
    <div class="content">
        <h2>Moje objednávky</h2>
        <table>
            <tr>
                <th>Uživatelské jméno</th>
                <th>Jméno a příjmení</th>
                <th>Adresa</th>
                <th>Město</th>
                <th>PSČ</th>
                <th>Email</th>
                <th>Telefon</th>
                <th>Způsob platby</th>
                <th>Celková cena</th>
                <th>Datum objednávky</th>
                <th>Stav</th>
                <th>Produkty</th>
            </tr>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($order['username']) ?></td>
                    <td><?= htmlspecialchars($order['name']) ?></td>
                    <td><?= htmlspecialchars($order['address']) ?></td>
                    <td><?= htmlspecialchars($order['city']) ?></td>
                    <td><?= htmlspecialchars($order['zip']) ?></td>
                    <td><?= htmlspecialchars($order['email']) ?></td>
                    <td><?= htmlspecialchars($order['phone']) ?></td>
                    <td><?= htmlspecialchars($order['payment_method']) ?></td>
                    <td><?= htmlspecialchars($order['total_price']) ?> Kč</td>
                    <td><?= htmlspecialchars($order['order_date']) ?></td>
                    <td>
                        <?php if ($order['shipping_method'] == 'courier'): ?>
                            Doručeno <span class="status-icon">🚚</span>
                        <?php elseif ($order['shipping_method'] == 'pickup'): ?>
                            Vyzvednuto <span class="status-icon">📦</span>
                        <?php else: ?>
                            <span>Neznámý stav</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($order['products']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

<footer>
    <p>© 2025 | <a href="obchodnipodminky.html">Obchodní podmínky</a> | <a href="pravidla.html">Pravidla ochrany soukromí</a></p>
    <p>Email: info@store.cz | Telefon: 777 666 555</p>
</footer>

<!-- JavaScript pro potvrzení smazání účtu -->
<script>
    function confirmDelete() {
        if (confirm("Opravdu chcete smazat svůj účet? Tuto akci nelze vrátit!")) {
            window.location.href = "server.php?action=delete_account"; // Odeslání požadavku na server
        }
    }
</script>

</body>
</html>
