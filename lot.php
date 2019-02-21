<?php

$data = [$_GET['id']];

require_once 'functions.php';

$lot = get_lot($link, $data = []);
var_dump($lot);

$page_content = include_template('lot.php', ['lot' => lot]);
print($page_content);



















