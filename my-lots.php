<?php
require_once 'init.php';
require_once 'functions.php';

// Получаем массив категорий
$categories = get_categories($link);

$page_content = include_template('my-lots.php', [
    'lots' => $lots,
    'categories' => $categories,
    'bets' => $bets,
    'error' => $error,
    'current_price' => $current_price,
    'min_bet' => $min_bet,
    'bets_count' => $bets_count,
    'lot_id' => $lot_id,
    'show_bet_form' => $show_bet_form
]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'username' => $user_name,
    'title' => 'YetiCave'
]);

print($layout_content);
