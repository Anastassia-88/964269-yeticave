<?php
require_once 'init.php';
require_once 'functions.php';
require_once 'get-winner.php';

$categories = get_categories($link);

$page_items = 9;
$lots = get_lots($link);

$page_content = include_template('index.php', [
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
