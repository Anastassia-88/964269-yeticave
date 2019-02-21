<?php

require_once 'functions.php';

$lot = get_lot($link);
var_dump($lot);

$page_content = include_template('lot.php', ['lot' => lot]);
print($page_content);



















