<?php

require_once 'init.php';
require_once 'functions.php';

// Запрос для получения массива категорий
$categories = get_categories($link);

// Убедимся, что форма была отправлена. Для этого проверяем метод, которым была запрошена страница
// Если метод POST - значит этот сценарий был вызван отправкой формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // В массиве $_POST содержатся все данные из формы. Копируем его в переменную
    $sign_up_form = $_POST;
    // Затем определяем список полей, которые собираемся валидировать
    $required_fields = ['name', 'email', 'password', 'message'];
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
    // Проверим, что значение из поля «email» действительно является валидным E-mail адресом 
    if (!filter_var($sign_up_form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введите валидный E-mail адрес';
    }
    // Проверим, что указанный email уже не используется другим пользователем
    if (empty($errors)){
        $email = mysqli_real_escape_string($link, $sign_up_form['email']);
        $sql = "SELECT id FROM users WHERE email = '$email'";
        $res = mysqli_query($link, $sql);
        // Если запрос вернул больше нуля записей, значит такой поьзователь уже существует
        if (mysqli_num_rows($res) > 0) {
            $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
        }
    }
    // Проверим, был ли загружен файл. Поле для загрузки файла в форме называется 'image',
    // поэтому нам следует искать в массиве $_FILES одноименный ключ.
    // Если таковой найден, то мы можем получить имя загруженного файла

    if (!empty($_FILES['image']['name'])) {
        $tmp_name = $_FILES['image']['tmp_name'];
        $path = $_FILES['image']['name'];

        // С помощью стандартной функции finfo_ можно получить информацию о типе файле
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);

        // Если файл соответствует ожидаемому типу, то мы копируем его в директорию где лежат все изображения,
        // а также добавляем путь к загруженному изображению в массив $sign_up_form
        if ($file_type == "image/jpeg" or $file_type == "image/png") {
            move_uploaded_file($tmp_name, 'uploads/' . $path);
            $sign_up_form['image'] = ('uploads/' . $path);
        } else {
            $errors['image'] = 'Загрузите картинку в формате jpg, jpeg или png';
        }
    } else {
        $sign_up_form['image'] = '';
    }
    
    // Проверяем длину массива с ошибками.
    // Если он не пустой, значит были ошибки и мы должны показать их пользователю вместе с формой.
    // Для этого подключаем шаблон формы и передаем туда массив, где будут заполненные поля, а также список ошибок
    if (count($errors)) {
        $page_content = include_template('sign-up.php', ['sign_up_form' => $sign_up_form, 'errors' => $errors,
            'categories' => $categories]);
    }
    // Если массив ошибок пуст, значит валидации прошла успешно.
    else {
        // Отправляем форму регистрации в базу данных
        // Чтобы не хранить пароль в открытом виде преобразуем его в хеш
        $password = password_hash($sign_up_form['password'], PASSWORD_DEFAULT);
        $new_user_data = [$sign_up_form['name'], $sign_up_form['email'],
            $sign_up_form['image'], $password,
            $sign_up_form['message']];
        add_user ($link, $new_user_data);
        // Перенаправляем пользователя на страницу входа
        header("Location: /index.php");
        exit();
    }
}

// Если метод не POST, значит форма не была отправлена и валидировать ничего не надо,
// поэтому просто подключаем шаблон показа формы
else {
   $page_content = include_template('sign-up.php', ['categories' => $categories]);
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'username' => $_SESSION['user']['name'],
    'title' => 'YetiCave'
]);

print($layout_content);


