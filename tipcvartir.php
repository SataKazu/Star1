<?php
// Подключаем файл с настройками базы данных
include 'D:\1\OSPanel\domains\bata\php\database\db.php';

// Обработка запросов AJAX для удаления, добавления и обновления
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    if ($action === 'delete') {
        $id_TipCvartir = intval($_POST['id_TipCvartir']);
        $stmt = $connection->prepare("DELETE FROM tipcvartir WHERE id_TipCvartir = ?");
        $stmt->bind_param("i", $id_TipCvartir);
        echo $stmt->execute() ? "success" : "error";
        $stmt->close();
    } elseif ($action === 'insert') {
        $NameTipCv = $_POST['NameTipCv'];
        $stmt = $connection->prepare("INSERT INTO tipcvartir (NameTipCv) VALUES (?)");
        $stmt->bind_param("s", $NameTipCv);
        echo $stmt->execute() ? "success" : "error";
        $stmt->close();
    } elseif ($action === 'update') {
        $id_TipCvartir = intval($_POST['id_TipCvartir']);
        $NameTipCv = $_POST['NameTipCv'];
        $stmt = $connection->prepare("UPDATE tipcvartir SET NameTipCv = ? WHERE id_TipCvartir = ?");
        $stmt->bind_param("si", $NameTipCv, $id_TipCvartir);
        echo $stmt->execute() ? "success" : "error";
        $stmt->close();
    }
    exit();
}

// Получение данных из таблицы tipcvartir
$sql = "SELECT id_TipCvartir, NameTipCv FROM tipcvartir";
$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Справочник "Типы Квартир"</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        .table td, .table th { font-size: 12px; padding: 5px; word-wrap: break-word; }
        .table { table-layout: fixed; width: 100%; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h4 class="text-center">Справочник - Типы Квартир</h4>
        <table class="table table-striped" id="data-table">
            <thead>
                <tr>
                    <th scope="col">ID Типа Квартиры</th>
                    <th scope="col">Название Типа</th>
                    <th scope="col" class="text-center">Действия</th>
                    <th scope="col">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#InsertModal">Добавить</button>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr id="row-<?= htmlspecialchars($row['id_TipCvartir']) ?>">
                            <th scope='row'><?= htmlspecialchars($row["id_TipCvartir"]) ?></th>
                            <td><?= htmlspecialchars($row["NameTipCv"]) ?></td>
                            <td class='text-center'>
                                <button class='btn btn-danger btn-sm' onclick='deleteRecord(<?= $row["id_TipCvartir"] ?>)'>Удалить</button>
                                <button class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#UpdateModal' onclick='fillUpdateForm(<?= $row["id_TipCvartir"] ?>, "<?= htmlspecialchars($row["NameTipCv"]) ?>")'>Обновить</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan='4' class='text-center'>Нет результатов</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Модальные окна для добавления и обновления -->
    <div class="modal fade" id="InsertModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog"><div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Добавить Тип Квартиры</h5></div>
            <div class="modal-body">
                <input type="text" id="insert_NameTipCv" class="form-control" placeholder="Название Типа" required>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button class="btn btn-success" onclick="addRecord()">Добавить</button>
            </div>
        </div></div>
    </div>

    <div class="modal fade" id="UpdateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog"><div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Обновить Тип Квартиры</h5></div>
            <div class="modal-body">
                <input type="hidden" id="update_id_TipCvartir">
                <input type="text" id="update_NameTipCv" class="form-control" required>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button class="btn btn-success" onclick="updateRecord()">Обновить</button>
            </div>
        </div></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        // Функция добавления
        function addRecord() {
            let NameTipCv = $('#insert_NameTipCv').val();
            $.post('', {action: 'insert', NameTipCv: NameTipCv}, function(response) {
                if (response === 'success') location.reload();
                else alert('Ошибка при добавлении.');
            });
        }

        // Функция удаления
        function deleteRecord(id_TipCvartir) {
            $.post('', {action: 'delete', id_TipCvartir: id_TipCvartir}, function(response) {
                if (response === 'success') $('#row-' + id_TipCvartir).remove();
                else alert('Ошибка при удалении.');
            });
        }

        // Функция обновления
        function fillUpdateForm(id_TipCvartir, NameTipCv) {
            $('#update_id_TipCvartir').val(id_TipCvartir);
            $('#update_NameTipCv').val(NameTipCv);
        }
        
        function updateRecord() {
            let id_TipCvartir = $('#update_id_TipCvartir').val();
            let NameTipCv = $('#update_NameTipCv').val();
            $.post('', {action: 'update', id_TipCvartir: id_TipCvartir, NameTipCv: NameTipCv}, function(response) {
                if (response === 'success') location.reload();
                else alert('Ошибка при обновлении.');
            });
        }
    </script>

    <?php $connection->close(); ?>
</body>
</html>
