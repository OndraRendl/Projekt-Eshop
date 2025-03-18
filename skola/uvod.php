<?php
session_start(); // Start session, abychom mohli pracovat s proměnnými session
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-shop Apple</title>
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
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 20px;
        }
        .overlay {
        padding: 20px;
        padding-top: 50px;
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

        nav .nav-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%); /* Uprostřed horizontálně */
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

        p {
            max-width: 800px;
            font-size: 1.4em;
            margin-bottom: 30px;
        }

        p1 {
            max-width: 800px;
            font-size: 0.9em;
            color: #bbb;
            padding: 5px;
            margin-top: 20px;
        }

        .button {
            padding: 10px 30px;
            background-color: white;
            color: black;
            text-decoration: none;
            font-size: 1.2em;
            border-radius: 5px;
        }

        .benefits {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .benefit {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 25px;
            width: 250px;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            color: white;
            margin: 0 10px;
        }

        .benefit:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
        }

        .benefit h3 {
            font-size: 1.6em;
            margin-bottom: 15px;
            color:rgb(255, 255, 255);
        }

        .benefit p {
            font-size: 1.1em;
            color: #ddd;
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
        <a href="uvod.php" class="active">Úvod</a>
        <a href="obchod.php">Obchod</a>
        <a href="kontakt.php">Kontakt</a>
        <span class="divider"></span>
        <a href="kosik.php">Košík 🛒</a>
    </div>
</nav>

<div class="background">
    <div class="overlay">
        <h1>E-SHOP S PRODUKTY FIRMY APPLE</h1>
        <p>Vítejte na našem e-shopu, kde najdete špičkové produkty od společnosti Apple. Objevte nejnovější modely iPhone, iPad, MacBook a Apple Watch, které kombinují elegantní design s nejmodernějšími technologiemi. Ať už hledáte výkon pro práci, zábavu, nebo stylové příslušenství, u nás si vyberete. Nabízíme také širokou škálu originálního příslušenství a doplňků, které dokonale doplní vaše zařízení Apple. Využijte naše akční nabídky a užijte si rychlé a bezpečné doručení zdarma!</p>
        <a href="obchod.php" class="button">Obchod</a><br>

        <!-- Čtyři obdélníky důvody proč nakupovat -->
        <div class="benefits">
            <div class="benefit">
                <h3>Rychlé doručení</h3>
                <p>Objednejte dnes a získejte rychlé a bezpečné doručení zdarma.</p>
            </div>
            <div class="benefit">
                <h3>Široký výběr</h3>
                <p>Naše nabídka zahrnuje všechny nejnovější modely Apple a příslušenství.</p>
            </div>
            <div class="benefit">
                <h3>Oficiální produkty</h3>
                <p>U nás nakoupíte pouze originální produkty Apple s plnou zárukou.</p>
            </div>
            <div class="benefit">
                <h3>Skvělé nabídky</h3>
                <p>Nabízíme dopravu zdarma na adresu nebo vyzvednutí na naší pobočce.</p></p>
            </div>
        </div>
    </div>
</div>

<footer>
    <p1>© 2025 | <a href="obchodnipodminky.html">Obchodní podmínky</a> | <a href="pravidla.html">Pravidla ochrany soukromí</a></p1>
    <p1>Email: info@store.cz | Telefon: 777 666 555</p1>
</footer>

</body>
</html>






