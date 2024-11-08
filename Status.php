<?php
// Подключаем файл с настройками базы данных
include 'D:\1\OSPanel\domains\bata\php\database\db.php';

// Удаление записи
if (isset($_POST['delete_id'])) {
    $Id_Role = intval($_POST['delete_id']);
    $stmt = $connection->prepare("DELETE FROM role WHERE Id_Role = ?");
    $stmt->bind_param("i", $Id_Role);
    $response = $stmt->execute();
    echo json_encode(['success' => $response]);
    exit;
}

// Добавление записи
if (isset($_POST['add_Status'])) {
    $status = $_POST['add_Status'];
    $stmt = $connection->prepare("INSERT INTO role (Status) VALUES (?)");
    $stmt->bind_param("s", $status);
    $response = $stmt->execute();
    echo json_encode(['success' => $response, 'id' => $stmt->insert_id, 'status' => $status]);
    exit;
}

// Обновление записи
if (isset($_POST['update_id_Role']) && isset($_POST['update_Status'])) {
    $Id_Role = intval($_POST['update_id_Role']);
    $status = $_POST['update_Status'];
    $stmt = $connection->prepare("UPDATE role SET Status = ? WHERE Id_Role = ?");
    $stmt->bind_param("si", $status, $Id_Role);
    $response = $stmt->execute();
    echo json_encode(['success' => $response]);
    exit;
}

// Получение данных из таблицы role
$sql = "SELECT Id_Role, Status FROM role";
$result = $connection->query($sql);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Справочник "rion"</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table td, .table th { font-size: 12px; padding: 5px; word-wrap: break-word; }
        .table { table-layout: fixed; width: 100%; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h4 class="text-center">Справочник - rion</h4>
        <table class="table table-striped" id="roleTable">
            <thead>
                <tr>
                    <th scope="col">Id_Role</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-center">Действия</th>
                    <th scope="col">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#InsertModal">Добавить</button>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr id="row-<?php echo $row['Id_Role']; ?>">
                        <th scope="row"><?php echo htmlspecialchars($row['Id_Role']); ?></th>
                        <td><?php echo htmlspecialchars($row['Status']); ?></td>
                        <td class='text-center'>
                            <button class="btn btn-danger btn-sm" onclick="deleteRole(<?php echo $row['Id_Role']; ?>)">Удалить</button>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#UpdateModal" onclick="fillUpdateForm(<?php echo $row['Id_Role']; ?>, '<?php echo htmlspecialchars($row['Status']); ?>')">Обновить</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Модальные окна для добавления и обновления -->
    <div class="modal fade" id="InsertModal" tabindex="-1" aria-labelledby="InsertModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Добавить Rion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="add_Status" class="form-control" placeholder="Status" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-success" onclick="addRole()">Добавить</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="UpdateModal" tabindex="-1" aria-labelledby="UpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Обновить запись</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="update_id_Role">
                    <input type="text" id="update_Status" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-success" onclick="updateRole()">Обновить</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Функции для отправки AJAX-запросов
        function addRole() {
            const status = document.getElementById("add_Status").value;
            fetch("", { method: "POST", headers: { "Content-Type": "application/x-www-form-urlencoded" }, body: `add_Status=${status}` })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const row = `<tr id="row-${data.id}">
                            <th scope="row">${data.id}</th>
                            <td>${data.status}</td>
                            <td class="text-center">
                                <button class="btn btn-danger btn-sm" onclick="deleteRole(${data.id})">Удалить</button>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#UpdateModal" onclick="fillUpdateForm(${data.id}, '${data.status}')">Обновить</button>
                            </td></tr>`;
                        document.querySelector("#roleTable tbody").insertAdjacentHTML("beforeend", row);
                        document.getElementById("add_Status").value = "";
                    }
                });
        }

        function deleteRole(id) {
            if (confirm("Удалить запись?")) {
                fetch("", { method: "POST", headers: { "Content-Type": "application/x-www-form-urlencoded" }, body: `delete_id=${id}` })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) document.getElementById(`row-${id}`).remove();
                    });
            }
        }

        function updateRole() {
            const id = document.getElementById("update_id_Role").value;
            const status = document.getElementById("update_Status").value;
            fetch("", { method: "POST", headers: { "Content-Type": "application/x-www-form-urlencoded" }, body: `update_id_Role=${id}&update_Status=${status}` })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector(`#row-${id} td:nth-child(2)`).textContent = status;
                        document.getElementById("update_Status").value = "";
                    }
                });
        }

        function fillUpdateForm(id, status) {
            document.getElementById("update_id_Role").value = id;
            document.getElementById("update_Status").value = status;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
