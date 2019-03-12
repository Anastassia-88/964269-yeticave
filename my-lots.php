<?php
require_once 'init.php';
require_once 'functions.php';

// Получаем массив категорий
$categories = get_categories($link);

$rates = get_my_rates($link, $user_id);

$page_content = include_template('my-lots.php', [
    'categories' => $categories,
    'rates' => $rates,
    'user_id' => $user_id
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'username' => $user_name,
    'title' => 'YetiCave'
]);
print($layout_content);
