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



