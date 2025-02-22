<?php
session_start();

// Připojení k databázi
require_once 'db_connection.php'; 

// Pokud je uživatel přihlášen, načteme jeho údaje
$username = NULL;  // Změněno z user_id na username
$userFullName = '';

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];  // Získáme přihlášené uživatelské jméno

    // Načteme jméno a příjmení uživatele
    $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $userFullName = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
    }
}

// Pokud nejsou dostupné údaje o objednávce, přesměrujeme na checkout
if (!isset($_SESSION['order_details']) || empty($_SESSION['order_details'])) {
    header('Location: checkout.php');
    exit();
}

$orderDetails = $_SESSION['order_details'];

// Připojení k databázi objednávek a produktů
$servername = "localhost";
$username_db = "root";  // změněno na jiný název pro připojení k databázi
$password = "";

$connOrders = new mysqli($servername, $username_db, $password, "eshop");
if ($connOrders->connect_error) {
    die("Connection to objednavky failed: " . $connOrders->connect_error);
}

$connProducts = new mysqli($servername, $username_db, $password, "eshop");
if ($connProducts->connect_error) {
    die("Connection to e-shopapple failed: " . $connProducts->connect_error);
}

// Odeslání objednávky po kliknutí na tlačítko "Objednat"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Zpracování objednávky
    $name = $connOrders->real_escape_string($orderDetails['name']);
    $address = $connOrders->real_escape_string($orderDetails['address']);
    $city = $connOrders->real_escape_string($orderDetails['city']);
    $zip = $connOrders->real_escape_string($orderDetails['zip']);
    $email = $connOrders->real_escape_string($orderDetails['email']);
    $phone = $connOrders->real_escape_string($orderDetails['phone']);
    $payment_method = $connOrders->real_escape_string($orderDetails['payment_method']);
    $shipping_method = $connOrders->real_escape_string($orderDetails['shipping_method']);
    $total_price = 0;

    $productsList = [];

    foreach ($_SESSION['cart'] as $product_id => $product) {
        $quantityOrdered = $product['quantity'];

        // Kontrola skladových zásob
        $checkStockSql = "SELECT skladem FROM produkty WHERE id = $product_id";
        $result = $connProducts->query($checkStockSql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if ($row['skladem'] < $quantityOrdered) {
                echo "Produkt " . htmlspecialchars($product['name']) . " není skladem v požadovaném množství.";
                exit();
            }
        } else {
            echo "Produkt s ID $product_id nebyl nalezen.";
            exit();
        }

        // Aktualizace skladu
        $updateStockSql = "UPDATE produkty SET skladem = skladem - $quantityOrdered WHERE id = $product_id";
        if (!$connProducts->query($updateStockSql)) {
            echo "Chyba při aktualizaci skladu pro produkt " . htmlspecialchars($product['name']) . ": " . $connProducts->error;
            exit();
        }

        $productsList[] = $product['name'] . " (" . $product['quantity'] . " ks)";
        $total_price += $product['price'] * $product['quantity'];
    }

    $products = implode(", ", $productsList);

    // SQL dotaz pro uložení objednávky
    if ($username === NULL) {
        // Pokud uživatel není přihlášen, objednávku uložíme bez username
        $sqlOrder = "INSERT INTO orders (name, address, city, zip, email, phone, payment_method, total_price, shipping_method, products)
                     VALUES ('$name', '$address', '$city', '$zip', '$email', '$phone', '$payment_method', '$total_price', '$shipping_method', '$products')";
    } else {
        // Pokud uživatel je přihlášen, objednávku uložíme s jeho username
        $sqlOrder = "INSERT INTO orders (username, name, address, city, zip, email, phone, payment_method, total_price, shipping_method, products)
                     VALUES ('$username', '$name', '$address', '$city', '$zip', '$email', '$phone', '$payment_method', '$total_price', '$shipping_method', '$products')";
    }
    

    if ($connOrders->query($sqlOrder) === TRUE) {
        unset($_SESSION['cart']);
        unset($_SESSION['order_details']);
        header("Location: thank_you.php");
        exit();
    } else {
        echo "Chyba při ukládání objednávky: " . $connOrders->error;
    }

    $connOrders->close();
    $connProducts->close();
}
?>


