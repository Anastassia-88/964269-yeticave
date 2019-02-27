<?php

require_once 'init.php';
require_once 'functions.php';

// запрос для получения массива категорий
$categories = get_categories($link);

// запрос для получения массива лота
$lot_id = $_GET['id'];
$lot = get_lot($link, $lot_id);
if ($lot) {
    $page_content = include_template('lot.php', ['lot' => $lot, 'categories' => $categories]);
}
else {

    $page_content = include_template('error.php', ['categories' => $categories]);
    http_response_code (404);
}

$layout_content = include_template('layout.php',
    ['content' => $page_content, 'categories' => $categories, 'title' => 'Просмотр лота']);
print($layout_content);
