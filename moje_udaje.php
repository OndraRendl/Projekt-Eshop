<?php
session_start();

// Připojení k databázi
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "eshop";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Připojení selhalo: " . $conn->connect_error);
}

if (!isset($_SESSION['username'])) {
    header('Location: login.html'); // Přesměrování na přihlášení, pokud není přihlášen
    exit();
}

// Získání údajů o uživateli podle username
$username = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die("Uživatel nebyl nalezen.");
}

// Zpracování formuláře pro změnu údajů
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Získání hodnot z formuláře (nebo ponechání původních hodnot, pokud jsou prázdné)
    $new_first_name = !empty($_POST['first_name']) ? $_POST['first_name'] : $user['first_name'];
    $new_last_name = !empty($_POST['last_name']) ? $_POST['last_name'] : $user['last_name'];
    $new_password = !empty($_POST['password']) ? $_POST['password'] : $user['password'];

    // Aktualizace údajů v databázi
    $update_query = "UPDATE users SET first_name = ?, last_name = ?, password = ? WHERE username = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ssss", $new_first_name, $new_last_name, $new_password, $username);

    if ($update_stmt->execute()) {
        // Po úspěšné změně údajů obnovíme stránku, aby se zobrazily nové hodnoty
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "<p>Chyba při aktualizaci údajů.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moje údaje</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        /* Styling pro stránku */
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 20%;
            background-color: #333;
            color: white;
            padding: 20px;
            box-sizing: border-box;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #555;
        }

        .sidebar .divider {
            border-top: 1px solid #555;
            margin: 20px 0;
        }

        .content {
            margin-left: 20%;
            width: 80%;
            padding: 20px;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            font-weight: 500;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Styling pro ikonky a inputy */
        .password-container {
            display: flex;
            align-items: center;
        }

        .password-container input {
            padding: 8px;
            font-size: 16px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .password-container i {
            margin-left: 10px;
            cursor: pointer;
        }

        footer {
            padding: 20px;
            background-color: #333;
            color: #bbb;
            font-size: 0.9em;
            text-align: center;
            margin-top: 40px;
        }

        footer a {
            color: #bbb;
            text-decoration: none;
            margin: 0 10px;
        }

        footer a:hover {
            color: #fff;
        }

        .form-container {
            margin-top: 30px;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-container button {
            padding: 10px 20px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #555;
        }
        .sidebar a.active {
            background-color:#28a745; /* Zvolte barvu pro zvýraznění */
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>E-shop Apple</h2>
        <div class="divider"></div>
        <p>Uživatelské jméno: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
        <div class="divider"></div>
        <a href="uvod.php">Úvod</a>
        <a href="obchod.php">Obchod</a>
        <a href="kontakt.php">Kontakt</a>
        <a href="kosik.php">Košík 🛒</a>
        <div class="divider"></div>
        <a href="moje_udaje.php" class="active">Můj účet</a>
        <a href="orders.php">Moje objednávky</a>
        <a href="server.php?action=logout" class="logout-btn">Odhlásit se</a>
        <a href="javascript:void(0);" onclick="confirmDelete()">Odstranit účet</a>
    </div>

    <!-- Content -->
    <div class="content">
        <h2>Můj účet</h2>
        <table>
            <tr>
                <th>Jméno</th>
                <td><?= htmlspecialchars($user['first_name']) ?></td>
            </tr>
            <tr>
                <th>Příjmení</th>
                <td><?= htmlspecialchars($user['last_name']) ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= htmlspecialchars($user['email']) ?></td>
            </tr>
            <tr>
                <th>Heslo</th>
                <td class="password-container">
                    <input type="password" id="password" value="<?= htmlspecialchars($user['password']) ?>" readonly>
                    <i id="toggle-password" onclick="togglePassword()">👁️</i>
                </td>
            </tr>
            <tr>
                <th>Vytvořeno</th>
                <td><?= htmlspecialchars($user['created_at']) ?></td>
            </tr>
        </table><br><br>

        <!-- Formulář pro změnu údajů -->
        <div class="form-container">
            <h3>Změnit údaje</h3>
            <form method="POST" action="">
                <label for="first_name">Jméno (nepovinné)</label>
                <input type="text" id="first_name" name="first_name" placeholder="Zadejte nové jméno">

                <label for="last_name">Příjmení (nepovinné)</label>
                <input type="text" id="last_name" name="last_name" placeholder="Zadejte nové příjmení">

                <label for="password">Nové heslo (nepovinné)</label>
                <input type="password" id="password" name="password" placeholder="Zadejte nové heslo">

                <button type="submit">Uložit změny</button>
            </form>
        </div>
    </div>
</div>

<footer>
    <p>© 2025 | <a href="obchodnipodminky.html">Obchodní podmínky</a> | <a href="pravidla.html">Pravidla ochrany soukromí</a></p>
    <p>Email: info@store.cz | Telefon: 777 666 555</p>
</footer>

<!-- JavaScript pro potvrzení smazání účtu a zobrazení/skrytí hesla -->
<script>
    function confirmDelete() {
        if (confirm("Opravdu chcete smazat svůj účet? Tuto akci nelze vrátit!")) {
            window.location.href = "server.php?action=delete_account"; // Odeslání požadavku na server
        }
    }

    function togglePassword() {
        const passwordField = document.getElementById('password');
        const passwordIcon = document.getElementById('toggle-password');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            passwordIcon.textContent = '🙈'; // Změní ikonu na "skrytí" hesla
        } else {
            passwordField.type = 'password';
            passwordIcon.textContent = '👁️'; // Změní ikonu na "zobrazení" hesla
        }
    }
</script>

</body>
</html>


