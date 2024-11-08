<?php
// Подключаем файл с настройками базы данных
include 'D:\1\OSPanel\domains\bata\php\database\db.php'; // Убедитесь, что путь правильный

// SQL-запрос для выборки данных из таблицы apportionment с использованием JOIN
$sql = "
    SELECT 
        a.id_Ap, 
        adr.Adres, 
        tc.NameTipCv, 
        a.PloshchadAp, 
        a.NomerGosRigistAp, 
        a.floor 
    FROM 
        apportionment a 
    JOIN 
        adres adr ON a.AdresAp = adr.id_Adres 
    JOIN 
        tipcvartir tc ON a.TipCvartirAp = tc.id_TipCvartir
";

// Выполняем запрос
$result = mysqli_query($connection, $sql);

// Проверяем, успешен ли запрос
if (!$result) {
    die("Ошибка выполнения запроса: " . mysqli_error($connection));
}

// Добавление, обновление и удаление данных с использованием подготовленных запросов
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Добавление нового апартамента
    if (isset($_POST['add'])) {
        $adres = $_POST['adres'];
        $tipCv = $_POST['tipCv'];
        $ploshchadAp = $_POST['ploshchadAp'];
        $nomerGosReg = $_POST['nomerGosReg'];
        $floor = $_POST['floor'];
        
        $stmt = $connection->prepare("INSERT INTO apportionment (AdresAp, TipCvartirAp, PloshchadAp, NomerGosRigistAp, floor) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisii", $adres, $tipCv, $ploshchadAp, $nomerGosReg, $floor);
        $stmt->execute();
        $stmt->close();
    }

    // Обновление данных апартамента
    if (isset($_POST['update'])) {
        $id_Ap = $_POST['id_Ap'];
        $adres = $_POST['adres'];
        $tipCv = $_POST['tipCv'];
        $ploshchadAp = $_POST['ploshchadAp'];
        $nomerGosReg = $_POST['nomerGosReg'];
        $floor = $_POST['floor'];
        
        $stmt = $connection->prepare("UPDATE apportionment SET AdresAp = ?, TipCvartirAp = ?, PloshchadAp = ?, NomerGosRigistAp = ?, floor = ? WHERE id_Ap = ?");
        $stmt->bind_param("iisiii", $adres, $tipCv, $ploshchadAp, $nomerGosReg, $floor, $id_Ap);
        $stmt->execute();
        $stmt->close();
    }

    // Удаление записи
    if (isset($_POST['delete'])) {
        $id_Ap = $_POST['id_Ap'];
        
        $stmt = $connection->prepare("DELETE FROM apportionment WHERE id_Ap = ?");
        $stmt->bind_param("i", $id_Ap);
        $stmt->execute();
        $stmt->close();
    }
}

// Запрос для получения всех доступных Адресов и Типов квартир для комбобоксов
$adres_query = "SELECT id_Adres, Adres FROM adres";
$adres_result = mysqli_query($connection, $adres_query);

