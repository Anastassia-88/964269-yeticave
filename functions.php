<?php







/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = null;

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);
    }

    return $stmt;
}

// Получение записей из БД
function db_fetch_data($link, $sql, $data = []) {
    $result = [];
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
    return $result;
}

// Добавление новой записи
function db_insert_data($link, $sql, $data = []) {
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    $result = mysqli_stmt_execute($stmt);
    if ($result) {
        $result = mysqli_insert_id($link);
    }
    return $result;
}

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

// Форматируем цену лота
function price_format($price) {
    $price_formatted = ceil($price);
    return number_format($price_formatted) . " &#8381;";
}

// Сколько часов и минут осталось до новых суток
function get_time($dt_add) {
    date_default_timezone_set('Europe/Berlin');
    $rest_time = strtotime("tomorrow") - $dt_add;
    return date("H:i", $rest_time);
}

// Вывод всех категорий
function get_categories($link){
    $sql = "select * from categories;";
    $categories = db_fetch_data($link, $sql);
    return $categories;
}

// Вывод новых лотов
function get_lots($link){ 
    $sql = "select 
    l.id as id, start_price, l.name as name, image, c.name as category, UNIX_TIMESTAMP(l.dt_add) as dt_add
    from lots l
    join categories c
    on l.category_id = c.id
    where winner_id is null
    order by l.id desc;";
    $lots = db_fetch_data($link, $sql);
    return $lots;
}

// Вывод лота по id
function get_lot($link) {
    $sql = "select
    l.id as id, start_price, l.name as name, image, c.name as category, UNIX_TIMESTAMP(l.dt_add) as dt_add
    from lots l
    join categories c
    on l.category_id = c.id
    where l.id = ?;";
    $lot_id = $_GET['id'];
    $lot = db_fetch_data($link, $sql,  $data = [$lot_id);
    return $lot;
}



