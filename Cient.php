<?php
// Подключаем файл с настройками базы данных
include 'D:\1\OSPanel\domains\bata\php\database\db.php'; // Убедитесь, что путь правильный

// SQL-запрос для выборки данных из таблицы client
$sql = "
    SELECT 
        client_id, 
        last_name, 
        first_name, 
        middle_name, 
        address, 
        city, 
        passport_series, 
        passport_number, 
        subdivision_code, 
        passport_issue_date, 
        date_of_birth 
    FROM 
        client
";

// Выполняем запрос
$result = mysqli_query($connection, $sql);

// Проверяем, успешен ли запрос
if (!$result) {
    die("Ошибка выполнения запроса: " . mysqli_error($connection));
}

// Обработка добавления, редактирования и удаления записей
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Добавление нового клиента
    if (isset($_POST['add'])) {
        $last_name = $_POST['last_name'];
        $first_name = $_POST['first_name'];
        $middle_name = $_POST['middle_name'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $passport_series = $_POST['passport_series'];
        $passport_number = $_POST['passport_number'];
        $subdivision_code = $_POST['subdivision_code'];
        $passport_issue_date = $_POST['passport_issue_date'];
        $date_of_birth = $_POST['date_of_birth'];

        $stmt = $connection->prepare("
            INSERT INTO client 
            (last_name, first_name, middle_name, address, city, passport_series, passport_number, subdivision_code, passport_issue_date, date_of_birth) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('ssssssssss', $last_name, $first_name, $middle_name, $address, $city, $passport_series, $passport_number, $subdivision_code, $passport_issue_date, $date_of_birth);
        $stmt->execute();
        $stmt->close();
    }

    // Обновление клиента
    if (isset($_POST['update'])) {
        $client_id = $_POST['client_id'];
        $last_name = $_POST['last_name'];
        $first_name = $_POST['first_name'];
        $middle_name = $_POST['middle_name'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $passport_series = $_POST['passport_series'];
        $passport_number = $_POST['passport_number'];
        $subdivision_code = $_POST['subdivision_code'];
        $passport_issue_date = $_POST['passport_issue_date'];
        $date_of_birth = $_POST['date_of_birth'];

        $stmt = $connection->prepare("
            UPDATE client 
            SET last_name = ?, first_name = ?, middle_name = ?, address = ?, city = ?, passport_series = ?, passport_number = ?, subdivision_code = ?, passport_issue_date = ?, date_of_birth = ? 
            WHERE client_id = ?
        ");
        $stmt->bind_param('ssssssssssi', $last_name, $first_name, $middle_name, $address, $city, $passport_series, $passport_number, $subdivision_code, $passport_issue_date, $date_of_birth, $client_id);
        $stmt->execute();
        $stmt->close();
    }

    // Удаление клиента
    if (isset($_POST['delete'])) {
        $client_id = $_POST['client_id'];

        $stmt = $connection->prepare("DELETE FROM client WHERE client_id = ?");
        $stmt->bind_param('i', $client_id);
        $stmt->execute();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Таблица клиентов</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h3>Таблица клиентов</h3>

    <!-- Форма для добавления нового клиента -->
    <form method="POST" action="" class="mb-4">
        <h4>Добавить нового клиента</h4>
        <div class="form-group">
            <label for="last_name">Фамилия</label>
            <input type="text" class="form-control" name="last_name" required>
        </div>
        <div class="form-group">
            <label for="first_name">Имя</label>
            <input type="text" class="form-control" name="first_name" required>
        </div>
        <div class="form-group">
            <label for="middle_name">Отчество</label>
            <input type="text" class="form-control" name="middle_name">
        </div>
        <div class="form-group">
            <label for="address">Адрес</label>
            <input type="text" class="form-control" name="address" required>
        </div>
        <div class="form-group">
            <label for="city">Город</label>
            <input type="text" class="form-control" name="city" required>
        </div>
        <div class="form-group">
            <label for="passport_series">Серия паспорта</label>
            <input type="text" class="form-control" name="passport_series" required>
        </div>
        <div class="form-group">
            <label for="passport_number">Номер паспорта</label>
            <input type="text" class="form-control" name="passport_number" required>
        </div>
        <div class="form-group">
            <label for="subdivision_code">Код подразделения</label>
            <input type="text" class="form-control" name="subdivision_code" required>
        </div>
        <div class="form-group">
            <label for="passport_issue_date">Дата выдачи паспорта</label>
            <input type="date" class="form-control" name="passport_issue_date" required>
        </div>
        <div class="form-group">
            <label for="date_of_birth">Дата рождения</label>
            <input type="date" class="form-control" name="date_of_birth">
        </div>
        <button type="submit" name="add" class="btn btn-primary">Добавить</button>
    </form>

    <!-- Таблица с клиентами -->
    <table class="table">
        <thead class="thead-dark">
            <tr>
                <th>ID Клиента</th>
                <th>Фамилия</th>
                <th>Имя</th>
                <th>Отчество</th>
                <th>Адрес</th>
                <th>Город</th>
                <th>Серия паспорта</th>
                <th>Номер паспорта</th>
                <th>Код подразделения</th>
                <th>Дата выдачи паспорта</th>
                <th>Дата рождения</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    echo '<th scope="row">' . htmlspecialchars($row["client_id"]) . '</th>';
                    echo '<td>' . htmlspecialchars($row["last_name"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["first_name"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["middle_name"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["address"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["city"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["passport_series"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["passport_number"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["subdivision_code"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["passport_issue_date"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["date_of_birth"]) . '</td>';
                    echo '<td>';
                    echo '<button class="btn btn-warning" data-toggle="modal" data-target="#updateModal" data-id="' . $row['client_id'] . '" data-lastname="' . $row['last_name'] . '" data-firstname="' . $row['first_name'] . '" data-middlename="' . $row['middle_name'] . '" data-address="' . $row['address'] . '" data-city="' . $row['city'] . '" data-passportseries="' . $row['passport_series'] . '" data-passportnumber="' . $row['passport_number'] . '" data-subdivisioncode="' . $row['subdivision_code'] . '" data-passportissuedate="' . $row['passport_issue_date'] . '" data-dob="' . $row['date_of_birth'] . '">Изменить</button>';
                    echo ' <form method="POST" style="display:inline;" action="">
                              <input type="hidden" name="client_id" value="' . $row["client_id"] . '">
                              <button type="submit" name="delete" class="btn btn-danger">Удалить</button>
                          </form>';
                    echo '</td>';
                    echo '</tr>';
                }
            }
            ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
