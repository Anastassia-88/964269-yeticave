<?php
require_once 'init.php';
require_once 'functions.php';

// Запрос для получения массива категорий
$categories = get_categories($link);

$search = (isset($_GET['search'])) ? (trim($_GET['search'])) : '';

// Current page
$cur_page = $_GET['page'] ?? 1;

// Number of lots per page
$page_items = 9;

$items_count = count_lots_by_search($link, $search);

$pages_count = ceil($items_count['cnt'] / $page_items);
$offset = ($cur_page - 1) * $page_items;
// Заполняем массив номерами всех страниц
$pages = range(1, $pages_count);

$lots = search_lot($link, $search, $page_items, $offset) ?? '';

$pagination = include_template('_pagination-search.php', [
    'pages_count' => $pages_count,
    'pages' => $pages,
    'cur_page' => $cur_page,
    'search' => $search
]);

$page_content = include_template('search.php', [
    'categories' => $categories,
    'lots' => $lots,
    'search' => $search,
    'pagination' => $pagination
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content ?? '',
    'categories' => $categories,
    'username' => $user_name,
    'title' => 'YetiCave'
]);

print($layout_content);
