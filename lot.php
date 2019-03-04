<?php

require_once 'init.php';
require_once 'functions.php';

// Получаем массив категорий
$categories = get_categories($link);

// Получаем массив лота
$lot_id = $_GET['id'];
$lot = get_lot($link, $lot_id);

// Получаем массив ставок
$bets = get_bets($link, $lot_id);

// Блок добавления ставки не показывается если 
// пользователь не авторизован
// срок размещения лота истёк
// лот создан текущим пользователем

isset($_SESSION['user'])
$lot['user_id'] == $_SESSION['user']['id']
strtotime("now") strtotime($lot['dt_end']) 
    
//Ищем в БД максимальную ставку по лоту
$max_bet_array = get_max_bet ($link, $lot_id);
$max_bet = $max_bet_array['MAX(amount)'];

// Определяем текущую цену лота
if ($max_bet) {
    $current_price = $max_bet;
}
else {
    $current_price = $lot['start_price'];
}

// Определяем минимальную ставку
if ($max_bet) {
    $min_bet = $current_price + $lot['bet_step'];
}
else {
    $min_bet = $current_price;
}

// Считаем общее количество ставок
$bets_count = count($bets);

// Добавление ставки
// Убедимся, что форма была отправлена. Для этого проверяем метод, которым была запрошена страница
// Если метод POST - значит этот сценарий был вызван отправкой формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // В массиве $_POST содержатся все данные из формы. Копируем его в переменную $bet
    $bet = $_POST;
    
    // Валидация
    // Проверяем, что поле со ставкой заполнено
    if (empty($bet['amount'])) {
        $error = 'Введите вашу ставку';
    }

    // Содержимое поля должно быть целым числом больше нуля
    if (empty($error) && (!intval($bet['amount']) or intval($bet['amount'])<=0)) {
        $error = 'Введите число больше нуля';
    }

    // Содержимое поля должно быть больше минимальной ставки
    if (empty($error) && ($bet['amount'] < $min_bet)) {
        $error = 'Введите число больше мин. ставки';
    }

    // Если ошибок нет, отправляем ставку в базу данных
    if (empty($error)) {
        $new_bet_data = [intval($bet['amount']), $_SESSION['user']['id'], $lot['id']];
        add_bet($link, $new_bet_data);
        header("Location: /lot.php?id=$lot_id");
        exit();
    }
}

if ($lot) {
    $page_content = include_template('lot.php', [
        'lot' => $lot,
        'categories' => $categories,
        'bets' => $bets,
        'error' => $error,
        'current_price' => $current_price,
        'min_bet' => $min_bet,
        'bets_count' => $bets_count,
        'lot_id' => $lot_id

    ]);
}

else {
    $page_content = include_template('error_404.php', [
        'categories' => $categories]
    );
    http_response_code (404);
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'username' => $_SESSION['user']['name'],
    'title' => 'YetiCave'
]);



print($layout_content);
