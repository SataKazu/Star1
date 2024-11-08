<?php
// Подключаем файл с настройками базы данных
include 'D:\1\OSPanel\domains\bata\php\database\db.php';

// Добавление нового пользователя
if (isset($_POST['add'])) {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $employee_id = $_POST['employee_id'];
    $role_id = $_POST['role'];

    $insertQuery = "INSERT INTO users (Login, Password, Id_Emp, Id_Role) VALUES ('$login', '$password', '$employee_id', '$role_id')";
    if (!mysqli_query($connection, $insertQuery)) {
        die("Ошибка при добавлении пользователя: " . mysqli_error($connection));
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Удаление пользователя
if (isset($_POST['delete'])) {
    $id_Users = $_POST['id_Users'];
    $deleteQuery = "DELETE FROM users WHERE id_Users = $id_Users";
    if (!mysqli_query($connection, $deleteQuery)) {
        die("Ошибка при удалении пользователя: " . mysqli_error($connection));
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Обновление пользователя
if (isset($_POST['update'])) {
    $id_Users = $_POST['id_Users'];
    $login = $_POST['login'];
    $password = $_POST['password'];
    $employee_id = $_POST['employee_id'];
    $role_id = $_POST['role'];

    $updateQuery = "UPDATE users SET Login='$login', Password='$password', Id_Emp='$employee_id', Id_Role='$role_id' WHERE id_Users='$id_Users'";
    if (!mysqli_query($connection, $updateQuery)) {
        die("Ошибка при обновлении пользователя: " . mysqli_error($connection));
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// SQL-запрос для выборки данных из таблицы users с использованием JOIN
$sql = "
    SELECT 
        users.id_Users, 
        users.Login, 
        users.Password, 
        employee.first_name,  
        employee.middle_name, 
        employee.last_name,   
        role.Status  
    FROM 
        users 
    JOIN 
        employee ON users.Id_Emp = employee.employee_id
    JOIN  
        role ON users.Id_Role = role.Id_Role
";

$result = mysqli_query($connection, $sql);
if (!$result) {
    die("Ошибка выполнения запроса: " . mysqli_error($connection));
}

// Получаем список сотрудников для выпадающего списка
$employeeQuery = "SELECT employee_id, first_name, middle_name, last_name FROM employee";
$employeeResult = mysqli_query($connection, $employeeQuery);
if (!$employeeResult) {
    die("Ошибка выполнения запроса сотрудников: " . mysqli_error($connection));
}

// Получаем список ролей для выпадающего списка
$roleQuery = "SELECT Id_Role, Status FROM role";
$roleResult = mysqli_query($connection, $roleQuery);
if (!$roleResult) {
    die("Ошибка выполнения запроса ролей: " . mysqli_error($connection));
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление пользователями</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h3>Таблица пользователей</h3>

    <!-- Форма для добавления нового пользователя -->
    <form method="POST" action="" class="mb-4">
        <h4>Добавить нового пользователя</h4>
        <div class="form-group">
            <label for="login">Логин</label>
            <input type="text" class="form-control" name="login" required>
        </div>
        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" class="form-control" name="password" required>
        </div>

        <!-- Комбобокс для выбора сотрудника -->
        <div class="form-group">
            <label for="employee_id">Сотрудник</label>
            <select name="employee_id" class="form-control" required>
                <option value="">Выберите сотрудника</option>
                <?php
                while ($employee = mysqli_fetch_assoc($employeeResult)) {
                    $fullName = $employee['first_name'] . ' ' . $employee['middle_name'] . ' ' . $employee['last_name'];
                    echo '<option value="' . $employee['employee_id'] . '">' . htmlspecialchars($fullName) . '</option>';
                }
                ?>
            </select>
        </div>

        <!-- Комбобокс для выбора роли -->
        <div class="form-group">
            <label for="role">Роль</label>
            <select name="role" class="form-control" required>
                <option value="">Выберите роль</option>
                <?php
                while ($role = mysqli_fetch_assoc($roleResult)) {
                    echo '<option value="' . $role['Id_Role'] . '">' . htmlspecialchars($role['Status']) . '</option>';
                }
                ?>
            </select>
        </div>

        <button type="submit" name="add" class="btn btn-primary">Добавить</button>
    </form>

    <!-- Таблица с пользователями -->
    <table class="table table-sm">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Логин</th>
                <th>Пароль</th>
                <th>Имя</th>
                <th>Отчество</th>
                <th>Фамилия</th>
                <th>Роль</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    echo '<th scope="row">' . htmlspecialchars($row["id_Users"]) . '</th>';
                    echo '<td>' . htmlspecialchars($row["Login"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["Password"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["first_name"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["middle_name"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["last_name"]) . '</td>';
                    echo '<td>' . htmlspecialchars($row["Status"]) . '</td>';
                    echo '<td>';
                    echo '<button class="btn btn-warning" data-toggle="modal" data-target="#updateModal" data-id="' . $row['id_Users'] . '" data-login="' . $row['Login'] . '" data-password="' . $row['Password'] . '" data-employee="' . $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name'] . '" data-role="' . $row['Status'] . '">Изменить</button>';
                    echo ' <form method="POST" style="display:inline;" action="">
                              <input type="hidden" name="id_Users" value="' . $row['id_Users'] . '">
                              <button type="submit" name="delete" class="btn btn-danger">Удалить</button>
                          </form>';
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="8" class="text-center">Нет результатов</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Модальное окно для обновления пользователя -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Изменить пользователя</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_Users" id="updateId">
                    <div class="form-group">
                        <label for="updateLogin">Логин</label>
                        <input type="text" class="form-control" name="login" id="updateLogin" required>
                    </div>
                    <div class="form-group">
                        <label for="updatePassword">Пароль</label>
                        <input type="password" class="form-control" name="password" id="updatePassword" required>
                    </div>
                    <div class="form-group">
                        <label for="updateEmployee">Сотрудник</label>
                        <select name="employee_id" id="updateEmployee" class="form-control" required>
                            <?php
                            mysqli_data_seek($employeeResult, 0);
                            while ($employee = mysqli_fetch_assoc($employeeResult)) {
                                $fullName = $employee['first_name'] . ' ' . $employee['middle_name'] . ' ' . $employee['last_name'];
                                echo '<option value="' . $employee['employee_id'] . '">' . htmlspecialchars($fullName) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="updateRole">Роль</label>
                        <select name="role" id="updateRole" class="form-control" required>
                            <?php
                            mysqli_data_seek($roleResult, 0);
                            while ($role = mysqli_fetch_assoc($roleResult)) {
                                echo '<option value="' . $role['Id_Role'] . '">' . htmlspecialchars($role['Status']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button type="submit" name="update" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
$('#updateModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var login = button.data('login');
    var password = button.data('password');
    var employee = button.data('employee');
    var role = button.data('role');

    var modal = $(this);
    modal.find('#updateId').val(id);
    modal.find('#updateLogin').val(login);
    modal.find('#updatePassword').val(password);
    modal.find('#updateEmployee').val(employee);
    modal.find('#updateRole').val(role);
});
</script>

</body>
</html>
