<?php

require_once 'init.php';
require_once 'functions.php';

// запрос для получения массива категорий
$categories = get_categories($link);

$page_content = include_template('sign-up.php', ['categories' => $categories]);
print($page_content);

// Убедимся, что форма была отправлена. Для этого проверяем метод, которым была запрошена страница
// Если метод POST - значит этот сценарий был вызван отправкой формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // В массиве $_POST содержатся все данные из формы. Копируем его в переменную
    $sign_up_form = $_POST;
    // Затем определяем список полей, которые собираемся валидировать
    $required_fields = ['name', 'email', 'password', 'message', 'image'];
    // Определяем пустой массив $errors, который будем заполнять ошибками валидации
    $errors = [];
    // Обходим массив $_POST. Здесь в переменной $key будет имя поля (из атрибута name).
    // Далее мы проверяем существование каждого поля в списке обязательных к заполнению.
    // И если оно там есть, а также поле не заполнено, то добавляем ошибку валидации в список ошибок
    foreach ($required_fields as $field) {
        if (empty($sign_up_form[$field])) {
            $errors[$field] = 'Поле не заполнено';
        }
    }
    // Проверка для категорий
    if ($lot['category'] == 'select'){
        $errors['category'] = 'Поле не заполнено';

    }
    // Проверим, был ли загружен файл. Поле для загрузки файла в форме называется 'image',
    // поэтому нам следует искать в массиве $_FILES одноименный ключ.
    // Если таковой найден, то мы можем получить имя загруженного файла

    if (isset($_FILES['image']['name'])) {
        $tmp_name = $_FILES['image']['tmp_name'];
        $path = $_FILES['image']['name'];

        // С помощью стандартной функции finfo_ можно получить информацию о типе файле
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);

        // Если файл соответствует ожидаемому типу, то мы копируем его в директорию где лежат все изображения,
        // а также добавляем путь к загруженному изображению в массив $sign_up_form
        if ($file_type == "image/jpeg" or $file_type == "image/png") {
            move_uploaded_file($tmp_name, 'img/' . $path);
            $lot['image'] = ('img/' . $path);
        }
        // Если файл не соответствует ожидаемому типу, добавляем ошибку
        else {
            $errors['image'] = 'Загрузите картинку в формате jpg, jpeg или png';
        }
    }
    // Если файл не был загружен, добавляем ошибку
    else {$errors['image'] = 'Вы не загрузили файл';
    }

    // Проверяем длину массива с ошибками.
    // Если он не пустой, значит были ошибки и мы должны показать их пользователю вместе с формой.
    // Для этого подключаем шаблон формы и передаем туда массив, где будут заполненные поля, а также список ошибок
    if (count($errors)) {
        $page_content = include_template('sign-up.php', ['sign_up_form' => $sign_up_form, 'errors' => $errors, 'categories' => $categories]);
    }
    // Если массив ошибок пуст, значит валидации прошла успешно.
    else {
        // Отправляем форму регистрации в базу данных
        $new_user_data = [$sign_up_form['dt_add'], $sign_up_form['name'], $sign_up_form['email'], $sign_up_form['image'], $sign_up_form['password'],
            $sign_up_form['message']];
        add_user($link, $new_user_data);
        // Перенаправляем пользователя на страницу входа
        header("Location: login.php");
    }
}

// Если метод не POST, значит форма не была отправлена и валидировать ничего не надо,
// поэтому просто подключаем шаблон показа формы
else {
    $page_content = include_template('sign-up.php', ['categories' => $categories]);
}

print($page_content);