$tipcv_query = "SELECT id_TipCvartir, NameTipCv FROM tipcvartir";
$tipcv_result = mysqli_query($connection, $tipcv_query);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Таблица апартаментов</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h3>Таблица апартаментов</h3>

    <!-- Форма для добавления нового апартамента -->
    <form method="POST" action="" class="mb-4">
        <h4>Добавить новый апартамент</h4>
        <div class="form-group">
            <label for="adres">Адрес</label>
            <select class="form-control" name="adres" required>
                <option value="">Выберите адрес</option>
                <?php while ($row = mysqli_fetch_assoc($adres_result)) { ?>
                    <option value="<?php echo $row['id_Adres']; ?>"><?php echo htmlspecialchars($row['Adres']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="tipCv">Тип квартиры</label>
            <select class="form-control" name="tipCv" required>
                <option value="">Выберите тип квартиры</option>
                <?php while ($row = mysqli_fetch_assoc($tipcv_result)) { ?>
                    <option value="<?php echo $row['id_TipCvartir']; ?>"><?php echo htmlspecialchars($row['NameTipCv']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="ploshchadAp">Площадь апартамента</label>
            <input type="text" class="form-control" name="ploshchadAp" required>
        </div>
        <div class="form-group">
            <label for="nomerGosReg">Номер гос. регистрации</label>
            <input type="text" class="form-control" name="nomerGosReg" required>
        </div>
        <div class="form-group">
            <label for="floor">Этаж</label>
            <input type="number" class="form-control" name="floor" required>
        </div>
        <button type="submit" name="add" class="btn btn-primary">Добавить</button>
    </form>

    <!-- Таблица с апартаментами -->
    <table class="table">
        <thead class="thead-dark">
            <tr>
                <th scope="col">ID Апартамента</th>
                <th scope="col">Адрес</th>
                <th scope="col">Тип квартиры</th>
                <th scope="col">Площадь апартамента</th>
                <th scope="col">Номер гос. регистрации</th>
                <th scope="col">Этаж</th>
                <th scope="col">Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                // Выводим данные построчно
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    echo '<th scope="row">' . htmlspecialchars($row["id_Ap"]) . '</th>';
                    echo '<td>' . htmlspecialchars($row["Adres"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["NameTipCv"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["PloshchadAp"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["NomerGosRigistAp"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["floor"]) . '</td>';
                    echo '<td>';
                    echo '<button class="btn btn-warning" data-toggle="modal" data-target="#updateModal" data-id="' . $row['id_Ap'] . '" data-adres="' . $row['Adres'] . '" data-tipcv="' . $row['NameTipCv'] . '" data-ploshchad="' . $row['PloshchadAp'] . '" data-nomer="' . $row['NomerGosRigistAp'] . '" data-floor="' . $row['floor'] . '">Изменить</button>';
                    echo ' <form method="POST" style="display:inline;" action="">
                              <input type="hidden" name="id_Ap" value="' . $row['id_Ap'] . '">
                              <button type="submit" name="delete" class="btn btn-danger">Удалить</button>
                          </form>';
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="7" class="text-center">0 результатов</td></tr>';
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
        <h5 class="modal-title" id="exampleModalLabel">Обновить апартамент</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="">
        <div class="modal-body">
          <input type="hidden" id="update-id_Ap" name="id_Ap">
          <div class="form-group">
            <label for="adres">Адрес</label>
            <input type="text" class="form-control" id="update-adres" name="adres" required>
          </div>
          <div class="form-group">
            <label for="tipCv">Тип квартиры</label>
            <input type="text" class="form-control" id="update-tipCv" name="tipCv" required>
          </div>
          <div class="form-group">
            <label for="ploshchadAp">Площадь апартамента</label>
            <input type="text" class="form-control" id="update-ploshchadAp" name="ploshchadAp" required>
          </div>
          <div class="form-group">
            <label for="nomerGosReg">Номер гос. регистрации</label>
            <input type="text" class="form-control" id="update-nomerGosReg" name="nomerGosReg" required>
          </div>
          <div class="form-group">
            <label for="floor">Этаж</label>
            <input type="number" class="form-control" id="update-floor" name="floor" required>
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
        var id_Ap = button.data('id');
        var adres = button.data('adres');
        var tipCv = button.data('tipcv');
        var ploshchad = button.data('ploshchad');
        var nomer = button.data('nomer');
        var floor = button.data('floor');
        
        // Заполняем поля в модальном окне
        var modal = $(this);
        modal.find('#update-id_Ap').val(id_Ap);
        modal.find('#update-adres').val(adres);
        modal.find('#update-tipCv').val(tipCv);
        modal.find('#update-ploshchadAp').val(ploshchad);
        modal.find('#update-nomerGosReg').val(nomer);
        modal.find('#update-floor').val(floor);
    });
</script>

<?php
// Закрываем соединение
mysqli_close($connection);
?>

</body>
</html>
