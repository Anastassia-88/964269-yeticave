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

// Получение записей из БД в виде двумерного ассоциативного массива (несколько лотов)
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

// Получение записей из БД в виде одномерного неассоциативного массива (один лот)
function db_fetch_data_1($link, $sql, $data = []) {
    $result = [];
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        $result = mysqli_fetch_assoc($res);
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
    return number_format($price,0,","," ") . " &#8381;";
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
    l.id as id, start_price, l.name as name, image, c.name as category, UNIX_TIMESTAMP(l.dt_add) as dt_add, description, dt_end
    from lots l
    join categories c
    on l.category_id = c.id
    where winner_id is null
    order by l.id desc
    LIMIT 9;";
    $lots = db_fetch_data($link, $sql);
    return $lots;
}

// Вывод лота по id
function get_lot($link, $lot_id) {
    $sql = "select
    l.id as id, start_price, l.name as name, image, c.name as category, UNIX_TIMESTAMP(l.dt_add) as dt_add, description, bet_step, dt_end
    from lots l
    join categories c
    on l.category_id = c.id
    where l.id = ?;";
    $lot = db_fetch_data_1($link, $sql, [$lot_id]);
    return $lot;
}

// Добавление нового лота
function add_lot($link, $new_lot_data) {
$sql = "insert into lots (dt_add, name, description, image, start_price, dt_end, bet_step, user_id, category_id)
values (now(), ?, ?, ?, ?, ?, ?, ?, ?)";
db_insert_data($link, $sql, $new_lot_data);
}

// Функция для проверки даты на соответствие формату
function check_date_format($date) {
    $result = false;
    $regexp = '/(\d{2})\.(\d{2})\.(\d{4})/m';
    if (preg_match($regexp, $date, $parts) && count($parts) == 4) {
        $result = checkdate($parts[2], $parts[1], $parts[3]);
    }
    return $result;
}

// Добавление в БД нового пользователя
function add_user($link, $new_user_data) {
    $sql = "INSERT INTO users (dt_add, name, email, image, password, message)
VALUES (NOW(), ?, ?, ?, ?, ?)";
    db_insert_data($link, $sql, $new_user_data);
}

// Добавление в БД новой ставки
function add_bet($link, $new_bet_data) {
    $sql = "INSERT INTO bets (dt_add, amount, user_id, lot_id)
VALUES (NOW(), ?, ?, ?)";
    db_insert_data($link, $sql, $new_bet_data);
}

//Ищем в БД максимальную ставку по лоту
function get_max_bet ($link, $lot_id) {
    $sql = "SELECT MAX(amount) from bets where lot_id = ?;";
    $max_bet = db_fetch_data_1($link, $sql, [$lot_id]);
    return $max_bet;
}

// Вывод всех ставок
function get_bets($link, $lot_id){
    $sql = "select 
    b.id, b.dt_add as dt_add, amount, u.name as name
    from bets b
    join users u
    on b.user_id = u.id
    where lot_id = ?
    order by b.id desc;";
    $bets = db_fetch_data($link, $sql, [$lot_id]);
    return $bets;
}

// Сколько дней, часов и минут осталось до окончания торгов по лоту
function time_left ($end_date) {
    // date_default_timezone_set('Europe/Berlin');
    $cur_date = date_create("now");
    $dt_end = date_create($end_date);
    $diff = date_diff($cur_date, $dt_end);
    $days_count = date_interval_format($diff, "%d");
    $hours_count = date_interval_format($diff, "%h");
    $minutes_count = date_interval_format($diff, "%i");
    $result = "$days_count" . " дн. " . "$hours_count" . " ч. " . "$minutes_count" . " мин. ";
    return $result;
}

// Сколько дней или часов и минут осталось до окончания торгов по лоту
function time_left_short ($end_date) {
    // date_default_timezone_set('Europe/Berlin');
    $cur_date = date_create("now");
    $dt_end = date_create($end_date);
    $diff = date_diff($cur_date, $dt_end);
    $days_count = date_interval_format($diff, "%d");
    if ($days_count) {
        $result = "$days_count" . " дн. ";
    }
    else {
        $result = date_interval_format($diff, "%H:%i");
    }
    return $result;
}

// Как давно была сделана запись
function time_ago ($add_date) {
    // date_default_timezone_set('Europe/Berlin');
    $cur_date = date_create("now");
    $dt_add = date_create($add_date);
    $diff = date_diff($cur_date, $dt_add);
    $days_count = date_interval_format($diff, "%d");
    $hours_count = date_interval_format($diff, "%h");
    $minutes_count = date_interval_format($diff, "%i");
    if ($days_count or $hours_count) {
        $result = "$days_count" . " дн. ";
    }
    else {
        $result = "$minutes_count" . " мин. назад";
    }
    return $result;
}

// Полнотекстовый поиск
function search_lot ($link, $search) {
    $sql = "select 
    l.id as id, start_price, l.name as name, image, c.name as category, UNIX_TIMESTAMP(l.dt_add) as dt_add, description
    from lots l
    join categories c
    on l.category_id = c.id
    where match(l.name, description) against(?)";
    $lots = db_fetch_data($link, $sql, [$search]);
    return $lots;
}
