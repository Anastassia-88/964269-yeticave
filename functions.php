<?php
/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Prepared Statement
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
/**
 * @param $link
 * @param $sql
 * @param array $data
 * @return array|null
 */
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
/**
 * @param $link
 * @param $sql
 * @param array $data
 * @return array|null
 */
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
/**
 * @param $link
 * @param $sql
 * @param array $data
 * @return bool|int|string
 */
function db_insert_data($link, $sql, $data = []) {
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    $result = mysqli_stmt_execute($stmt);
    if ($result) {
        $result = mysqli_insert_id($link);
    }
    return $result;
}

// Подключение шаблона
/**
 * @param $name
 * @param $data
 * @return false|string
 */
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
/**
 * @param $price
 * @return string
 */
function price_format($price) {
    return number_format($price,0,","," ") . " &#8381;";
}

/**
 * Retrieve all categories
 * @param $link
 * @return array|null
 */
function get_categories($link){
    $sql = "select * from categories;";
    $categories = db_fetch_data($link, $sql);
    return $categories;
}

/**
 * Retrieve the name of a category from its id
 * @param $link
 * @param $category_id
 * @return array|null
 */
function get_category_name($link, $category_id) {
    $sql = "select name from categories
    where id = ?;";
    $category_name = db_fetch_data_1($link, $sql, [$category_id]);
    return $category_name;
}

/**
 * Retrieve new lots
 * @param $link
 * @return array|null
 */
function get_lots($link){
    $sql = "select 
    l.id as id, start_price, l.name as name, image, c.name as category, UNIX_TIMESTAMP(l.dt_add) as dt_add, 
       description, dt_end
    from lots l
    join categories c
    on l.category_id = c.id
    where dt_end > now()
    order by l.id desc
    LIMIT 9;";
    $lots = db_fetch_data($link, $sql);
    return $lots;
}

// Вывод лотов по категории, учитывая смещение и число гифок на странице
/**
 * @param $link
 * @param $category_id
 * @return array|null
 */
function get_lots_by_cat($link, $category_id, $cur_page, $lots){
    // Определяем число лотов на странице
    $page_items = 2;
// Узнаем общее число лотов. Считаем кол-во страниц и смещение
    $items_count = count($lots);
    $pages_count = ceil($items_count / $page_items);
    $offset = ($cur_page - 1) * $page_items;
// Заполняем массив номерами всех страниц
    $pages = range(1, $pages_count);




    $sql = "select 
    l.id as id, start_price, l.name as name, image, c.name as category, UNIX_TIMESTAMP(l.dt_add) as dt_add, 
       description, dt_end from lots l
    join categories c on l.category_id = c.id
    where category_id = ? and dt_end > now()
    order by l.id desc
    LIMIT" . $page_items . "OFFSET" . $offset;
    $lots = db_fetch_data($link, $sql, [$category_id]);
    return $lots;
}

// Полнотекстовый поиск, учитывая смещение и число гифок на странице
/**
 * @param $link
 * @param $search
 * @return array|null
 */
function search_lot ($link, $search) {
    $sql = "select 
    l.id as id, start_price, l.name as name, image, c.name as category, UNIX_TIMESTAMP(l.dt_add) as dt_add, description
    from lots l
    join categories c
    on l.category_id = c.id
    where dt_end > now() and match(l.name, description) against(?)
    order by l.id desc;";
    $lots = db_fetch_data($link, $sql, [$search]);
    return $lots;
}


function kkk($link, $category_id, $cur_page, $lots){
    // Определяем число лотов на странице
    $page_items = 2;
// Узнаем общее число лотов. Считаем кол-во страниц и смещение
    $items_count = count($lots);
    $pages_count = ceil($items_count / $page_items);
    $offset = ($cur_page - 1) * $page_items;
// Заполняем массив номерами всех страниц
    $pages = range(1, $pages_count);




    $sql = "select 
    l.id as id, start_price, l.name as name, image, c.name as category, UNIX_TIMESTAMP(l.dt_add) as dt_add, 
       description, dt_end from lots l
    join categories c on l.category_id = c.id
    where category_id = ? and dt_end > now()
    order by l.id desc
    LIMIT" . $page_items . "OFFSET" . $offset;
    $lots = db_fetch_data($link, $sql, [$category_id]);
    return $lots;
}

















