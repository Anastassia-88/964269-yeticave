<?php
require_once 'init.php';
require_once 'functions.php';

// Получаем массив категорий
$categories = get_categories($link);

// Получаем массив лотов
$category_id = $_GET['id'];
$lots = get_lots_by_cat($link, $category_id);

$page_content = include_template('all-lots.php', [
    'lots' => $lots,
    'categories' => $categories
]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'username' => $user_name,
    'title' => 'YetiCave'
]);

print($layout_content);
