<?php
// Подключаем файл с настройками базы данных
include 'D:\1\OSPanel\domains\bata\php\database\db.php'; // Убедитесь, что путь правильный

// SQL-запрос для выборки данных из таблицы dogovor с использованием JOIN
$sql = "
   SELECT dogovor.idDogovor, 
          rabotnic.FioRab AS FioRabotnic, 
          rabotnic.DataRab AS DataRabotnic, 
          client.FioCl AS FioClient, 
          dogovor.NameDogovor, 
          dogovor.ItogovoyCenal, 
          apportionment.PloshchadAp AS Ploshchad, 
          apportionment.floor, 
          apportionment.NomerGosRigistAp 
    FROM dogovor 
    JOIN rabotnic ON dogovor.Id_Rabotnic = rabotnic.id_Rab 
    JOIN client ON dogovor.Id_Client = client.id_Client 
    JOIN apportionment ON dogovor.Id_Appor = apportionment.id_Ap
";

// Выполняем запрос
$result = mysqli_query($connection, $sql);

// Проверяем, успешен ли запрос
if (!$result) {
    die("Ошибка выполнения запроса: " . mysqli_error($connection));
}

// Запрашиваем данные для комбобоксов
$rabotnic_result = mysqli_query($connection, "SELECT id_Rab, FioRab FROM rabotnic");
$client_result = mysqli_query($connection, "SELECT id_Client, FioCl FROM client");
$apportionment_result = mysqli_query($connection, "
    SELECT id_Ap, PloshchadAp, floor, NomerGosRigistAp 
    FROM apportionment
");

// Обработка добавления, редактирования и удаления записей
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Добавление нового договора
    if (isset($_POST['add_record'])) {
        $Id_Rabotnic = $_POST['Id_Rabotnic'];
        $Id_Client = $_POST['Id_Client'];
        $Id_Appor = $_POST['Id_Appor'];
        $NameDogovor = $_POST['NameDogovor'];
        $ItogovoyCenal = $_POST['ItogovoyCenal'];

        $stmt = $connection->prepare("
            INSERT INTO dogovor (NameDogovor, ItogovoyCenal, Id_Rabotnic, Id_Client, Id_Appor)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('ssiii', $NameDogovor, $ItogovoyCenal, $Id_Rabotnic, $Id_Client, $Id_Appor);
        $stmt->execute();
        $stmt->close();
    }

    // Обновление договора
    if (isset($_POST['update_record'])) {
        $idDogovor = $_POST['idDogovor'];
        $NameDogovor = $_POST['NameDogovor'];
        $ItogovoyCenal = $_POST['ItogovoyCenal'];

        $stmt = $connection->prepare("
            UPDATE dogovor 
            SET NameDogovor = ?, ItogovoyCenal = ? 
            WHERE idDogovor = ?
        ");
        $stmt->bind_param('ssi', $NameDogovor, $ItogovoyCenal, $idDogovor);
        $stmt->execute();
        $stmt->close();
    }

    // Удаление договора
    if (isset($_POST['delete_record'])) {
        $idDogovor = $_POST['idDogovor'];

        $stmt = $connection->prepare("DELETE FROM dogovor WHERE idDogovor = ?");
        $stmt->bind_param('i', $idDogovor);
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
    <title>Управление договорами</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h3>Таблица договоров</h3>

    <!-- Форма для добавления нового договора -->
    <form method="POST" action="" class="mb-4">
        <h4>Добавить новый договор</h4>
        <div class="form-group">
            <label for="Id_Rabotnic">Выберите работника</label>
            <select class="form-control" name="Id_Rabotnic" required>
                <option value="">Выберите работника</option>
                <?php
                while ($rabotnic = mysqli_fetch_assoc($rabotnic_result)) {
                    echo '<option value="' . $rabotnic['id_Rab'] . '">' . htmlspecialchars($rabotnic['FioRab']) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="Id_Client">Выберите клиента</label>
            <select class="form-control" name="Id_Client" required>
                <option value="">Выберите клиента</option>
                <?php
                while ($client = mysqli_fetch_assoc($client_result)) {
                    echo '<option value="' . $client['id_Client'] . '">' . htmlspecialchars($client['FioCl']) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="Id_Appor">Выберите апартаменты</label>
            <select class="form-control" name="Id_Appor" required>
                <option value="">Выберите апартаменты</option>
                <?php
                while ($apportionment = mysqli_fetch_assoc($apportionment_result)) {
                    echo '<option value="' . $apportionment['id_Ap'] . '">Площадь: ' . htmlspecialchars($apportionment['PloshchadAp']) . ', Этаж: ' . htmlspecialchars($apportionment['floor']) . ', Рег. номер: ' . htmlspecialchars($apportionment['NomerGosRigistAp']) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="NameDogovor">Название договора</label>
            <input type="text" class="form-control" name="NameDogovor" required>
        </div>
        <div class="form-group">
            <label for="ItogovoyCenal">Итоговая цена</label>
            <input type="number" class="form-control" name="ItogovoyCenal" step="0.01" required>
        </div>
        <button type="submit" name="add_record" class="btn btn-primary">Добавить договор</button>
    </form>

    <!-- Таблица с договорами -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Работник</th>
                <th>Клиент</th>
                <th>Название договора</th>
                <th>Итоговая цена</th>
                <th>Апартаменты (Площадь, Этаж, Рег. номер)</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Отображение данных из таблицы договоов
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . $row['idDogovor'] . '</td>';
                echo '<td>' . htmlspecialchars($row['FioRabotnic']) . '</td>';
                echo '<td>' . htmlspecialchars($row['FioClient']) . '</td>';
                echo '<td>' . htmlspecialchars($row['NameDogovor']) . '</td>';
                echo '<td>' . htmlspecialchars($row['ItogovoyCenal']) . '</td>';
                echo '<td>Площадь: ' . htmlspecialchars($row['Ploshchad']) . ', Этаж: ' . htmlspecialchars($row['floor']) . ', Рег. номер: ' . htmlspecialchars($row['NomerGosRigistAp']) . '</td>';
                echo '<td>
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="idDogovor" value="' . $row['idDogovor'] . '">
                            <button type="submit" name="delete_record" class="btn btn-danger btn-sm">Удалить</button>
                        </form>
                        <button class="btn btn-warning btn-sm" onclick="openEditForm(' . $row['idDogovor'] . ', \'' . $row['NameDogovor'] . '\', \'' . $row['ItogovoyCenal'] . '\')">Изменить</button>
                      </td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>

    <!-- Форма редактирования договора (скрытая, будет показана при редактировании) -->
    <div id="editForm" style="display: none;">
        <h4>Редактировать договор</h4>
        <form method="POST" action="">
            <input type="hidden" name="idDogovor" id="edit_idDogovor">
            <div class="form-group">
                <label for="edit_NameDogovor">Название договора</label>
                <input type="text" class="form-control" name="NameDogovor" id="edit_NameDogovor" required>
            </div>
            <div class="form-group">
                <label for="edit_ItogovoyCenal">Итоговая цена</label>
                <input type="number" class="form-control" name="ItogovoyCenal" id="edit_ItogovoyCenal" step="0.01" required>
            </div>
            <button type="submit" name="update_record" class="btn btn-primary">Сохранить изменения</button>
            <button type="button" class="btn btn-secondary" onclick="closeEditForm()">Отмена</button>
        </form>
    </div>

</div>

<!-- Скрипт для управления формой редактирования -->
<script>
function openEditForm(idDogovor, NameDogovor, ItogovoyCenal) {
    document.getElementById('edit_idDogovor').value = idDogovor;
    document.getElementById('edit_NameDogovor').value = NameDogovor;
    document.getElementById('edit_ItogovoyCenal').value = ItogovoyCenal;
    document.getElementById('editForm').style.display = 'block';
}

function closeEditForm() {
    document.getElementById('editForm').style.display = 'none';
}
</script>

</body>
</html>

