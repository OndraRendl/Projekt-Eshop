<?php
session_start(); // Start session, abychom mohli pracovat s proměnnými session
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
            height: 100vh;
            background-image: url('1.avif');
            background-size: cover;
            background-position: center;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            padding: 20px;
            padding-top: 50px; /* Posun nadpisu níže */
            box-sizing: border-box;
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
            color: white; /* Barva textu */
            font-size: 1em; /* Velikost písma */
            margin-right: 20px; /* Odstup z pravé strany */
            font-weight: bold; /* Zvýraznění textu */
            margin-left: 30px; /* Posunout o 30px od levého okraje */
        }

        h1 {
            font-size: 3em;
            margin-bottom: 20px;
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
                <span class="username">Uživatel: <?php echo htmlspecialchars($_SESSION['username']); ?></span> <!-- Zobrazení uživatelského jména -->
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
            <h1>Náš Obchod</h1>
        </div>
    </div>

    <footer>
        <p>© 2025 | <a href="obchodnipodminky.html">Obchodní podmínky</a> | <a href="pravidla.html">Pravidla ochrany soukromí</a></p>
        <p>Email: info@store.cz | Telefon: 777 555 444</p>
    </footer>

</body>
</html>


