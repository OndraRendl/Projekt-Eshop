<?php
// Zapnutí zobrazování chyb pro lepší debugování
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Startování session na začátku skriptu
session_start();

// Cesta k souboru pro ukládání uživatelských dat
$file = 'users.txt';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    // Zpracování registrace
    if ($action === 'register') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password']; // Heslo bez šifrování

        // Kontrola, zda už uživatel existuje
        $users = file($file, FILE_IGNORE_NEW_LINES);
        foreach ($users as $user) {
            $user_data = explode(',', $user);

            // Ověření, zda řádek obsahuje alespoň dvě položky (username, email)
            if (count($user_data) >= 2) {
                list($saved_username, $saved_email) = $user_data;

                if ($saved_username === $username || $saved_email === $email) {
                    echo "Uživatelské jméno nebo email je již zaregistrován.";
                    exit;
                }
            }
        }

        // Uložení nového uživatele do souboru (každý uživatel na jednom řádku)
        file_put_contents($file, "$username,$email,$password\n", FILE_APPEND);

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
        $users = file($file, FILE_IGNORE_NEW_LINES);
        foreach ($users as $user) {
            $user_data = explode(',', $user);

            // Ověření, že řádek obsahuje správný počet položek (username, email, password)
            if (count($user_data) == 3) {
                list($saved_username, $saved_email, $saved_password) = $user_data;

                if (($saved_username === $username_or_email || $saved_email === $username_or_email) && $saved_password === $password) {
                    // Kontrola, zda je uživatel admin
                    if ($saved_username === 'admin' && $saved_password === 'admin') {
                        $_SESSION['username'] = $saved_username;
                        // Přesměrování na admin stránku
                        header('Location: admin.php');
                        exit;
                    } else {
                        $_SESSION['username'] = $saved_username;
                        // Přesměrování na běžnou stránku (obchod)
                        header('Location: obchod.php');
                        exit;
                    }
                }
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
            $users = file($file, FILE_IGNORE_NEW_LINES);
            $updated_users = array_filter($users, function ($user) use ($username) {
                list($saved_username) = explode(',', $user);
                return $saved_username !== $username;
            });

            // Uložení aktualizovaného seznamu uživatelů
            file_put_contents($file, implode("\n", $updated_users) . "\n");

            // Odhlášení uživatele po odstranění účtu
            session_destroy();
            header('Location: uvod.php');
            exit;
        }
    }
}
?>




