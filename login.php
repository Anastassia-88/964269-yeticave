<?php

require_once 'init.php';
require_once 'functions.php';

// запрос для получения массива категорий
$categories = get_categories($link);

// Убедимся, что форма была отправлена. Для этого проверяем метод, которым была запрошена страница
// Если метод POST - значит этот сценарий был вызван отправкой формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // В массиве $_POST содержатся все данные из формы. Копируем его в переменную
    $login_form = $_POST;
    // Затем определяем список полей, которые собираемся валидировать
    $required_fields = ['email', 'password'];
    // Определяем пустой массив $errors, который будем заполнять ошибками валидации
    $errors = [];
    // Обходим массив $_POST. Здесь в переменной $key будет имя поля (из атрибута name).
    // Далее мы проверяем существование каждого поля в списке обязательных к заполнению.
    // И если оно там есть, а также поле не заполнено, то добавляем ошибку валидации в список ошибок
    foreach ($required_fields as $field) {
        if (empty($login_form[$field])) {
            $errors[$field] = 'Поле не заполнено';
        }
    }
    // Проверим, что значение из поля «email» действительно является валидным E-mail адресом
    if (!filter_var($login_form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[$email] = 'Введите валидный E-mail адрес';
    }
    // Найдем в таблице users пользователя с переданным email
    $email = mysqli_real_escape_string($link, $login_form['email']);
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $res = mysqli_query($link, $sql);

    // Проверяем, что сохраненный хеш пароля и введенный пароль из формы совпадают
    // Если совпадение есть, значит пользователь указал верный пароль
    // Тогда мы можем открыть для него сессию и записать в неё все данные о пользователе
    $user = $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : null;

    if (!count($errors) and $user) {
        if (password_verify($login_form['password'], $user['password'])) {
            $_SESSION['user'] = $user;
        } else {
            $errors['password'] = 'Неверный пароль';
        }
    }
    else {
        $errors['email'] = 'Такой пользователь не найден';
    }

    // Проверяем длину массива с ошибками.
    // Если он не пустой, значит были ошибки и мы должны показать их пользователю вместе с формой.
    // Для этого подключаем шаблон формы и передаем туда массив, где будут заполненные поля, а также список ошибок
    if (count($errors)) {
        $page_content = include_template('login.php', [
            'login_form' => $login_form,
            'errors' => $errors,
            'categories' => $categories
        ]);
    }
    // Если массив ошибок пуст, значит валидации прошла успешно.
    else {
        // Перенаправляем пользователя на главную страницу
        header("Location: /login.php");
        exit();
    }
}
// Если метод не POST, значит форма не была отправлена
// Проверяем существование сессии с пользователем
// Сессия есть - значит пользователь залогинен и ему можно показать страницу приветствия
// Сессии нет - показываем форму для входа на сайт

else {
    if (isset($_SESSION['user'])) {
        $page_content = include_template('index.php', [
            'categories' => $categories,
            'lots' => $lots,
            'username' => $_SESSION['user']['name']
        ]);



    } else {
        $page_content = include_template('login.php', [
            'categories' => $categories
        ]);
    }
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'username' => $_SESSION['user']['name'],
    'title' => 'Вход на сайт']);

print($layout_content);