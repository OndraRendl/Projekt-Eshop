<?php
session_start(); // Start session, abychom mohli pracovat s proměnnými session

require_once 'db_connection.php'; // Zde připojte soubor s připojením k databázi

$conn = new mysqli($host, $username, $password, $dbname);

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

// Přidání produktu do košíku
if (isset($_POST['add_to_cart'])) {
    $quantity = (int)$_POST['quantity'];
    
    // Zjisti, kolik kusů už je v košíku
    $cart_quantity = isset($_SESSION['cart'][$product_id]['quantity']) ? $_SESSION['cart'][$product_id]['quantity'] : 0;
    
    // Zkontroluj, jestli by celkové množství nepřesáhlo dostupné množství na skladě
    if ($cart_quantity + $quantity > $product['skladem']) {
        echo "<script>alert('Není možné přidat více kusů, než je dostupné na skladě!');</script>";
    } else {
        // Pokud je vše v pořádku, přidej do košíku
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'name' => $product['nazev'],
                'price' => $product['cena'],
                'quantity' => $quantity,
                'image' => $product['obrazek']
            ];
        }
        header('Location: kosik.php');
        exit();
    }
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
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .overlay {
            padding: 20px;
            padding-top: 50px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        nav {
            position: absolute;
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

        .product-title {
            font-size: 2.5em;
            color: white;
            margin-bottom: 40px;
        }

        .product-details,
        .product-info {
            background: rgba(255, 255, 255, 0.2);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        .product-details {
            width: 250px;
            height: 450px;
        }

        .product-details img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 20px;
            max-height: 200px;
            object-fit: contain;
        }

        .product-info {
            width: 300px;
            text-align: left;
        }

        .product-info h3 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        .product-info p {
            font-size: 1.1em;
            color: #ccc;
            margin-bottom: 10px;
        }

        .product-info .price {
            font-size: 1.5em;
            color: white;
            font-weight: bold;
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

        .product-container {
            display: flex;
            gap: 30px;
        }

        /* Základní styl pro tlačítka */
        button, .button {
            background-color: #ff6f61; /* Hezká barva pozadí */
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1.2em;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        /* Styl pro tlačítko při najetí myší */
        button:hover, .button:hover {
            background-color: #ff4f3b; /* Tmavší odstín pro hover */
            transform: scale(1.05); /* Trochu zvětší tlačítko */
        }

        /* Styl pro vstupní pole množství */
        input[type="number"] {
            padding: 10px;
            font-size: 1.2em;
            border: 2px solid #ccc;
            border-radius: 8px;
            width: 60px;
            text-align: center;
            margin-top: 10px;
            transition: border-color 0.3s ease;
        }

        /* Styl pro vstupní pole při najetí myší */
        input[type="number"]:focus {
            border-color: #ff6f61; /* Změna barvy rámečku */
            outline: none; /* Odebrání defaultního ohraničení */
        }
        nav .nav-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%); /* Uprostřed horizontálně */
            font-size: 1.5em;
            font-weight: bold;
            color: white;
        }
        .price-small small {
            font-size: 0.9em;
            color: #ccc;
            margin-bottom: 10px;
        }

    </style>
</head>
<body>

    <nav>
        <div class="auth-links">
            <?php if (isset($_SESSION['username'])): ?>
                <span class="username">Uživatel: <?php echo htmlspecialchars($_SESSION['username']); ?></span> <!-- Zobrazení uživatelského jména -->
                
                <a href="moje_udaje.php" class="view-orders-btn">Můj účet</a>
                <a href="orders.php" class="view-orders-btn">Moje objednávky</a>
                <a href="server.php?action=logout" class="logout-btn">Odhlásit se</a>

            <?php else: ?>
                <a href="login.html" class="login-btn">Přihlásit se</a>
                <a href="register.html" class="register-btn">Registrovat se</a>
            <?php endif; ?>
            <div class="nav-center">
                <span class="site-title">E-shop Apple</span> <!-- Titul E-shop Apple -->
            </div>
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
            <h2 class="product-title"><?php echo htmlspecialchars($product['nazev']); ?></h2>

            <div class="product-container">
                <div class="product-details">
                    <img src="<?php echo htmlspecialchars($product['obrazek']); ?>" alt="Produkt">
                    <h3><?php echo htmlspecialchars($product['nazev']); ?></h3>
                </div>

                <div class="product-info">
                    <h3>Popis produktu</h3>
                    <p><?php echo htmlspecialchars($product['popis']); ?></p><br>

                    <?php if ($product['skladem'] > 0): ?>
                        <p>Skladem: 
                            <?php 
                                $quantity_text = ($product['skladem'] > 10) ? ">10 ks" : $product['skladem'] . " ks";
                                echo $quantity_text;
                            ?>
                        </p>

                        <div class="price">
                            Cena: <?php echo number_format($product['cena'], 0, ',', ' ') . ' Kč'; ?>
                        </div>
                        <?php
                            $cenaBezDPH = round($product['cena'] / 1.21, 0, PHP_ROUND_HALF_UP);
                        ?>
                        <div class="price-small">
                            <small>bez DPH: <?php echo number_format($cenaBezDPH, 0, ',', ' ') . ' Kč'; ?></small>
                        </div><br>


                        <form method="POST">
                            <label for="quantity">Množství:</label><br>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['skladem']; ?>" required>
                            <button type="submit" name="add_to_cart" class="button">Přidat do košíku</button>
                        </form>
                    <?php else: ?>
                        <p><strong style="color: red;" >Vyprodáno</strong></p>
                    <?php endif; ?>
                </div>



            </div>
        </div>
    </div>

    <footer>
    <p>© 2025 | <a href="obchodnipodminky.html">Obchodní podmínky</a> | <a href="pravidla.html">Pravidla ochrany soukromí</a></p>
    <p>Email: info@store.cz | Telefon: 777 666 555</p>
</footer>

</body>
</html>


