<?php

require_once 'init.php';
require_once 'functions.php';

// запрос для получения массива категорий
$categories = get_categories($link);

$page_content = include_template('add.php', ['categories' => $categories]);

// Убедимся, что форма была отправлена. Для этого проверяем метод, которым была запрошена страница
// Если метод POST - значит этот сценарий был вызван отправкой формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // В массиве $_POST содержатся все данные из формы. Копируем его в переменную $lot
    $lot = $_POST;
    var_dump($lot);
    // Затем определяеи список полей, которые собираемся валидировать
    $required = ['name', 'category', 'description', 'image', 'start_price', 'bet_step', 'dt_end'];
    // Определяем пустой массив $errors, который будем заполнять ошибками валидации
    $errors = [];
    // Обходим массив $_POST. Здесь в переменной $key будет имя поля (из атрибута name).
    // Далее мы проверяем существование каждого поля в списке обязательных к заполнению.
    // И если оно там есть, а также поле не заполнено, то добавляем ошибку валидации в список ошибок
    foreach ($required as $key) {
        if (empty($_POST[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
        }
    }
    // Проверим, был ли загружен файл. Поле для загрузки файла в форме называется 'image',
    // поэтому нам следует искать в массиве $_FILES одноименный ключ.
    // Если таковой найден, то мы можем получить имя загруженного файла
    if (isset($_FILES['image']['name'])) {
        $tmp_name = $_FILES['image']['tmp_name'];
        $path = $_FILES['image']['name'];

        // С помощью стандартной функции finfo_ можно получить информацию о типе файле
        $finfo = finfo_open(fileinfo_mime_type);
        $file_type = finfo_file($finfo, $tmp_name);
        if ($file_type !== "image/jpeg" or "image/png") {
            $errors['file'] = 'Загрузите картинку в формате jpg, jpeg или png';
        }
        // Если файл соответствует ожидаемому типу, то мы копируем его в директорию где лежат все изображения,
        // а также добавляем путь к загруженному изображению в массив $lot
        else {
            move_uploaded_file($tmp_name, 'img/' . $path);
            $lot['img'] = $path;
        }
    }
    // Если файл не был загружен, добавляем ошибку
    else {$errors['image'] = 'Вы не загрузили файл';
    }

    // Проверяем длину массива с ошибками.
    // Если он не пустой, значит были ошибки и мы должны показать их пользователю вместе с формой.
    // Для этого подключаем шаблон формы и передаем туда массив, где будут заполненные поля, а также список ошибок
    if (count($errors)) {
        $page_content = include_template('add.php', ['lot' => $lot, 'errors' => $errors, 'dict' => $dict, 'categories' => $categories]);
    }
    // Если массив ошибок пуст, значит валидации прошла успешно.
    else {
        // Отправляем лот в базу данных
        $new_lot_data = [$lot['name'], $lot['description'], $lot['image'], $lot['start_price'], $lot['dt_end'], $lot['bet_step'], $lot['category']];
        add_lot($link, $new_lot_data);
        // Получаем ID нового лота и перенаправляем пользователя на страницу с его просмотром
        $lot_id = mysqli_insert_id($link);
        header("Location: lot.php?id=" . $lot_id);
    }
}

// Если метод не POST, значит форма не была отправлена и валидировать ничего не надо,
// поэтому просто подключаем шаблон показа формы
else {
    $page_content = include_template('add.php', ['categories' => $categories]);
}


print($page_content);