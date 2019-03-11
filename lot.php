<?php
require_once 'init.php';
require_once 'functions.php';

$categories = get_categories($link);

$lot_id = $_GET['id'] ?? '';
$lot = get_lot($link, $lot_id);

// Получаем массив ставок
$bets = get_bets($link, $lot_id);

// Условия показа блока добавления ставки:
// юзер авторизован, срок размещения лота не истёк, лот создан др. юзером, юзер еще не делал ставки по этому лоту
$condition_1 = isset($_SESSION['user']);
$condition_2 = strtotime("now") < strtotime($lot['dt_end']);
$condition_3 = $lot['user_id'] === $user_id;
$condition_4 = get_user_bets($link, $lot_id, $user_id);
$show_bet_form = $condition_1 && $condition_2 && !$condition_3 && !$condition_4;

// Max rate search
$max_bet_array = get_max_bid($link, $lot_id);
$max_bet = $max_bet_array['amount'];

// Current lot price definition
$current_price = ($max_bet) ? $max_bet : $lot['start_price'];

// Minimum rate definition
$min_bet = ($max_bet) ? ($current_price + $lot['bet_step']) : $current_price;

// Total number of rates
$bets_count = count($bets);

// Adding a new rate
// Убедимся, что форма была отправлена. Для этого проверяем метод, которым была запрошена страница
// Если метод POST - значит этот сценарий был вызван отправкой формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // В массиве $_POST содержатся все данные из формы. Копируем его в переменную $bet
    $bet = $_POST;

    // Валидация
    // Проверяем, что поле со ставкой заполнено
    if (empty($bet['amount'])) {
        $error = 'Введите вашу ставку';
    }

    // Содержимое поля должно быть целым числом больше нуля
    if (empty($error) && (!intval($bet['amount']) or intval($bet['amount']) <= 0)) {
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
        'error' => $error ?? '',
        'current_price' => $current_price,
        'min_bet' => $min_bet,
        'bets_count' => $bets_count,
        'lot_id' => $lot_id,
        'show_bet_form' => $show_bet_form
    ]);
} else {
    $page_content = include_template('error_404.php', [
            'categories' => $categories
        ]
    );
    http_response_code(404);
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'username' => $user_name,
    'title' => 'YetiCave'
]);
print($layout_content);