// Вывод лота по id
/**
 * @param $link
 * @param $lot_id
 * @return array|null
 */
function get_lot($link, $lot_id) {
    $sql = "select
    l.id as id, start_price, l.name as name, image, c.name as category, UNIX_TIMESTAMP(l.dt_add) as dt_add, 
       description, bet_step, dt_end, user_id
    from lots l
    join categories c
    on l.category_id = c.id
    where l.id = ?;";
    $lot = db_fetch_data_1($link, $sql, [$lot_id]);
    return $lot;
}

// Добавление нового лота
/**
 * @param $link
 * @param $new_lot_data
 */
function add_lot($link, $new_lot_data) {
$sql = "insert into lots (dt_add, name, description, image, start_price, dt_end, bet_step, user_id, category_id)
values (now(), ?, ?, ?, ?, ?, ?, ?, ?)";
db_insert_data($link, $sql, $new_lot_data);
}

// Функция для проверки даты на соответствие формату
/**
 * @param $date
 * @return bool
 */
function check_date_format($date) {
    $result = false;
    $regexp = '/(\d{2})\.(\d{2})\.(\d{4})/m';
    if (preg_match($regexp, $date, $parts) && count($parts) == 4) {
        $result = checkdate($parts[2], $parts[1], $parts[3]);
    }
    return $result;
}

// Добавление в БД нового пользователя
/**
 * @param $link
 * @param $new_user_data
 */
function add_user($link, $new_user_data) {
    $sql = "INSERT INTO users (dt_add, name, email, image, password, message)
VALUES (NOW(), ?, ?, ?, ?, ?)";
    db_insert_data($link, $sql, $new_user_data);
}

// Добавление в БД новой ставки
/**
 * @param $link
 * @param $new_bet_data
 */
function add_bet($link, $new_bet_data) {
    $sql = "INSERT INTO bets (dt_add, amount, user_id, lot_id)
VALUES (NOW(), ?, ?, ?)";
    db_insert_data($link, $sql, $new_bet_data);
}

//Ищем в БД максимальную ставку по лоту
/**
 * @param $link
 * @param $lot_id
 * @return array|null
 */
function get_max_bet ($link, $lot_id) {
    $sql = "SELECT MAX(amount) from bets where lot_id = ?;";
    $max_bet = db_fetch_data_1($link, $sql, [$lot_id]);
    return $max_bet;
}

// Вывод всех ставок
/**
 * @param $link
 * @param $lot_id
 * @return array|null
 */
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

// Поиск ставки юзера по лоту
/**
 * @param $link
 * @param $lot_id
 * @param $user_id
 * @return array|null
 */
function get_user_bets($link, $lot_id, $user_id){
    $sql = "select 
    b.id
    from bets b
    where lot_id = ? and user_id = ?;";
    $bets = db_fetch_data($link, $sql, [$lot_id, $user_id]);
    return $bets;
}

// Поиск всех ставок юзера
/**
 * @param $link
 * @param $user_id
 * @return array|null
 */
function get_my_rates($link, $user_id){
    $sql = "select b.dt_add, amount, b.user_id, l.name as lot_name, c.name as category_name, message, l.image, 
       l.id as lot_id, l.dt_end, winner_id
    from bets b
    join lots l
    on b.lot_id = l.id
    join categories c
    on l.category_id = c.id
    join users u
    on l.user_id = u.id
    where b.user_id = ?;";
    $rates = db_fetch_data($link, $sql, [$user_id]);
    return $rates;
}

// Сколько дней, часов и минут осталось до окончания торгов по лоту
/**
 * @param $end_date
 * @return string
 */
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
/**
 * @param $end_date
 * @return DateInterval|string
 */
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
/**
 * @param $add_date
 * @return false|string
 */
function time_ago ($add_date) {
    // date_default_timezone_set('Europe/Berlin');
    $cur_date = date_create("now");
    $dt_add = date_create($add_date);
    $diff = date_diff($cur_date, $dt_add);
    $days_count = date_interval_format($diff, "%d");
    $hours_count = date_interval_format($diff, "%h");
    $minutes_count = date_interval_format($diff, "%i");
    if ($days_count) {
        $result = date('d.m.y \в H:i', strtotime($add_date));
    }
    elseif ($hours_count) {
        $result = "$hours_count" . " ч. назад";
    }
    else {
        $result = "$minutes_count" . " мин. назад";
    }
    return $result;
}

