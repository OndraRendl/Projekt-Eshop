<?php
session_start(); // Start session, abychom mohli pracovat s proměnnými session

// Funkce pro odstranění položky z košíku
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    unset($_SESSION['cart'][$product_id]); // Odstraní produkt z košíku
    header('Location: kosik.php');
    exit();
}

// Funkce pro aktualizaci množství v košíku
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int) $_POST['quantity'];

    if ($quantity > 0 && isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
    }
    header('Location: kosik.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-shop - Košík</title>
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
            min-height: 100vh;
            background-image: url('1.avif');
            background-size: cover;
            background-position: center;
            padding-top: 80px;
            padding-bottom: 80px;
            box-sizing: border-box;
        }

        .overlay {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
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

        nav .auth-links a {
            color: white;
            text-decoration: none;
            margin: 0 20px;
            font-size: 1em;
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

        .cart-items {
            margin-top: 50px;
            width: 90%;
            margin: 0 auto;
        }

        .cart-header {
            display: grid;
            grid-template-columns: 60px 1fr 150px 150px 150px 60px;
            align-items: center;
            gap: 20px;
            font-size: 1.2em;
            font-weight: bold;
            color: white;
            margin-bottom: 20px;
        }

        .cart-item {
            background: rgba(255, 255, 255, 0.3);
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            display: grid;
            grid-template-columns: 60px 1fr 150px 150px 150px 60px;
            align-items: center;
            gap: 20px;
        }

        .cart-item img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .cart-item p {
            color: white;
            font-size: 1.2em;
            margin: 0;
            text-align: left;
        }

        .quantity-btn {
            background-color: #fff;
            color: black;
            border: none;
            padding: 5px 10px;
            font-size: 1em;
            cursor: pointer;
            margin: 0 5px;
            border-radius: 5px;
        }

        .quantity-btn:hover {
            background-color: #ddd;
        }

        .remove-btn {
            color: red;
            text-decoration: none;
            font-size: 1.5em;
            cursor: pointer;
        }

        .button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #fff;
            color: black;
            border-radius: 5px;
            text-decoration: none;
        }

        footer {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 0.9em;
            color: #bbb;
            padding: 20px;
            margin-top: 20px;
            background-color: rgba(0, 0, 0, 0.8);
        }

        footer a {
            color: #bbb;
            text-decoration: none;
        }
        .image-label1 {
            display: inline-block; /* Můžete použít block, inline nebo inline-block */
            margin-left: 70px;     /* Posunutí doprava */
            font-size: 1em;      /* Zvětšení textu */
            color: white;          /* Barva textu */
        }

        .image-label2 {
            display: inline-block; /* Můžete použít block, inline nebo inline-block */
            margin-left: 1050px;     /* Posunutí doprava */
            font-size: 1em;      /* Zvětšení textu */
            color: white;          /* Barva textu */
        }

        .image-label3 {
            display: inline-block; /* Můžete použít block, inline nebo inline-block */
            margin-left: 135px;     /* Posunutí doprava */
            font-size: 1em;      /* Zvětšení textu */
            color: white;          /* Barva textu */
        }

        .image-label4 {
            display: inline-block; /* Můžete použít block, inline nebo inline-block */
            margin-left: 100px;     /* Posunutí doprava */
            font-size: 1em;      /* Zvětšení textu */
            color: white;          /* Barva textu */
        }
        .price-details {
            font-size: 1em;
            color: white;
            margin-top: 10px;
            text-align: left;
        }

        .continue-shopping {
            text-decoration: underline;
            color: white;
            cursor: pointer;
        }

        .order-button {
            background-color: white;
            color: black;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }

        .order-button:hover {
            background-color: #ddd;
        }

        .cart-summary {
            margin-top: 30px;
            text-align: center;
            font-size: 1.2em;
            color: white;
        }
        .cart-summary {
            margin-top: 30px;
            text-align: right; /* Zarovnání textu do prava */
            font-size: 1.2em;
            color: white;
            padding-left: 1300px; /* Odsazení od pravého okraje */
        }

        .cart-summary p {
            margin: 10px 0; /* Vytvoření mezery mezi jednotlivými řádky */
        }


        
    </style>
</head>
<body>

    <nav>
        <div class="auth-links">
            <?php if (isset($_SESSION['username'])): ?>
                <span class="username">Uživatel: <?php echo htmlspecialchars($_SESSION['username']); ?></span> <!-- Zobrazení uživatelského jména -->
                <?php if ($_SESSION['username'] === 'admin'): ?> <!-- Pokud je přihlášen admin -->
                    <a href="admin.php" class="admin-btn">Správa produktů</a> <!-- Odkaz pro správu produktů -->
                <?php endif; ?>
                <a href="server.php?action=logout" class="logout-btn">Odhlásit se</a>

            <?php else: ?>
                <a href="login.html" class="login-btn">Přihlásit se</a>
                <a href="register.html" class="register-btn">Registrovat se</a>
            <?php endif; ?>
            </div>
            <div>
                <a href="uvod.php">Úvod</a>
                <a href="obchod.php">Obchod</a>
                <a href="kontakt.php">Kontakt</a>
                <span class="divider"></span>
                <a href="kosik.php" class="active">Košík 🛒</a>
            </div>
    </nav>

    <div class="background">
        <div class="overlay">
            <h1>Nákupní košík</h1>

            <?php if (!empty($_SESSION['cart'])): ?>
                    <div class="cart-header">
                        <span class="image-label1">Produkt</span>
                        <span class="image-label2">Cena</span>
                        <span class="image-label3">Množství</span>
                        <span class="image-label4">Celkem</span>
                        <span></span>
                    </div>
                <div class="cart-items">
                    <?php
                    $totalPrice = 0; // Celková cena
                    $totalWithoutVAT = 0; // Cena bez DPH
                    $totalVAT = 0; // DPH
                    foreach ($_SESSION['cart'] as $product_id => $product):
                        $productTotal = $product['price'] * $product['quantity'];
                        $totalPrice += $productTotal;
                        $totalWithoutVAT += $productTotal / 1.21;
                        $totalVAT += $productTotal - ($productTotal / 1.21);
                    ?>
                        <div class="cart-item">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Produkt">
                            <p><?php echo htmlspecialchars($product['name']); ?></p>
                            <p><?php echo number_format($product['price'], 0, ',', ' ') . " Kč"; ?></p>
                            <form action="kosik.php" method="post">
                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                <button type="button" onclick="updateQuantity(this, 'decrease')" class="quantity-btn">-</button>
                                <span id="quantity-<?php echo $product_id; ?>"><?php echo $product['quantity']; ?></span>
                                <button type="button" onclick="updateQuantity(this, 'increase')" class="quantity-btn">+</button>
                            </form>
                            <p><?php echo number_format($productTotal, 0, ',', ' ') . " Kč"; ?></p>
                            <a href="?remove=<?php echo $product_id; ?>" class="remove-btn">✖</a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-summary">
                <p>Včetně DPH: &nbsp&nbsp<?php echo number_format($totalPrice, 2, ',', ' ') . " Kč"; ?></p>
                <p>Bez DPH: &nbsp&nbsp<?php echo number_format($totalWithoutVAT, 2, ',', ' ') . " Kč"; ?></p>
                <p>DPH 21 %: &nbsp&nbsp<?php echo number_format($totalVAT, 2, ',', ' ') . " Kč"; ?></p>
                <p><strong>CELKEM: &nbsp&nbsp<?php echo number_format($totalPrice, 2, ',', ' ') . " Kč"; ?></strong></p><br>

                    
                    <a href="obchod.php" class="order-button">Pokračovat v nákupu</a>
                    <a href="checkout.php" class="order-button" >Objednat</a>
                    
                </div>
            <?php else: ?>
                <p>Košík je zatím prázdný. <br>Vy to ale můžete změnit. Vyberte si z naší nabídky.</p><br>
                <a href="obchod.php" class="order-button continue-shopping">Pokračovat v nákupu</a>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>© 2025 | <a href="obchodnipodminky.html">Obchodní podmínky</a> | <a href="pravidla.html">Pravidla ochrany soukromí</a></p>
        <p>Email: info@store.cz | Telefon: 777 666 555</p>
    </footer>

    <script>
        function updateQuantity(button, action) {
            const productId = button.closest('form').querySelector('input[name="product_id"]').value;
            const quantitySpan = document.getElementById('quantity-' + productId);
            let quantity = parseInt(quantitySpan.textContent, 10);

            if (action === 'decrease' && quantity > 1) {
                quantity--;
            } else if (action === 'increase') {
                quantity++;
            }

            quantitySpan.textContent = quantity;

            const form = button.closest('form');
            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = 'quantity';
            quantityInput.value = quantity;
            form.appendChild(quantityInput);
            form.submit();
        }
    </script>

</body>
</html>
    





