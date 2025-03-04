<?php
session_start();
require_once 'db_connection.php'; // Zde připojte soubor s připojením k databázi

// Pokud je uživatel přihlášen, načtěte jeho údaje
$userEmail = '';
$userFullName = ''; // Přidáme proměnnou pro jméno a příjmení

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Načteme jméno, příjmení a email z databáze
    $stmt = $pdo->prepare("SELECT first_name, last_name, email FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $userEmail = htmlspecialchars($user['email']);
        $userFullName = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); // Spojíme jméno a příjmení
    }
}

// Zpracování odeslání formuláře
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Uložení údajů o objednávce do session
    $_SESSION['order_details'] = [
        'name' => $_POST['name'],
        'address' => $_POST['address'],
        'city' => $_POST['city'],
        'zip' => $_POST['zip'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'payment_method' => $_POST['payment_method'],
        'shipping_method' => $_POST['shipping_method'] // Uložení dopravy
    ];

    // Přesměrování na stránku s přehledem objednávky
    header('Location: order_summary.php');
    exit();
}

// Předpokládáme, že košík je již naplněn
if (empty($_SESSION['cart'])) {
    header('Location: kosik.php'); // Pokud není nic v košíku, přesměrujeme na košík
    exit();
}

// Výpočet celkové ceny bez slevy
$totalPrice = 0;
foreach ($_SESSION['cart'] as $product_id => $product) {
    $totalPrice += $product['price'] * $product['quantity'];
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Objednávka - E-shop</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: black;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
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

        .main-section {
            display: flex;
            gap: 20px;
            margin-top: 30px;
        }

        .form-section, .cart-section {
            flex: 1;
            padding: 20px;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .form-section h2, .cart-section h2 {
            margin-bottom: 20px;
        }

        .input-field {
            margin-bottom: 15px;
            width: 98%;
        }

        .input-field input, .input-field select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1em;
        }

        .input-field label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .cart-summary table {
            width: 100%;
            border-collapse: collapse;
        }

        .cart-summary th, .cart-summary td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        .cart-summary th {
            background-color: #f4f4f4;
        }

        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }

        .order-button {
            margin-top: 20px;
            background-color: #28a745;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.2em;
            display: block;
            width: 100%;
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

    <h1>Potvrzení objednávky</h1>

    <!-- Order Progress -->
    <div class="order-progress">
        <div><a href="kosik.php">Košík</a></div>
        <div class="active">Kontaktní údaje, doprava, platba</div>
        <div>Potvrzení objednávky</div>
    </div>

    <div class="main-section">
        <!-- Form Section -->
        <div class="form-section">
            <h2>Údaje o doručení</h2>
            <form action="checkout.php" method="POST">
                <div class="input-field">
                    <label for="name">Jméno a příjmení</label>
                    <input type="text" id="name" name="name" required value="<?php echo $userFullName; ?>"
                        <?php echo isset($_SESSION['username']) ? 'readonly style="font-weight: bold;"' : ''; ?>>
                </div>
                <div class="input-field">
                    <label for="address">Adresa</label>
                    <input type="text" id="address" name="address" required>
                </div>
                <div class="input-field">
                    <label for="city">Město</label>
                    <input type="text" id="city" name="city" required>
                </div>
                <div class="input-field">
                    <label for="zip">PSČ</label>
                    <input type="text" id="zip" name="zip" required>
                </div>
                <div class="input-field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required value="<?php echo $userEmail; ?>"
                        <?php echo isset($_SESSION['username']) ? 'readonly style="font-weight: bold;"' : ''; ?>>
                </div>
                <div class="input-field">
                    <label for="phone">Telefonní číslo</label>
                    <input type="tel" id="phone" name="phone" required pattern="[0-9]{9}" title="Telefonní číslo musí obsahovat 9 číslic">
                </div>



                <!-- Způsob dopravy -->
                <h2>Způsob dopravy</h2>
                <div class="input-field">
                    <label for="shipping_method">Vyberte způsob dopravy</label>
                    <select id="shipping_method" name="shipping_method" required>
                        <option value="courier">Doručení kurýrem na adresu</option>
                        <option value="pickup">Osobní odběr (na adrese Milevská 2094/3, 140 00 Praha 4 - Krč)</option>
                    </select>
                </div>

                <!-- Způsob platby -->
                <h2>Způsob platby</h2>
                <div class="input-field">
                    <label for="payment_method">Vyberte způsob platby</label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="card">Platební karta</option>
                        <option value="cash">Platba na dobírku</option>
                    </select>
                </div>

                <button type="submit" class="order-button">Potvrdit objednávku</button>
            </form>
        </div>

        <!-- Cart Section -->
        <div class="cart-section">
            <h2>Souhrn objednávky</h2>
            <div class="cart-summary">
                <table>
                    <tr>
                        <th>Produkt</th>
                        <th>Obrázek</th>
                        <th>Cena</th>
                        <th>Množství</th>
                        <th>Celkem</th>
                    </tr>
                    <?php
                    foreach ($_SESSION['cart'] as $product_id => $product) {
                        $productTotal = $product['price'] * $product['quantity'];
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Produkt" class="product-image"></td>
                        <td><?php echo number_format($product['price'], 0, ',', ' ') . " Kč"; ?></td>
                        <td><?php echo $product['quantity']; ?></td>
                        <td><?php echo number_format($productTotal, 0, ',', ' ') . " Kč"; ?></td>
                    </tr>
                    <?php } ?>
                </table>
                <p><strong>Celková cena: <?php echo number_format($totalPrice, 0, ',', ' ') . " Kč"; ?></strong></p>
            </div>
        </div>
    </div>

</div>
</body>
</html>
