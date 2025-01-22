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
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-shop - obchod</title>
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
            min-height: calc(100vh - 100px); /* Zajistí, že pozadí se nevytáhne pod lištu */
            padding-bottom: 20px;
        }

        .background {
            position: relative;
            width: 100%;
            height: auto;
            background-image: url('1.avif');
            background-size: cover;
            background-position: center;
            margin-top: 100px; /* Posune obsah pod lištu */
        }

        .overlay {
            padding: 20px;
            padding-top: 50px;
            box-sizing: border-box;
        }

        nav {
            position: absolute; /* Fixní pozice */
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

        .products {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
            min-height: 100vh; /* Zajistí, že produkty zaplní dostupnou výšku */
        }

        .product {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            width: 250px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            margin-bottom: 20px; /* Přidá mezery mezi produkty */
        }

        .product img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }

        .product h3 {
            margin: 10px 0;
        }

        .product p {
            margin: 10px 0;
            font-size: 0.9em;
            color: #ccc;
        }

        .product .price {
            font-size: 1.2em;
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
            <h1>Náš obchod</h1>

            <div class="products">
                <?php
                // Načtení produktů z databáze
                $sql = "SELECT * FROM produkty";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="product">';
                        echo '<a href="produkt.php?id=' . $row['id'] . '">';
                        echo '<img src="' . htmlspecialchars($row['obrazek']) . '" alt="Produkt">';
                        echo '<h3>' . htmlspecialchars($row['nazev']) . '</h3>';
                        echo '<p>' . htmlspecialchars($row['popis']) . '</p>';
                        echo '<div class="price">' . htmlspecialchars($row['cena']) . ' Kč</div>';
                        echo '</a>';
                        echo '</div>';
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




