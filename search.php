<?php
require_once 'init.php';
require_once 'functions.php';

// Запрос для получения массива категорий
$categories = get_categories($link);

// Получаем поисковый запрос пользователя
$search = trim($_GET['search']) ?? '';

if ($search) {
    $lots = search_lot ($link, $search);
}

if ($lots) {
    $page_content = include_template('search.php', [
        'categories' => $categories,
        'lots' => $lots,
        'search' => $search
    ]);
}
else {
    $page_content = include_template('error_search.php', [
        'categories' => $categories,
        'search' => $search
    ]);
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'username' => $_SESSION['user']['name'],
    'title' => 'YetiCave'
]);

print($layout_content);
