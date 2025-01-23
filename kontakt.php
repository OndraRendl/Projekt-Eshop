<?php
session_start(); // Start session, abychom mohli pracovat s proměnnými session
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-shop - kontakt</title>
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
            display: flex;
            flex-direction: column;
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
            margin-bottom: 40px;
            text-align: left;
            padding-left: 3cm;
            width: 50%;
            float: right;
            margin-top: 80px;
        }

        .contact-info {
            max-width: 800px;
            text-align: left;
            font-size: 1.2em;
            margin-bottom: 30px;
            padding-left: 3cm;
            width: 50%;
            float: left;
            line-height: 1.6;
        }

        .map-container {
            width: 50%;
            float: right;
            padding-right: 4.5cm;
        }

        .button {
            padding: 10px 30px;
            background-color: white;
            color: black;
            text-decoration: none;
            font-size: 1.2em;
            border-radius: 5px;
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
            <?php if ($_SESSION['username'] === 'admin'): ?> <!-- Pokud je přihlášen admin -->
                <a href="admin.php" class="admin-btn">Správa produktů</a> <!-- Odkaz pro správu produktů -->
            <?php endif; ?>
            <a href="server.php?action=logout" class="logout-btn">Odhlásit se</a>
            <a href="server.php?action=delete_account" class="delete-account-btn">Odstranit účet</a>

        <?php else: ?>
            <a href="login.html" class="login-btn">Přihlásit se</a>
            <a href="register.html" class="register-btn">Registrovat se</a>
        <?php endif; ?>
        </div>
        <div>
            <a href="uvod.php">Úvod</a>
            <a href="obchod.php">Obchod</a>
            <a href="kontakt.php" class="active">Kontakt</a>
            <span class="divider"></span>
            <a href="kosik.php">Košík 🛒</a>
        </div>
    </nav>

    <div class="background">
        <div class="overlay">
            <br><br><h1>Kontaktujte nás</h1>
            <div style="display: flex; justify-content: space-between; width: 100%;">
                <div class="contact-info">
                    <p><strong>Adresa:</strong><br>V TOVER Prague<br>Milevská 2094/3, 140 00 Praha 4 - Krč</p>
                    <p><strong>Telefon:</strong><br>777 666 555</p>
                    <p><strong>E-mail:</strong><br>info@store.cz</p>
                </div>
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3046.7248667868666!2d14.438922264153241!3d50.04920560588697!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x470b94751e16c6a1%3a0x740b067895ad75cf!2sv%20tower%2c%20prague!5e0!3m2!1scs!2scz!4v1737381294216!5m2!1scs!2scz" width="1000" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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





