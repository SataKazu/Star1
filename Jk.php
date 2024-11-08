<?php
// Подключаем файл с настройками базы данных
include 'D:\1\OSPanel\domains\bata\php\database\db.php';

// Если запрос пришел на добавление новой записи
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'insert') {
    $Name_JK = $_POST['Name_JK'];
    $stmt = $connection->prepare("INSERT INTO jk (Name_JK) VALUES (?)");
    $stmt->bind_param("s", $Name_JK);
    $stmt->execute();
    echo json_encode(["status" => "success", "message" => "Запись добавлена"]);
    $stmt->close();
    exit;
}

// Если запрос пришел на обновление записи
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id_JK = intval($_POST['id_JK']);
    $Name_JK = $_POST['Name_JK'];
    $stmt = $connection->prepare("UPDATE jk SET Name_JK = ? WHERE id_JK = ?");
    $stmt->bind_param("si", $Name_JK, $id_JK);
    $stmt->execute();
    echo json_encode(["status" => "success", "message" => "Запись обновлена"]);
    $stmt->close();
    exit;
}

// Если запрос пришел на удаление записи
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id_JK = intval($_POST['id_JK']);
    $stmt = $connection->prepare("DELETE FROM jk WHERE id_JK = ?");
    $stmt->bind_param("i", $id_JK);
    $stmt->execute();
    echo json_encode(["status" => "success", "message" => "Запись удалена"]);
    $stmt->close();
    exit;
}

// Получение данных ЖК
$sql = "SELECT id_JK, Name_JK FROM jk";
$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Справочник "ЖК"</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table td, .table th { font-size: 12px; padding: 5px; word-wrap: break-word; }
        .table { table-layout: fixed; width: 100%; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h4 class="text-center">Справочник - Жилищные комплексы</h4>
        <table class="table table-striped" id="jkTable">
            <thead>
                <tr>
                    <th scope="col">id_JK</th>
                    <th scope="col">Name_JK</th>
                    <th scope="col"></th>
                    <th scope="col">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#InsertModal">Добавить</button>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) { ?>
                        <tr id="row-<?php echo $row['id_JK']; ?>">
                            <td><?php echo htmlspecialchars($row["id_JK"]); ?></td>
                            <td><?php echo htmlspecialchars($row["Name_JK"]); ?></td>
                            <td>
                                <button class="btn btn-danger btn-sm" onclick="deleteRecord(<?php echo $row['id_JK']; ?>)">Удалить</button>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#UpdateModal" onclick="fillUpdateForm(<?php echo $row['id_JK']; ?>, '<?php echo htmlspecialchars($row['Name_JK']); ?>')">Обновить</button>
                            </td>
                        </tr>
                <?php }
                } else {
                    echo "<tr><td colspan='3' class='text-center'>0 результатов</td></tr>";
                } ?>
            </tbody>
        </table>
    </div>

    <!-- Модальное окно "Добавление" -->
    <div class="modal fade" id="InsertModal" tabindex="-1" aria-labelledby="InsertModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="insertForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="InsertModalLabel">Добавить ЖК</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" name="Name_JK" class="form-control" id="Name_JK" required>
                            <label for="Name_JK">Name_JK</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-success">Добавить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальное окно "Обновление" -->
    <div class="modal fade" id="UpdateModal" tabindex="-1" aria-labelledby="UpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="updateForm">
                    <input type="hidden" id="update_id_JK" name="id_JK">
                    <div class="modal-header">
                        <h5 class="modal-title" id="UpdateModalLabel">Обновить ЖК</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input type="text" id="update_Name_JK" name="Name_JK" class="form-control" required>
                            <label for="update_Name_JK">Name_JK</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-success">Обновить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Добавление новой записи
        document.getElementById("insertForm").addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append("action", "insert");
            fetch("", { method: "POST", body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") location.reload(); // обновление страницы
                });
        });

        // Обновление записи
        document.getElementById("updateForm").addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append("action", "update");
            fetch("", { method: "POST", body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") location.reload();
                });
        });

        // Удаление записи
        function deleteRecord(id_JK) {
            const formData = new FormData();
            formData.append("action", "delete");
            formData.append("id_JK", id_JK);
            fetch("", { method: "POST", body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") document.getElementById("row-" + id_JK).remove();
                });
        }

        // Заполнение формы обновления
        function fillUpdateForm(id_JK, Name_JK) {
            document.getElementById("update_id_JK").value = id_JK;
            document.getElementById("update_Name_JK").value = Name_JK;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
