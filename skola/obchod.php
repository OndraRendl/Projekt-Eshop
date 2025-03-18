<?php
session_start(); // Start session, abychom mohli pracovat s proměnnými session

require_once 'db_connection.php'; // Zde připojte soubor s připojením k databázi

$conn = new mysqli($host, $username, $password, $dbname);

// Kontrola připojení
if ($conn->connect_error) {
    die("Připojení k databázi selhalo: " . $conn->connect_error);
}

// Získání filtru, pokud je nastaven
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'doporucujeme'; // Defaultní filtr je doporučujeme

// Určujeme SQL dotaz podle zvoleného filtru
switch ($filter) {
    case 'nejlevnejsi':
        $sql = "SELECT * FROM produkty ORDER BY cena ASC";
        break;
    case 'nejdrazsi':
        $sql = "SELECT * FROM produkty ORDER BY cena DESC";
        break;
    case 'doporucujeme':
    default:
        $sql = "SELECT * FROM produkty ORDER BY id ASC"; // Nejmenší ID pro doporučování
        break;
}

$result = $conn->query($sql);

// Pokud bylo odesláno tlačítko pro přidání do košíku
if (isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];  // Získání ID produktu

    // Získání informací o produktu z databáze (můžeš si upravit podle svého kódu)
    $query = "SELECT * FROM produkty WHERE id = $product_id";
    $result = $conn->query($query);
    $product = $result->fetch_assoc();

    // Pokud produkt existuje
    if ($product) {
        // Zjistíme, kolik už je v košíku
        $cart_quantity = isset($_SESSION['cart'][$product_id]['quantity']) ? $_SESSION['cart'][$product_id]['quantity'] : 0;

        // Pokud je celkové množství větší než dostupné množství na skladě
        if ($cart_quantity + 1 > $product['skladem']) {
            echo "<script>alert('Není možné přidat více kusů, než je dostupné na skladě!');</script>";
            echo "<script>window.history.back();</script>"; // Tento řádek vrátí uživatele zpět na předchozí stránku
            exit(); // Ukončení skriptu, aby se už nic nestalo
        } else {
            // Přidání produktu do košíku (nebo zvýšení množství, pokud už v košíku je)
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += 1;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'name' => $product['nazev'],
                    'price' => $product['cena'],
                    'quantity' => 1,
                    'image' => $product['obrazek']
                ];
            }

            // Po úspěšném přidání přesměrujeme zpět na košík
            header('Location: kosik.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-shop - obchod</title>
    <style>
    /* Filtr pro produkty */
    .filter {
        margin: 20px;
        text-align: center;
    }

    .filter button {
        padding: 10px 20px;
        font-size: 1em;
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin: 5px;
        transition: background-color 0.3s;
    }

    .filter button:hover {
        background-color: rgba(255, 255, 255, 0.4);
    }

    .filter button.active {
        background-color: rgba(255, 255, 255, 0.6);
    }

    /* Celkový design těla */
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        color: white;
        background-color: black;
        text-align: center;
    }

    /* Pozadí */
    .background {
        position: relative;
        width: 100%;
        min-height: 100vh;
        background-image: url('1.avif');
        background-size: cover;
        background-position: center;
        padding-top: 80px;
        padding-bottom: 80px;
        box-sizing: border-box;
    }

    /* Overlay pro obsah */
    .overlay {
        padding: 20px;
        padding-top: 50px;
        box-sizing: border-box;
    }

    /* Navigační menu */
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

    nav .nav-center {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        font-size: 1.5em;
        font-weight: bold;
        color: white;
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

    /* Divider mezi odkazy */
    .divider {
        border-left: 2px solid white;
        height: 24px;
        margin: 0 10px;
    }

    /* Jméno uživatele v menu */
    .username {
        color: white;
        font-size: 1em;
        margin-right: 20px;
        font-weight: bold;
        margin-left: 30px;
    }

    /* Nadpis */
    h1 {
        font-size: 3em;
        margin-bottom: 20px;
    }

    /* Seznam produktů */
    .products {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
        padding: 20px;
        min-height: 100vh;
    }

    /* Styl pro produkt */
    .product {
        background: rgba(255, 255, 255, 0.1);
        padding: 20px;
        border-radius: 10px;
        width: 250px;
        text-align: center;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        margin-bottom: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* Obrázek produktu */
    .product img {
        width: 100%;
        height: auto;
        border-radius: 5px;
    }

    /* Název produktu - tučný a bílý */
    .product h3 {
        margin: 10px 0;
        font-weight: bold;
        color: white;
        text-align: left;  /* Zarovnání názvu produktu vlevo */
    }

    /* Popis produktu - šedý a světlý */
    .product p {
        font-size: 14px;
        color: #d3d3d3; /* Světlá šedá */
        text-align: left;  /* Zarovnání textu vlevo */
        margin-top: 5px;  /* Malá mezera mezi názvem a popisem */
        padding-left: 10px;  /* Odsazení zleva pro popis */
        margin-bottom: 0;  /* Zajistí, že nebude mít žádnou mezeru pod popisem */
    }

    /* Zápatí produktu - cena, skladem, tlačítko */
    .product-footer {
        display: flex;
        flex-direction: column;
        align-items: center;  /* Změněno z flex-start na center */
        justify-content: center; /* Zajištění vertikálního zarovnání na střed */
        margin-top: 20px;
        text-align: center;  /* Ujišťujeme se, že text bude na střed */
    }

    /* Cena produktu */
    .product .price {
        font-size: 1.5em;
        color: white;
        font-weight: bold;
        margin-top: 10px;
    }

    /* Informace o posledních kusech */
    .product .last-items {
        color: red;
        font-weight: bold;
        margin-top: 5px;
    }

    /* Tlačítko přidání do košíku */
    .add-to-cart {
        padding: 10px 20px;
        background-color: #ff6f61;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 10px;
        text-align: center;  /* Zajištění, že text uvnitř tlačítka je na střed */
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    
    /* Vyprodáno - červený text */
    .product .out-of-stock {
        color: red;
        font-weight: bold;
        margin-top: 10px;
    }

    /* Footer */
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
    /* Styl pro tlačítko při najetí myší */
    button:hover, .button:hover {
            background-color: #ff4f3b; /* Tmavší odstín pro hover */
            transform: scale(1.05); /* Trochu zvětší tlačítko */
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
    </div>
    <div class="nav-center">
        <span class="site-title">E-shop Apple</span> <!-- Titul E-shop Apple -->
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
        <h1>Náš obchod</h1>

        <!-- Filtr pro produkty (tlačítka) -->
        <div class="filter">
            <button onclick="window.location.href='obchod.php?filter=doporucujeme'" class="<?php echo ($filter == 'doporucujeme') ? 'active' : ''; ?>">Doporučujeme</button>
            <button onclick="window.location.href='obchod.php?filter=nejlevnejsi'" class="<?php echo ($filter == 'nejlevnejsi') ? 'active' : ''; ?>">Nejlevnější</button>
            <button onclick="window.location.href='obchod.php?filter=nejdrazsi'" class="<?php echo ($filter == 'nejdrazsi') ? 'active' : ''; ?>">Nejdražší</button>
        </div>

        <div class="products">
        <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product">';
                    echo '<a href="produkt.php?id=' . $row['id'] . '" class="product-link">';
                    echo '<img src="' . htmlspecialchars($row['obrazek']) . '" alt="Produkt" class="product-image">';
                    echo '<h3 class="product-name">' . htmlspecialchars($row['nazev']) . '</h3>';
                    echo '</a>';

                    // Rychlý popis produktu (pokud existuje)
                    if (!empty($row['popis'])) {
                        echo '<p class="product-description">' . htmlspecialchars($row['popis']) . '</p>';
                    }

                    // Kontrola skladové dostupnosti
                    echo '<div class="product-footer">';
                    if ($row['skladem'] > 0) {
                        echo '<div class="price">' . number_format($row['cena'], 0, ',', ' ') . ' Kč</div>';

                        // Pokud je skladem méně než 10 kusů
                        if ($row['skladem'] < 10) {
                            echo '<div class="stock last-items">POSLEDNÍ KUSY</div>';
                        }

                        // Formulář pro přidání do košíku
                        echo '<form method="POST" class="add-to-cart-form">';
                        echo '<input type="hidden" name="product_id" value="' . $row['id'] . '">';
                        echo '<button type="submit" name="add_to_cart" class="add-to-cart">Přidat do košíku</button>';
                        echo '</form>';
                    } else {
                        echo '<div class="price out-of-stock">Vyprodáno</div>';
                    }
                    echo '</div>'; // End product-footer
                    echo '</div>'; // End product
                }
            } else {
                echo '<p>Žádné produkty nebyly nalezeny.</p>';
            }
            ?>
        </div>
    </div>
</div>

<footer>
    <p>© 2025 | <a href="obchodnipodminky.html">Obchodní podmínky</a> | <a href="pravidla.html">Pravidla ochrany soukromí</a></p>
    <p>Email: info@store.cz | Telefon: 777 666 555</p>
</footer>

</body>
</html>






