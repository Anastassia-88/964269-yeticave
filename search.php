<?php
require_once 'init.php';
require_once 'functions.php';

// Запрос для получения массива категорий
$categories = get_categories($link);

$search = (isset($_GET['search'])) ? (trim($_GET['search'])) : '';

$lots = search_lot($link, $search) ?? '';

$page_content = include_template('search.php', [
    'categories' => $categories,
    'lots' => $lots,
    'search' => $search
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content ?? '',
    'categories' => $categories,
    'username' => $user_name,
    'title' => 'YetiCave'
]);

print($layout_content);
