<?php
// selShow.php
// Параметры подключения к базе данных PostgreSQL
$host = 'localhost';
$port = '5432';
$dbname = 'blog';
$user = 'blog';
$password = 'xsw2cde3zaq1';

// DSN для PDO PostgreSQL
$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";

try {
    // Создаем подключение PDO
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Получаем параметр фильтра из GET (если есть)
    $fioFilter = isset($_GET['fio']) ? trim($_GET['fio']) : '';

    // Формируем запрос в зависимости от наличия фильтра
    if ($fioFilter !== '') {
        // Используем ILIKE для регистронезависимого поиска подстроки
        $sql = "SELECT * FROM arh WHERE fio ILIKE :fio ORDER BY fio, tdate, time, predmet, nazv, grade ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['fio' => '%' . $fioFilter . '%']);
    } else {
        // Без фильтра — все записи
        $sql = "SELECT * FROM arh ORDER BY fio, tdate, time, predmet, nazv, grade ASC";
        $stmt = $pdo->query($sql);
    }

    $rows = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Ошибка подключения или запроса: " . $e->getMessage());
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Просмотр</title>
<style type="text/css">
<!--
body { font: 12px Times; color: #666666; }
h3 { font-size: 16px; text-align: center; }
table { width: 1010px; border-collapse: collapse; margin: 0px auto; background: #FFFFFF; }
td { padding: 3px; text-align: center; vertical-align: middle; }
.buttons { width: auto; border: double 1px #666666; background: #D6D6D6; }
.filter-form { text-align: center; margin: 15px auto; }
.filter-form input[type="text"] { padding: 4px; width: 200px; }
.filter-form input[type="submit"], .filter-form a { padding: 4px 8px; margin-left: 5px; text-decoration: none; background: #D6D6D6; border: 1px solid #666; color: #000; }
-->
</style>
</head>
<body>
<h3>Просмотр результатов тестирования</h3>

<!-- Форма фильтрации по фамилии -->
<div class="filter-form">
    <form method="get" action="show.php" style="display: inline-block;">
        <label for="fio">Фамилия:</label>
      <input type="text" id="fio" name="fio"id="fio" value="<?php echo htmlspecialchars($fioFilter); ?>" list="lift" autocomplete="off" placeholder="наведите курсор на поле и выберите в списке свою фамилию" required>
      <datalist id="lift">
        <option>Егор</option>
        <option>Ксюша</option>
        <option>Анюта</option>
        <option>Даня</option>
      </datalist>
        
        <input type="submit" value="Найти">
        <?php if ($fioFilter !== ''): ?>
            <a href="show.php">Сбросить</a>
        <?php endif; ?>
    </form>
</div>

<table border="1" cellpadding="0" cellspacing="0">
<tr style="border: solid 1px #000">
<td align="center" width="10%"><b>п/п</b></td>
<td align="center"><b>Исполнитель</b></td>
<td align="center"><b>дата</b></td>
<td align="center"><b>время</b></td>
<td align="center"><b>предмет</b></td>
<td align="center"><b>тест</b></td>
<td align="center"><b>оценка</b></td>
</tr>

<?php
$num = 0; // счетчик строк
foreach ($rows as $row) {
    $num++;
    echo "<tr>\n";
    echo "<td>" . $num . "</td>\n";
    echo "<td>" . htmlspecialchars($row['fio']) . "</td>\n";
    echo "<td>" . htmlspecialchars($row['tdate']) . "</td>\n";
    echo "<td>" . htmlspecialchars($row['time']) . "</td>\n";
    echo "<td>" . htmlspecialchars($row['predmet']) . "</td>\n";
    echo "<td>" . htmlspecialchars($row['nazv']) . "</td>\n";
    echo "<td>" . htmlspecialchars($row['grade']) . "</td>\n";
    echo "</tr>\n";
}
// Если записей нет, выводим сообщение
if ($num === 0) {
    echo "<tr><td colspan='7' style='text-align:center;'>Нет записей, соответствующих фильтру</td></tr>";
}
?>
</table>

<div style="text-align: center; margin-top: 10px;">
<a href="matematik30.html">Ввод данных</a>&nbsp;
<a href="selShow.php">Просмотр</a>
</div>

</body>
</html>