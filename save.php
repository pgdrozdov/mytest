<?php
// save.php
// Скрипт для сохранения результатов теста в PostgreSQL

// Настройки подключения к БД
$host = 'localhost';
$port = '5432';
$dbname = 'blog';
$user = 'blog';
$password = 'xsw2cde3zaq1';

// Включаем отображение ошибок только для отладки (на продакшене отключить)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Проверяем метод отправки
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Ошибка: форма должна быть отправлена методом POST');
}

// Функция логирования ошибок
function logError($message) {
    $logFile = __DIR__ . '/logs/errors.log';
    $dir = dirname($logFile);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents($logFile, '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL, FILE_APPEND);
}

// Получаем и очищаем входные данные
$fio      = isset($_POST['fio']) ? trim($_POST['fio']) : '';
$predmet  = isset($_POST['predmet']) ? trim($_POST['predmet']) : '';
$tdate    = isset($_POST['tdate']) ? trim($_POST['tdate']) : '';
$nazv     = isset($_POST['nazv']) ? trim($_POST['nazv']) : '';
$time     = isset($_POST['time']) ? $_POST['time'] : '';
$grade    = isset($_POST['grade']) ? $_POST['grade'] : '';

// Валидация обязательных полей
if (empty($fio)) {
    die('Ошибка: поле ФИО обязательно для заполнения');
}
if ($grade === '' || !is_numeric($grade)) {
    die('Ошибка: оценка должна быть указана и являться числом');
}

// Приведение типов и ограничение длины
$fio = mb_substr($fio, 0, 25, 'UTF-8');          // CHAR(25) в БД
$predmet = mb_substr($predmet, 0, 255, 'UTF-8');
$nazv = mb_substr($nazv, 0, 255, 'UTF-8');
$grade = (int)$grade;

// Обработка даты: ожидается строка в формате "ГГГГ-ММ-ДД ЧЧ:ММ:СС"
if (empty($tdate)) {
    $tdate = date('Y-m-d H:i:s');
} else {
    try {
        $dateTime = new DateTime($tdate);
        $tdate = $dateTime->format('Y-m-d H:i:s');
    } catch (Exception $e) {
        die('Ошибка: неверный формат даты (требуется ГГГГ-ММ-ДД ЧЧ:ММ:СС)');
    }
}

// Обработка времени выполнения (целое число секунд)
$time = is_numeric($time) ? (int)$time : 0;

// DSN для PostgreSQL
$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";

try {
    // Подключение к БД
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET client_encoding TO 'UTF8'");

    // Проверка существования таблицы
    $tableCheck = $pdo->query("SELECT to_regclass('public.arh')");
    if (!$tableCheck || $tableCheck->fetchColumn() === null) {
        throw new Exception("Таблица 'arh' не существует в базе данных. Создайте её или проверьте имя.");
    }

    // Подготовка и выполнение запроса
    $sql = "INSERT INTO arh (fio, tdate, time, predmet, nazv, grade) 
            VALUES (:fio, :tdate, :time, :predmet, :nazv, :grade)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':fio'     => $fio,
        ':tdate'   => $tdate,
        ':time'    => $time,
        ':predmet' => $predmet,
        ':nazv'    => $nazv,
        ':grade'   => $grade
    ]);

    // Успех – выводим сообщение и кнопки навигации
    echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Результат сохранения</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding-top: 50px; }
        .success { color: green; font-weight: bold; margin-bottom: 20px; }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div class="success">✅ Данные успешно сохранены в базе.</div>
    <div>
        <a href="index.html" class="btn">📝 Меню</a>
        <a href="show.php?fio=' . urlencode($fio) . '" class="btn">👁️ Просмотр (текущая фамилия)</a>
    </div>
</body>
</html>';

} catch (PDOException $e) {
    logError("Ошибка базы данных: " . $e->getMessage());
    die("<p style='color: red; text-align: center;'>❌ Не удалось сохранить данные. Пожалуйста, обратитесь к администратору.</p>");
} catch (Exception $e) {
    logError("Общая ошибка: " . $e->getMessage());
    die("<p style='color: red; text-align: center;'>❌ Ошибка: " . htmlspecialchars($e->getMessage()) . "</p>");
}
?>