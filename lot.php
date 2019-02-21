<?php

require_once 'functions.php';

// запрос для получения массива категорий
$categories = get_categories($link);

// запрос для получения массива лота
$lot = get_lot($link);
var_dump($lot);

$page_content = include_template('lot.php', ['lot' => lot]);
print($page_content);



















