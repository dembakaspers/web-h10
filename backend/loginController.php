<?php
session_start();

// 1. Haal formulierdata op
$username = $_POST['username'];
$password = $_POST['password'];

require_once('../config.php'); // pad kan verschillen afhankelijk van jouw mapstructuur

try {
    // 1. Haal de verbinding erbij (staat in config.php)

    // 2. Schrijf de query met placeholders
    $query = "SELECT * FROM users WHERE username = :username";

    // 3. Zet om naar prepared statement
    $statement = $pdo->prepare($query);

    // 4. Voer het statement uit
    $statement->execute([
        ':username' => $username
    ]);

    // 5. Check of er een gebruiker is gevonden
    if ($statement->rowCount() < 1) {
        echo "Account niet gevonden";
        exit;
    }

    // Haal de gebruiker op
    $user = $statement->fetch(PDO::FETCH_ASSOC);

    // 6. Check wachtwoord
    if (!password_verify($password, $user['password'])) {
        echo "Wachtwoord klopt niet.";
        exit;
    }

    // 7. Alles klopt → sla gegevens op in session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['username'];

    // 8. Redirect naar hoofdpagina
    header("Location: ../index.php");
    exit;

} catch (PDOException $e) {
    echo "Fout: " . $e->getMessage();
    exit;
}
