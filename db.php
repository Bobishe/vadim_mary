<?php
/* Подключение к БД -------------------------------------------------------- */
$host    = 'localhost';
$db      = 'zenyaobk_vadim';
$user    = 'zenyaobk_vadim';
$pass    = '*qx9q9kVdzf0';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}

/* Определяем, из какой формы пришёл запрос ------------------------------- */
$formName = $_POST['tildaspec-formname'] ?? '';
$name     = trim($_POST['name'] ?? '');               // имя гостя
$alcohol  = isset($_POST['alcohol']) ? trim($_POST['alcohol']) : null;

/*
 * Логика:
 * 1. У формы «Я приду» всегда передаётся поле alcohol (выбор напитка),
 *    у формы «Я не приду» такого поля нет.
 * 2. Дополнительно подстраховываемся по тексту tildaspec‑formname.
 */
$isYesForm = $alcohol !== null ||
             preg_match('/я\s*приду/ui', $formName);
$isNoForm  = !$isYesForm;   // любое другое сообщение считаем «не приду»

/* Сохраняем данные -------------------------------------------------------- */
if ($isYesForm) {
    $stmt = $pdo->prepare(
        'INSERT INTO rsvp_yes (name, alcohol) VALUES (?, ?)'
    );
    $stmt->execute([$name, $alcohol]);
} else {
    $stmt = $pdo->prepare(
        'INSERT INTO rsvp_no (name) VALUES (?)'
    );
    $stmt->execute([$name]);
}

/* Редирект обратно на сайт ----------------------------------------------- */
header('Location: index.html');
exit;
?>
