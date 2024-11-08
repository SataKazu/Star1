<?php
// Подключаем файл с настройками базы данных
include 'D:\1\OSPanel\domains\bata\php\database\db.php';

// SQL-запрос для выборки данных из таблицы rabotnic
$sql = "SELECT id_Rab, FioRab, DataRab, PasportSeriaandNumborRab, TelephoneRab, MestoRodRab, AdresPropeskiRab, CemVidonRab, dataVidaciPasparta FROM rabotnic";

// Выполняем запрос
$result = mysqli_query($connection, $sql);

// Проверяем, успешен ли запрос
if (!$result) {
    die("Ошибка выполнения запроса: " . mysqli_error($connection));
}

// Обработка добавления, редактирования и удаления записей
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Добавление нового сотрудника
    if (isset($_POST['add'])) {
        $fio = $_POST['fio'];
        $dataRab = $_POST['dataRab'];
        $pasport = $_POST['pasport'];
        $mestoRod = $_POST['mestoRod'];
        $adres = $_POST['adres'];
        $telefon = $_POST['telefon'];
        $cemVidon = $_POST['cemVidon'];
        $dataVidaci = $_POST['dataVidaci'];

        $stmt = $connection->prepare("
            INSERT INTO rabotnic 
            (FioRab, DataRab, PasportSeriaandNumborRab, MestoRodRab, AdresPropeskiRab, TelephoneRab, CemVidonRab, dataVidaciPasparta) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('ssssssss', $fio, $dataRab, $pasport, $mestoRod, $adres, $telefon, $cemVidon, $dataVidaci);
        $stmt->execute();
        $stmt->close();
    }

    // Обновление сотрудника
    if (isset($_POST['update'])) {
        $id_Rab = $_POST['id_Rab'];
        $fio = $_POST['fio'];
        $dataRab = $_POST['dataRab'];
        $pasport = $_POST['pasport'];
        $mestoRod = $_POST['mestoRod'];
        $adres = $_POST['adres'];
        $telefon = $_POST['telefon'];
        $cemVidon = $_POST['cemVidon'];
        $dataVidaci = $_POST['dataVidaci'];

        $stmt = $connection->prepare("
            UPDATE rabotnic 
            SET FioRab = ?, DataRab = ?, PasportSeriaandNumborRab = ?, MestoRodRab = ?, AdresPropeskiRab = ?, TelephoneRab = ?, CemVidonRab = ?, dataVidaciPasparta = ? 
            WHERE id_Rab = ?
        ");
        $stmt->bind_param('ssssssssi', $fio, $dataRab, $pasport, $mestoRod, $adres, $telefon, $cemVidon, $dataVidaci, $id_Rab);
        $stmt->execute();
        $stmt->close();
    }

    // Удаление сотрудника
    if (isset($_POST['delete'])) {
        $id_Rab = $_POST['id_Rab'];

        $stmt = $connection->prepare("DELETE FROM rabotnic WHERE id_Rab = ?");
        $stmt->bind_param('i', $id_Rab);
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
    <title>Таблица сотрудников</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h3>Таблица сотрудников</h3>

    <!-- Форма для добавления нового сотрудника -->
    <form method="POST" action="" class="mb-4">
        <h4>Добавить нового сотрудника</h4>
        <!-- Поля формы -->
        <div class="form-group">
            <label for="fio">ФИО</label>
            <input type="text" class="form-control" name="fio" required>
        </div>
        <div class="form-group">
            <label for="dataRab">Дата рождения</label>
            <input type="date" class="form-control" name="dataRab" required>
        </div>
        <div class="form-group">
            <label for="pasport">Паспорт (серия и номер)</label>
            <input type="text" class="form-control" name="pasport" required>
        </div>
        <div class="form-group">
            <label for="mestoRod">Место рождения</label>
            <input type="text" class="form-control" name="mestoRod" required>
        </div>
        <div class="form-group">
            <label for="adres">Адрес прописки</label>
            <input type="text" class="form-control" name="adres" required>
        </div>
        <div class="form-group">
            <label for="telefon">Телефон</label>
            <input type="text" class="form-control" name="telefon" required>
        </div>
        <div class="form-group">
            <label for="cemVidon">Кем выдан</label>
            <input type="text" class="form-control" name="cemVidon" required>
        </div>
        <div class="form-group">
            <label for="dataVidaci">Дата выдачи паспорта</label>
            <input type="date" class="form-control" name="dataVidaci" required>
        </div>
        <button type="submit" name="add" class="btn btn-primary">Добавить</button>
    </form>

    <!-- Таблица сотрудников -->
    <table class="table">
        <thead class="thead-dark">
            <tr>
                <th>ID Сотрудника</th>
                <th>ФИО</th>
                <th>Дата рождения</th>
                <th>Паспорт</th>
                <th>Место рождения</th>
                <th>Адрес прописки</th>
                <th>Телефон</th>
                <th>Кем выдан</th>
                <th>Дата выдачи паспорта</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    echo '<th scope="row">' . htmlspecialchars($row["id_Rab"]) . '</th>';
                    echo '<td>' . htmlspecialchars($row["FioRab"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["DataRab"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["PasportSeriaandNumborRab"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["MestoRodRab"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["AdresPropeskiRab"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["TelephoneRab"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["CemVidonRab"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["dataVidaciPasparta"]) . '</td>';
                    echo '<td>';
                    echo '<button class="btn btn-warning" data-toggle="modal" data-target="#updateModal" data-id="' . $row['id_Rab'] . '" data-fio="' . $row['FioRab'] . '" data-datarab="' . $row['DataRab'] . '" data-pasport="' . $row['PasportSeriaandNumborRab'] . '" data-mestorod="' . $row['MestoRodRab'] . '" data-adres="' . $row['AdresPropeskiRab'] . '" data-telefon="' . $row['TelephoneRab'] . '" data-cemvidon="' . $row['CemVidonRab'] . '" data-datavidaci="' . $row['dataVidaciPasparta'] . '">Изменить</button>';
                    echo '<form method="POST" style="display:inline-block;">';
                    echo '<input type="hidden" name="id_Rab" value="' . $row["id_Rab"] . '">';
                    echo '<button type="submit" name="delete" class="btn btn-danger">Удалить</button>';
                    echo '</form>';
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="10">Нет данных</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Модальное окно для редактирования сотрудника -->
<!-- Здесь вы можете добавить код для отображения модального окна, которое будет заполнено текущими данными сотрудника для редактирования -->
</body>
</html>
