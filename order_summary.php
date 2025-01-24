<?php
session_start();

// Zkontrolujeme, jestli jsou k dispozici údaje o objednávce
if (!isset($_SESSION['order_details']) || empty($_SESSION['order_details'])) {
    header('Location: checkout.php'); // Pokud neexistují, přesměrujeme zpět na checkout
    exit();
}

$orderDetails = $_SESSION['order_details'];

// Připojení k databázi (nezapomeňte změnit podle vašich údajů)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "objednavky"; // Změňte na název vaší databáze

$conn = new mysqli($servername, $username, $password, $dbname);

// Zkontrolujeme připojení
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Odeslání objednávky do databáze po kliknutí na tlačítko "Objednat"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vložení objednávky do tabulky
    $name = $orderDetails['name'];
    $address = $orderDetails['address'];
    $city = $orderDetails['city'];
    $zip = $orderDetails['zip'];
    $email = $orderDetails['email'];
    $phone = $orderDetails['phone'];
    $payment_method = $orderDetails['payment_method'];
    $shipping_method = $orderDetails['shipping_method']; // Přidání dopravy
    $total_price = 0;

    // Výpočet celkové ceny
    foreach ($_SESSION['cart'] as $product_id => $product) {
        $total_price += $product['price'] * $product['quantity'];
    }

    // SQL dotaz pro vložení objednávky
    $sql = "INSERT INTO orders (name, address, city, zip, email, phone, payment_method, shipping_method, total_price)
            VALUES ('$name', '$address', '$city', '$zip', '$email', '$phone', '$payment_method', '$shipping_method', '$total_price')";

    if ($conn->query($sql) === TRUE) {
        // Po úspěšném vložení objednávky přesměrujeme na stránku s poděkováním
        unset($_SESSION['cart']); // Vyprázdnění košíku
        unset($_SESSION['order_details']); // Smazání údajů o objednávce
        header("Location: thank_you.php");
        exit();
    } else {
        echo "Chyba při odesílání objednávky: " . $conn->error;
    }

    $conn->close();
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

    <!-- Order Progress -->
    <div class="order-progress">
        <div><a>Košík</a></div>
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
            <p><strong>Telefonní číslo:</strong> <?php echo htmlspecialchars($orderDetails['phone']); ?></p>
            <p><strong>Způsob dopravy:</strong> 
                <?php 
                if ($orderDetails['shipping_method'] === 'courier') {
                    echo 'Kurýr';
                } elseif ($orderDetails['shipping_method'] === 'pickup') {
                    echo 'Osobní odběr';
                }
                ?>
            </p>
            <p><strong>Způsob platby:</strong> <?php echo $orderDetails['payment_method'] === 'card' ? 'Platební karta' : 'Platba na dobírku'; ?></p>
        </div>

        <h2>Souhrn objednávky</h2>
        <table>
            <tr>
                <th>Produkt</th>
                <th>Cena</th>
                <th>Množství</th>
                <th>Celkem</th>
            </tr>
            <?php
            $totalPrice = 0;
            foreach ($_SESSION['cart'] as $product_id => $product) {
                $productTotal = $product['price'] * $product['quantity'];
                $totalPrice += $productTotal;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo number_format($product['price'], 0, ',', ' ') . " Kč"; ?></td>
                <td><?php echo $product['quantity']; ?></td>
                <td><?php echo number_format($productTotal, 0, ',', ' ') . " Kč"; ?></td>
            </tr>
            <?php } ?>
        </table>
        <p><strong>Celková cena: <?php echo number_format($totalPrice, 0, ',', ' ') . " Kč"; ?></strong></p>

        <!-- Formulář pro odeslání objednávky -->
        <form action="order_summary.php" method="POST">
            <button type="submit" class="order-button">Objednat</button>
        </form>
    </div>

</div>

<footer class="footer">
    <p>© 2025 | <a href="obchodnipodminky.html">Obchodní podmínky</a> | <a href="pravidla.html">Pravidla ochrany soukromí</a></p>
    <p>Email: info@store.cz | Telefon: 777 666 555</p>
</footer>

</body>
</html>






