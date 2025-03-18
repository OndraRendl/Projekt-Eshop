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

// Získání ID objednávky z URL
$order_id = isset($_GET['id']) ? $_GET['id'] : 0;

if ($order_id == 0) {
    die("Neplatné ID objednávky.");
}

// Získání detailů objednávky
$query = "SELECT * FROM orders WHERE id = ? AND username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $order_id, $_SESSION['username']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Objednávka nenalezena.");
}

?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-shop - Detail objednávky</title>
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

        .order-details {
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .order-details h2 {
            margin-bottom: 20px;
            font-size: 1.5em;
            color: #333;
        }

        .order-details .detail-row {
            margin-bottom: 10px;
        }

        .order-details .detail-row span {
            font-weight: bold;
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
        .sidebar a.active {
            background-color:#28a745; /* Zvolte barvu pro zvýraznění */
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
        <h2>Detail objednávky</h2>
        <div class="order-details">
            <h2>Objednávka č. <?php echo htmlspecialchars($order['id']); ?></h2>

            <div class="detail-row">
                <span>Jméno a příjmení: </span><?php echo htmlspecialchars($order['name']); ?>
            </div>
            <div class="detail-row">
                <span>Adresa: </span><?php echo htmlspecialchars($order['address']); ?>
            </div>
            <div class="detail-row">
                <span>Město: </span><?php echo htmlspecialchars($order['city']); ?>
            </div>
            <div class="detail-row">
                <span>PSČ: </span><?php echo htmlspecialchars($order['zip']); ?>
            </div>
            <div class="detail-row">
                <span>Email: </span><?php echo htmlspecialchars($order['email']); ?>
            </div>
            <div class="detail-row">
                <span>Telefon: </span><?php echo htmlspecialchars($order['phone']); ?>
            </div>
            <div class="detail-row">
                <span>Způsob platby: </span><?php echo htmlspecialchars($order['payment_method']); ?>
            </div>
            <div class="detail-row">
                <span>Celková cena: </span><?php echo htmlspecialchars($order['total_price']); ?> Kč
            </div>
            <div class="detail-row">
                <span>Datum objednávky: </span><?php echo htmlspecialchars($order['order_date']); ?>
            </div>
            <div class="detail-row">
                <span>Stav: </span>
                <?php if ($order['shipping_method'] == 'courier'): ?>
                    Doručeno 🚚
                <?php elseif ($order['shipping_method'] == 'pickup'): ?>
                    Vyzvednuto 📦
                <?php else: ?>
                    Neznámý stav
                <?php endif; ?>
            </div>
            <div class="detail-row">
                <span>Produkty: </span><?php echo htmlspecialchars($order['products']); ?>
            </div>
        </div>
    </div>
</div>

<footer>
    <p>© 2025 | <a href="obchodnipodminky.html">Obchodní podmínky</a> | <a href="pravidla.html">Pravidla ochrany soukromí</a></p>
    <p>Email: info@store.cz | Telefon: 777 666 555</p>
</footer>

<script>
    function confirmDelete() {
        if (confirm("Opravdu chcete smazat svůj účet? Tuto akci nelze vrátit!")) {
            window.location.href = "server.php?action=delete_account"; // Odeslání požadavku na server
        }
    }
</script>

</body>
</html>
