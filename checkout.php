<?php
session_start();

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
        'payment_method' => $_POST['payment_method']
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

        .section {
            margin-top: 30px;
            padding: 20px;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .section h2 {
            margin-bottom: 20px;
        }

        .input-field {
            margin-bottom: 15px;
            width: 100%;
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

        .cart-summary {
            margin-top: 20px;
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

    <div class="section">
        <h2>Údaje o doručení</h2>
        <form action="checkout.php" method="POST">
            <div class="input-field">
                <label for="name">Jméno a příjmení</label>
                <input type="text" id="name" name="name" required>
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
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-field">
                <label for="phone">Telefonní číslo</label>
                <input type="tel" id="phone" name="phone" required>
            </div>

            <h2>Způsob platby</h2>
            <div class="input-field">
                <label for="payment_method">Vyberte způsob platby</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="card">Platební karta</option>
                    <option value="cash">Platba na dobírku</option>
                </select>
            </div>

            <h2>Souhrn objednávky</h2>
            <div class="cart-summary">
                <table>
                    <tr>
                        <th>Produkt</th>
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
                        <td><?php echo number_format($product['price'], 0, ',', ' ') . " Kč"; ?></td>
                        <td><?php echo $product['quantity']; ?></td>
                        <td><?php echo number_format($productTotal, 0, ',', ' ') . " Kč"; ?></td>
                    </tr>
                    <?php } ?>
                </table>
                <p><strong>Celková cena: <?php echo number_format($totalPrice, 0, ',', ' ') . " Kč"; ?></strong></p>
            </div>

            <button type="submit" class="order-button">Potvrdit objednávku</button>
        </form>
    </div>

</div>

<footer class="footer">
    <p>© 2025 | <a href="obchodnipodminky.html">Obchodní podmínky</a> | <a href="pravidla.html">Pravidla ochrany soukromí</a></p>
    <p>Email: info@store.cz | Telefon: 777 666 555</p>
</footer>

</body>
</html>





