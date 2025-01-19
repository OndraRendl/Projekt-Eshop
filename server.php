<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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
            list($saved_username, $saved_email) = explode(',', $user);
            if ($saved_username === $username || $saved_email === $email) {
                echo "Uživatelské jméno nebo email je již zaregistrován.";
                exit;
            }
        }

        // Uložení nového uživatele do souboru
        file_put_contents($file, "$username,$email,$password\n", FILE_APPEND);
        echo "Úspěšná registrace!";
    }

    // Zpracování přihlášení
    if ($action === 'login') {
        $username_or_email = $_POST['username'];
        $password = $_POST['password'];

        // Kontrola, zda uživatel existuje a porovnání hesla
        $users = file($file, FILE_IGNORE_NEW_LINES);
        foreach ($users as $user) {
            list($saved_username, $saved_email, $saved_password) = explode(',', $user);
            if (($saved_username === $username_or_email || $saved_email === $username_or_email) && $saved_password === $password) {
                $_SESSION['username'] = $saved_username;
                header('Location: obchod.html');
                exit;
            }
        }

        echo "Nesprávné uživatelské jméno nebo heslo.";
    }
}
?>



