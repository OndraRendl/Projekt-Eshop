<?php
session_start(); // Start session, abychom mohli pracovat s proměnnými session

// Připojení k databázi
$servername = "localhost";
$username = "root"; // Výchozí uživatelské jméno pro XAMPP
$password = ""; // Výchozí heslo pro XAMPP (prázdné)
$database = "e-shopapple"; // Název vaší databáze

$conn = new mysqli($servername, $username, $password, $database);

// Kontrola připojení
if ($conn->connect_error) {
    die("Připojení k databázi selhalo: " . $conn->connect_error);
}

// Získání ID produktu z URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id > 0) {
    // Načtení informací o produktu z databáze
    $sql = "SELECT * FROM produkty WHERE id = $product_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        die("Produkt nebyl nalezen.");
    }
} else {
    die("Neplatné ID produktu.");
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-shop - Produkt</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: white;
            background-color: black;
            text-align: center;
        }

        .background {
            position: relative;
            width: 100%;
            height: 100vh;
            background-image: url('1.avif');
            background-size: cover;
            background-position: center;
        }

        .overlay {
            padding: 20px;
            padding-top: 50px;
            box-sizing: border-box;
        }

        nav {
            position: sticky;
            top: 0;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: space-between;
            padding: 20px 0;
            z-index: 1000;
        }

        nav .auth-links {
            display: flex;
            justify-content: flex-start;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 20px;
            font-size: 1em;
            position: relative;
        }

        nav a.active::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            background-color: white;
            bottom: -5px;
            left: 0;
        }

        .divider {
            border-left: 2px solid white;
            height: 24px;
            margin: 0 10px;
        }

        .username {
            color: white;
            font-size: 1em;
            margin-right: 20px;
            font-weight: bold;
            margin-left: 30px;
        }

        h1 {
            font-size: 3em;
            margin-bottom: 20px;
        }

        .product-details {
            background: rgba(255, 255, 255, 0.2);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            width: 250px; /* Stejná šířka jako produktové rámečky */
            height: 450px; /* Můžete upravit výšku podle potřeby */
            text-align: center;
        }

        .product-details img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 20px;
            max-height: 200px; /* Limit výšky obrázku */
            object-fit: contain;
        }

        .product-details h3 {
            font-size: 2em;
            margin: 10px 0;
        }

        .product-details p {
            font-size: 1.1em;
            color: #ccc;
            margin-bottom: 20px;
        }

        .product-details .price {
            font-size: 1.5em;
            color: white;
            font-weight: bold;
            margin-bottom: 20px;
        }

        footer {
            font-size: 0.9em;
            color: #bbb;
            padding: 20px;
            margin-top: 20px;
        }

        footer a {
            color: #bbb;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <nav>
        <div class="auth-links">
            <?php if (isset($_SESSION['username'])): ?>
                <span class="username">Uživatel: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="server.php?action=logout" class="logout-btn">Odhlásit se</a>
                <a href="server.php?action=delete_account" class="delete-account-btn">Odstranit účet</a>
            <?php else: ?>
                <a href="login.html" class="login-btn">Přihlásit se</a>
                <a href="register.html" class="register-btn">Registrovat se</a>
            <?php endif; ?>
        </div>
        <div>
            <a href="uvod.php">Úvod</a>
            <a href="obchod.php" class="active">Obchod</a>
            <a href="kontakt.php">Kontakt</a>
            <span class="divider"></span>
            <a href="kosik.php">Košík 🛒</a>
        </div>
    </nav>

    <div class="background">
        <div class="overlay">
            <div class="product-details">
                <img src="<?php echo htmlspecialchars($product['obrazek']); ?>" alt="Produkt">
                <h3><?php echo htmlspecialchars($product['nazev']); ?></h3>
                <p><?php echo htmlspecialchars($product['popis']); ?></p>
                <div class="price"><?php echo htmlspecialchars($product['cena']); ?> Kč</div>
            </div>
        </div>
    </div>

    <footer>
        <p>© 2025 | <a href="obchodnipodminky.html">Obchodní podmínky</a> | <a href="pravidla.html">Pravidla ochrany soukromí</a></p>
    </footer>

</body>
</html>



