<?php
function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = 'Что-то пошло не так';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

function price_format($price) {
    $price_formatted = ceil($price);
    return number_format($price_formatted) . " &#8381;";
}

function get_time($current_time) { // сколько часов и минут осталось до новых суток
    date_default_timezone_set('Europe/Berlin');
    $rest_time = strtotime("tomorrow") - $current_time;
    return date("H:i", $rest_time);
}

