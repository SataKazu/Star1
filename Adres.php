<?php
// Подключаем файл с настройками базы данных
include 'D:\1\OSPanel\domains\bata\php\database\db.php'; // Убедитесь, что путь правильный

// SQL-запрос для выборки данных из таблицы адреса с использованием JOIN
$sql = "
    SELECT 
        adres.id_Adres, 
        adres.Adres, 
        jk.Name_JK, 
        rion.NameRion,
        jk.id_JK, 
        rion.id_Rion
    FROM 
        adres 
    JOIN 
        jk ON adres.id_JK = jk.id_JK 
    JOIN 
        rion ON adres.id_Rion = rion.id_Rion
";

// Выполняем запрос
$result = mysqli_query($connection, $sql);

// Проверяем, успешен ли запрос
if (!$result) {
    die("Ошибка выполнения запроса: " . mysqli_error($connection));
}

// Добавление, удаление и обновление данных с использованием подготовленных запросов
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Добавление нового адреса
    if (isset($_POST['add'])) {
        $adres = $_POST['adres'];
        $jk = $_POST['jk'];
        $rion = $_POST['rion'];
        
        $stmt = $connection->prepare("INSERT INTO adres (Adres, id_JK, id_Rion) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $adres, $jk, $rion);
        $stmt->execute();
        $stmt->close();
    }

    // Обновление данных адреса
    if (isset($_POST['update'])) {
        $id_Adres = $_POST['id_Adres'];
        $adres = $_POST['adres'];
        $jk = $_POST['jk'];
        $rion = $_POST['rion'];
        
        $stmt = $connection->prepare("UPDATE adres SET Adres = ?, id_JK = ?, id_Rion = ? WHERE id_Adres = ?");
        $stmt->bind_param("siii", $adres, $jk, $rion, $id_Adres);
        $stmt->execute();
        $stmt->close();
    }

    // Удаление записи
    if (isset($_POST['delete'])) {
        $id_Adres = $_POST['id_Adres'];
        
        $stmt = $connection->prepare("DELETE FROM adres WHERE id_Adres = ?");
        $stmt->bind_param("i", $id_Adres);
        $stmt->execute();
        $stmt->close();
    }
}

// Запрос для получения всех Жилых комплексов и Районов для комбобоксов
$jk_query = "SELECT id_JK, Name_JK FROM jk";
$jk_result = mysqli_query($connection, $jk_query);

$rion_query = "SELECT id_Rion, NameRion FROM rion";
$rion_result = mysqli_query($connection, $rion_query);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Таблица адресов</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h3>Таблица адресов</h3>

    <!-- Форма для добавления нового адреса -->
    <form method="POST" action="" class="mb-4">
        <h4>Добавить новый адрес</h4>
        <div class="form-group">
            <label for="adres">Адрес</label>
            <input type="text" class="form-control" name="adres" required>
        </div>
        <div class="form-group">
            <label for="jk">Жилой Комплекс</label>
            <select class="form-control" name="jk" required>
                <option value="">Выберите жилой комплекс</option>
                <?php while ($row = mysqli_fetch_assoc($jk_result)) { ?>
                    <option value="<?php echo $row['id_JK']; ?>"><?php echo htmlspecialchars($row['Name_JK']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="rion">Район</label>
            <select class="form-control" name="rion" required>
                <option value="">Выберите район</option>
                <?php while ($row = mysqli_fetch_assoc($rion_result)) { ?>
                    <option value="<?php echo $row['id_Rion']; ?>"><?php echo htmlspecialchars($row['NameRion']); ?></option>
                <?php } ?>
            </select>
        </div>
        <button type="submit" name="add" class="btn btn-primary">Добавить</button>
    </form>

    <!-- Таблица с адресами -->
    <table class="table">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Id_Adres</th>
                <th scope="col">Адрес</th>
                <th scope="col">Жилой Комплекс</th>
                <th scope="col">Район</th>
                <th scope="col">Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                // Выводим данные построчно
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    echo '<th scope="row">' . htmlspecialchars($row["id_Adres"]) . '</th>';
                    echo '<td>' . htmlspecialchars($row["Adres"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["Name_JK"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["NameRion"]) . '</td>';
                    echo '<td>';
                    echo '<button class="btn btn-warning" data-toggle="modal" data-target="#updateModal" data-id="' . $row['id_Adres'] . '" data-adres="' . htmlspecialchars($row['Adres']) . '" data-jk="' . $row['id_JK'] . '" data-rion="' . $row['id_Rion'] . '">Изменить</button>';
                    echo ' <form method="POST" style="display:inline;" action="">
                              <input type="hidden" name="id_Adres" value="' . $row['id_Adres'] . '">
                              <button type="submit" name="delete" class="btn btn-danger">Удалить</button>
                          </form>';
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="5" class="text-center">0 результатов</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Модальное окно для обновления данных -->
<div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Обновить адрес</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="">
        <div class="modal-body">
          <input type="hidden" name="id_Adres" id="update-id_Adres">
          <div class="form-group">
            <label for="update-adres">Адрес</label>
            <input type="text" class="form-control" name="adres" id="update-adres" required>
          </div>
          <div class="form-group">
            <label for="update-jk">Жилой Комплекс</label>
            <select class="form-control" name="jk" id="update-jk" required>
                <?php
                // Повторный запрос для заполнения списка Жилых комплексов
                $jk_result = mysqli_query($connection, $jk_query);
                while ($row = mysqli_fetch_assoc($jk_result)) { ?>
                    <option value="<?php echo $row['id_JK']; ?>"><?php echo htmlspecialchars($row['Name_JK']); ?></option>
                <?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label for="update-rion">Район</label>
            <select class="form-control" name="rion" id="update-rion" required>
                <?php
                // Повторный запрос для заполнения списка Районов
                $rion_result = mysqli_query($connection, $rion_query);
                while ($row = mysqli_fetch_assoc($rion_result)) { ?>
                    <option value="<?php echo $row['id_Rion']; ?>"><?php echo htmlspecialchars($row['NameRion']); ?></option>
                <?php } ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
          <button type="submit" name="update" class="btn btn-primary">Сохранить изменения</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    // Заполняем модальное окно для обновления данных
    $('#updateModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Кнопка, которая открывает модальное окно
        var id_Adres = button.data('id');
        var adres = button.data('adres');
        var jk = button.data('jk');
        var rion = button.data('rion');
        
        // Заполняем поля в модальном окне
        var modal = $(this);
        modal.find('#update-id_Adres').val(id_Adres);
        modal.find('#update-adres').val(adres);
        modal.find('#update-jk').val(jk);
        modal.find('#update-rion').val(rion);
    });
</script>

<?php
// Закрываем соединение
mysqli_close($connection);
?>

</body>
</html>
