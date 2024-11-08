<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель навигации</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-color: #f4f4f4;
        }
        /* Стили для левой панели */
        .sidebar {
            width: 200px;
            height: 100vh;
            background-color: #333;
            padding-top: 20px;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 15px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .sidebar a:hover {
            background-color: #575757;
        }
        .sidebar a.active {
            background-color: #4CAF50;
        }
        /* Стили для основного контента */
        .content {
            margin-left: 200px;
            padding: 20px;
            flex-grow: 1;
        }
        iframe {
            width: 100%;
            height: 100vh;
            border: none;
            transition: opacity 0.5s ease;
        }
        /* Плавная анимация */
        iframe.hidden {
            opacity: 0;
        }
        /* Адаптивность для мобильных устройств */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .content {
                margin-left: 0;
            }
            iframe {
                height: 70vh; /* Уменьшаем высоту iframe для мобильных */
            }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="/php/Adres.php" target="content-frame" onclick="setActive(this)">Адреса</a>
        <a href="/php/Apportionment.php" target="content-frame" onclick="setActive(this)">Квартиры</a>
        <a href="/php/Cient.php" target="content-frame" onclick="setActive(this)">Клиенты</a>
        <a href="/php/Dogovor.php" target="content-frame" onclick="setActive(this)">Договоры</a>
        <a href="/php/Jk.php" target="content-frame" onclick="setActive(this)">Жилые комплексы</a>
        <a href="/php/Status.php" target="content-frame" onclick="setActive(this)">Статус</a>
        <a href="/php/Users.php" target="content-frame" onclick="setActive(this)">Пользователи</a>
        <a href="/php/Rabotnic.php" target="content-frame" onclick="setActive(this)">Работники</a>
        <a href="/php/tipcvartir.php" target="content-frame" onclick="setActive(this)">Тип квартир</a>

    </div>

    <div class="content">
        <!-- Загружаемая страница -->
        <iframe name="content-frame" src="/php/Adres.php" id="content-frame"></iframe>
    </div>

    <script>
        // Функция для активного состояния ссылок
        function setActive(element) {
            // Удаляем класс active у всех ссылок
            const links = document.querySelectorAll('.sidebar a');
            links.forEach(link => link.classList.remove('active'));

            // Добавляем класс active к выбранной ссылке
            element.classList.add('active');

            // Сохраняем активную ссылку в localStorage
            localStorage.setItem('activeLink', element.getAttribute('href'));
        }

        // Восстановление активного состояния ссылки при загрузке страницы
        window.onload = function() {
            const activeLink = localStorage.getItem('activeLink');
            if (activeLink) {
                const link = document.querySelector(`.sidebar a[href="${activeLink}"]`);
                if (link) {
                    link.classList.add('active');
                    document.getElementById('content-frame').src = activeLink; // Устанавливаем нужную страницу в iframe
                }
            }
        }

        // Плавная анимация при загрузке iframe
        const iframe = document.getElementById('content-frame');
        iframe.addEventListener('load', function() {
            iframe.classList.remove('hidden');
        });

        // Скрытие iframe при смене содержимого
        document.querySelectorAll('.sidebar a').forEach(link => {
            link.addEventListener('click', function() {
                iframe.classList.add('hidden');
            });
        });
    </script>

</body>
</html>
