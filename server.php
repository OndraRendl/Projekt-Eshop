<?php
// Zapnutí zobrazování chyb pro lepší debugování
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Startování session na začátku skriptu
session_start();

// Připojení k databázi
$host = 'localhost';
$dbname = 'uzivatele';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Chyba připojení k databázi: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    // Zpracování registrace
    if ($action === 'register') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password']; // Heslo bez šifrování - pro produkci by mělo být šifrováno

        // Kontrola, zda už uživatel existuje
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        if ($stmt->rowCount() > 0) {
            echo "Uživatelské jméno nebo email je již zaregistrován.";
            exit;
        }

        // Uložení nového uživatele do databáze
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->execute(['username' => $username, 'email' => $email, 'password' => $password]);

        // Uložení uživatele do session
        $_SESSION['username'] = $username;

        // Přesměrování na obchod.php
        header('Location: obchod.php');
        exit;
    }

    // Zpracování přihlášení
    if ($action === 'login') {
        $username_or_email = $_POST['username'];
        $password = $_POST['password'];

        // Kontrola, zda uživatel existuje a porovnání hesla
        $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = :username OR email = :email) AND password = :password");
        $stmt->execute(['username' => $username_or_email, 'email' => $username_or_email, 'password' => $password]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            // Kontrola, zda je uživatel admin
            if ($user['username'] === 'admin' && $user['password'] === 'admin') {
                $_SESSION['username'] = $user['username'];
                // Přesměrování na admin stránku
                header('Location: admin.php');
                exit;
            } else {
                $_SESSION['username'] = $user['username'];
                // Přesměrování na běžnou stránku (obchod)
                header('Location: obchod.php');
                exit;
            }
        }

        echo "Nesprávné uživatelské jméno nebo heslo.";
    }
}

// Zpracování akcí přes GET parametry
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action'])) {
        $action = $_GET['action'];

        // Odhlášení uživatele
        if ($action === 'logout') {
            session_destroy();
            header('Location: uvod.php');
            exit;
        }

        // Odstranění účtu
        if ($action === 'delete_account' && isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            $stmt = $pdo->prepare("DELETE FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);

            // Odhlášení uživatele po odstranění účtu
            session_destroy();
            header('Location: uvod.php');
            exit;
        }
    }
}
?>





