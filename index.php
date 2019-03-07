<?php
require_once 'init.php';
require_once 'functions.php';

// SQL-запрос для получения списка новых лотов
$lots = get_lots($link);

// SQL-запрос для получения списка категорий
$categories = get_categories($link);

$page_content = include_template('index.php', [
    'lots' => $lots,
    'categories' => $categories
]);

$user_name = $_SESSION['user']['name'] ?? '';

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'username' => $user_name,
    'title' => 'YetiCave'
    ]);

print($layout_content);
