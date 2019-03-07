<?php
require_once 'init.php';
require_once 'functions.php';

// Запрос для получения массива категорий
$categories = get_categories($link);

// Убедимся, что форма была отправлена. Для этого проверяем метод, которым была запрошена страница
// Если метод GET - значит этот сценарий был вызван отправкой формы
var_dump($_GET);
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // В массиве $_GET содержатся все данные из формы
    //Копируем поисковый запрос пользователя в переменную
    $search = trim($_GET['search']) ?? '';



$lots = search_lot($link, $search) ?? '';




    if ($lots) {
        $page_content = include_template('search.php', [
            'categories' => $categories,
            'lots' => $lots,
            'search' => $search
        ]);
    } else {
        $page_content = include_template('error_search.php', [
            'categories' => $categories,
            'search' => $search
        ]);
    }



}

$if_page_content = $page_content ?? '';

$layout_content = include_template('layout.php', [
    'content' => $if_page_content,
    'categories' => $categories,
    'username' => $user_name,
    'title' => 'YetiCave'
]);

print($layout_content);
