<?php
require_once 'init.php';
require_once 'functions.php';

$categories = get_categories($link);
$category_id = $_GET['id'] ?? '';

// Current page / Текущая страница
$cur_page = $_GET['page'] ?? 1;

// Number of lots per page / Количество лотов на странице
$page_items = 9;

$items_count = count_lots_by_cat($link, $category_id);

$pages_count = $items_count['cnt'] ? (int)ceil($items_count['cnt'] / $page_items) : '';
$offset = ($cur_page - 1) * $page_items;
// Заполняем массив номерами всех страниц
$pages = range(1, $pages_count);

$category_name = get_category_name($link, $category_id);
// Lots (multi-dimensional array)
$lots = get_lots_by_cat($link, $category_id, $page_items, $offset);

$pagination = include_template('_pagination-cat.php', [
    'pages_count' => $pages_count,
    'pages' => $pages,
    'cur_page' => $cur_page,
    'category_id' => $category_id
]);

$page_content = include_template('all-lots.php', [
    'lots' => $lots,
    'categories' => $categories,
    'category_name' => $category_name,
    'pagination' => $pagination
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'username' => $user_name,
    'title' => 'YetiCave'
]);
print($layout_content);
