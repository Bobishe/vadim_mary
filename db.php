<?php
// Database connection settings
$host = 'localhost';
$db   = 'wedding';
$user = 'username';
$pass = 'password';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}

// Determine which form was submitted
$formName = $_POST['tildaspec-formname'] ?? '';
$name = trim($_POST['name'] ?? '');
$alcohol = isset($_POST['alcohol']) ? trim($_POST['alcohol']) : null;

if ($formName === 'форма с кнопки "Я приду"') {
    // Guest will attend
    $stmt = $pdo->prepare('INSERT INTO rsvp_yes (name, alcohol) VALUES (?, ?)');
    $stmt->execute([$name, $alcohol]);
} else {
    // Guest will not attend
    $stmt = $pdo->prepare('INSERT INTO rsvp_no (name) VALUES (?)');
    $stmt->execute([$name]);
}

header('Location: index.html');
exit;
?>
