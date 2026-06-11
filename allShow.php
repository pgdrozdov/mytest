<?php
// show.php
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

    // Выполняем запрос
    $stmt = $pdo->query("SELECT * FROM arh ORDER BY fio, tdate, time, predmet, nazv, grade ASC");
    $rows = $stmt->fetchAll(); // можно и в цикле, но так удобнее для подсчета

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
-->
</style>
</head>
<body>
<h3>Просмотр результатов тестирования</h3>
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
    // При необходимости можно добавить ссылку на редактирование
    // echo "<td><a href=\"update_avtor.php?link=" . $row['id_avtor'] . "\">Ввод</a></td>\n";
    echo "</tr>\n";
}
?>

</table>

<div style="text-align: center; margin-top: 10px;">
<a href="indextime.html">Ввод данных</a>&nbsp;
<a href="show.php">Просмотр</a>
</div>

</body>
</html>
