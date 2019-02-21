<?php

require_once 'init.php';
require_once 'functions.php';

// запрос для получения массива категорий
$categories = get_categories($link);
var_dump($categories);

// запрос для получения массива лота
$lot = get_lot($link);
var_dump($lot);

$page_content = include_template('lot.php', ['lot' => $lot, 'categories' => $categories]);
print($page_content);



