<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Souhrn objednávky - E-shop</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: black;
            font-size: 2.5em;
        }
        .order-progress {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
        }
        .order-progress div {
            flex: 1;
            text-align: center;
            font-weight: bold;
            padding: 10px;
            border-radius: 50px;
        }
        .order-progress div.active {
            background-color: #007bff;
            color: white;
        }
        .order-progress div a {
            text-decoration: none;
            color: inherit;
        }
        .order-progress div:not(.active) {
            background-color: #f8f9fa;
        }
        .section {
            margin-top: 30px;
            padding: 20px;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .order-details p {
            font-size: 1.2em;
            line-height: 1.5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background-color: #f4f4f4;
        }
        .order-button {
            background-color: #28a745;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.2em;
            width: 100%;
            margin-top: 20px;
        }
        .order-button:hover {
            background-color: #218838;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 0.8em;
            color: #777;
        }
        .footer a {
            text-decoration: none;
            color: #777;
        }
    </style>
</head>
<body>

<div class="container">

    <h1>Souhrn objednávky</h1>

    <div class="order-progress">
        <div><a href="kosik.php">Košík</a></div>
        <div><a href="checkout.php">Kontaktní údaje, doprava, platba</a></div>
        <div class="active">Potvrzení objednávky</div>
    </div>

    <div class="section">
        <h2>Údaje o doručení</h2>
        <div class="order-details">
            <p><strong>Jméno a příjmení:</strong> <?php echo htmlspecialchars($orderDetails['name']); ?></p>
            <p><strong>Adresa:</strong> <?php echo htmlspecialchars($orderDetails['address']); ?></p>
            <p><strong>Město:</strong> <?php echo htmlspecialchars($orderDetails['city']); ?></p>
            <p><strong>PSČ:</strong> <?php echo htmlspecialchars($orderDetails['zip']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($orderDetails['email']); ?></p>
            <p><strong>Telefon:</strong> <?php echo htmlspecialchars($orderDetails['phone']); ?></p>
            <p><strong>Způsob platby:</strong> <?php echo htmlspecialchars($orderDetails['payment_method']); ?></p>
            <p><strong>Způsob dopravy:</strong> <?php echo htmlspecialchars($orderDetails['shipping_method']); ?></p>
        </div>
    </div>

    <div class="section">
        <h2>Seznam objednaných produktů</h2>
        <table>
            <tr>
                <th>Produkt</th>
                <th>Cena</th>
                <th>Množství</th>
            </tr>
            <?php foreach ($_SESSION['cart'] as $product) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo number_format($product['price'], 2, ',', ' '); ?> Kč</td>
                    <td><?php echo htmlspecialchars($product['quantity']); ?> ks</td>
                </tr>
            <?php $total_price = 0; // Inicializace proměnné pro celkovou cenu
$productsList = [];

foreach ($_SESSION['cart'] as $product_id => $product) {
    $quantityOrdered = $product['quantity'];

    // Kontrola skladových zásob
    $checkStockSql = "SELECT skladem FROM produkty WHERE id = $product_id";
    $result = $connProducts->query($checkStockSql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if ($row['skladem'] < $quantityOrdered) {
            echo "Produkt " . htmlspecialchars($product['name']) . " není skladem v požadovaném množství.";
            exit();
        }
    } else {
        echo "Produkt s ID $product_id nebyl nalezen.";
        exit();
    }

    // Aktualizace skladu
    $updateStockSql = "UPDATE produkty SET skladem = skladem - $quantityOrdered WHERE id = $product_id";
    if (!$connProducts->query($updateStockSql)) {
        echo "Chyba při aktualizaci skladu pro produkt " . htmlspecialchars($product['name']) . ": " . $connProducts->error;
        exit();
    }

    $productsList[] = $product['name'] . " (" . $product['quantity'] . " ks)";
    $total_price += $product['price'] * $product['quantity']; // Výpočet celkové ceny
}} ?>
        </table>

        <p><strong>Celková cena:</strong> <?php echo number_format($total_price, 2, ',', ' '); ?> Kč</p>
        <form action="" method="POST">
            <button class="order-button" type="submit">Potvrdit objednávku</button>
        </form>
    </div>

    <div class="footer">
        <p>&copy; 2025 E-shop Apple. Všechna práva vyhrazena.</p>
    </div>
    <?php


// Zkontrolujeme, jestli je uživatel přihlášen
if (isset($_SESSION['username'])) {
    echo "Ahoj, " . htmlspecialchars($_SESSION['username']) . "!";
} else {
    echo "Nejste přihlášen.";
}
?>

</div>

</body>
</html>







