<?php

require_once 'init.php';
require_once 'functions.php';

// запрос для получения массива категорий
$categories = get_categories($link);

$page_content = include_template('login.php', ['categories' => $categories]);
print($page_content);
