<?php

/**
 * Returns a prepared statement
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $sql string - SQL query with placeholders instead of values / SQL запрос с плейсхолдерами вместо значений
 * @param array $data - Values to insert instead of placeholders / Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Prepared statement / Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = [])
{
    $stmt = mysqli_prepare($link, $sql);

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = null;

            if (is_int($value)) {
                $type = 'i';
            } else {
                if (is_string($value)) {
                    $type = 's';
                } else {
                    if (is_double($value)) {
                        $type = 'd';
                    }
                }
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

/**
 * Returns data from MySQL database as associative two-dimensional array
 * Возвращает записи из БД в виде двумерного ассоциативного массива
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $sql string - SQL query with placeholders instead of values / SQL запрос с плейсхолдерами вместо значений
 * @param array $data - Values to insert instead of placeholders / Данные для вставки на место плейсхолдеров
 * @return array|null - Двумерный ассоциативный массив
 */
function db_fetch_data($link, $sql, $data = [])
{
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
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $sql string - SQL query with placeholders instead of values / SQL запрос с плейсхолдерами вместо значений
 * @param array $data - Values to insert instead of placeholders / Данные для вставки на место плейсхолдеров
 * @return array|null
 */
function db_fetch_data_1($link, $sql, $data = [])
{
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
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $sql string - SQL query with placeholders instead of values / SQL запрос с плейсхолдерами вместо значений
 * @param array $data - Values to insert instead of placeholders / Данные для вставки на место плейсхолдеров
 * @return bool|int|string
 */
function db_insert_data($link, $sql, $data = [])
{
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    $result = mysqli_stmt_execute($stmt);
    if ($result) {
        $result = mysqli_insert_id($link);
    }
    return $result;
}

/**
 * Includes template / Подключает шаблон
 *
 * @param $name
 * @param $data - Values to insert instead of placeholders / Данные для вставки на место плейсхолдеров
 * @return false|string
 */
function include_template($name, $data)
{
    $name = 'templates/' . $name;
    $result = 'Что-то пошло не так';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    RETURN $result;
}

/**
 * Formats price
 *
 * @param $price
 * @return string
 */
function price_format($price)
{
    RETURN number_format($price, 0, ",", " ") . " &#8381;";
}

/**
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @return array|null
 */
function get_categories($link)
{
    $sql = "SELECT * from categories;";
    $categories = db_fetch_data($link, $sql);
    RETURN $categories;
}

/**
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $category_id
 * @return array|null
 */
function get_category_name($link, $category_id)
{
    $sql = "SELECT name from categories
    where id = ?;";
    $category_name = db_fetch_data_1($link, $sql, [$category_id]);
    RETURN $category_name;
}

/**
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @return array|null
 */
function get_lots($link)
{
    $sql = "SELECT 
    l.id as id, start_price, l.name as name, image, c.name as category, UNIX_TIMESTAMP(l.dt_add) as dt_add, 
       description, dt_end
    FROM lots l
    JOIN categories c
    ON l.category_id = c.id
    WHERE dt_end > now()
    ORDER BY l.id DESC
    LIMIT 9;";
    $lots = db_fetch_data($link, $sql);
    RETURN $lots;
}

/**
 * Finds max lot bid
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $lot_id
 * @return array|null - Max rate
 */
function get_max_bid($link, $lot_id)
{
    $sql = "SELECT amount FROM bets WHERE lot_id = ? ORDER BY amount DESC LIMIT 1;";
    $max_bid = db_fetch_data_1($link, $sql, [$lot_id]);
    RETURN $max_bid;
}

/**
 * Counts lots by category
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $category_id
 * @return array|null
 */
function count_lots_by_cat($link, $category_id)
{
    $sql = "SELECT COUNT(*) as cnt from lots 
    where category_id = ? and dt_end > now();";
    $result = db_fetch_data_1($link, $sql, [$category_id]);
    RETURN $result;
}

/**
 * Counts lots by search
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $search string - Search query / Поисковой запрос
 * @return array|null
 */
function count_lots_by_search($link, $search)
{
    $sql = "SELECT COUNT(*) as cnt from lots 
    where dt_end > now() and match(name, description) against(?);";
    $result = db_fetch_data_1($link, $sql, [$search]);
    RETURN $result;
}

// Вывод лотов по категории
/**
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $category_id
 * @param $page_items
 * @param $offset
 * @return array|null
 */
function get_lots_by_cat($link, $category_id, $page_items, $offset)
{
    $sql = "SELECT l.id as id, start_price, l.name as name, image, c.name as category, 
       UNIX_TIMESTAMP(l.dt_add) as dt_add, description, dt_end from lots l
    join categories c on l.category_id = c.id
    where category_id = ? and dt_end > now()
    order by l.id desc
    LIMIT ? OFFSET ?;";
    $lots = db_fetch_data($link, $sql, [$category_id, $page_items, $offset]);
    RETURN $lots;
}

// Полнотекстовый поиск
/**
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $search string - Search query / Поисковой запрос
 * @param $page_items
 * @param $offset
 * @return array|null
 */
function search_lot($link, $search, $page_items, $offset)
{
    $sql = "SELECT 
    l.id as id, start_price, l.name as name, image, c.name as category, UNIX_TIMESTAMP(l.dt_add) as dt_add, description
    from lots l
    join categories c
    on l.category_id = c.id
    where dt_end > now() and match(l.name, description) against(?)
    order by l.id desc
    LIMIT ? OFFSET ?;";
    $lots = db_fetch_data($link, $sql, [$search, $page_items, $offset]);
    RETURN $lots;
}

/**
 * Gets lot by id
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $lot_id
 * @return array|null
 */
function get_lot($link, $lot_id)
{
    $sql = "SELECT
    l.id as id, start_price, l.name as name, image, c.name as category, UNIX_TIMESTAMP(l.dt_add) as dt_add, 
       description, bet_step, dt_end, user_id
    from lots l
    join categories c
    on l.category_id = c.id
    where l.id = ?;";
    $lot = db_fetch_data_1($link, $sql, [$lot_id]);
    RETURN $lot;
}

/**
 * Adds a new lot to a MySQL database / Добавляет новый лот в БД
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $data - Values to insert instead of placeholders / Данные для вставки на место плейсхолдеров
 */
function add_lot($link, $data)
{
    $sql = "insert into lots (dt_add, name, description, image, start_price, dt_end, bet_step, user_id, category_id)
values (now(), ?, ?, ?, ?, STR_TO_DATE(?, \"%d.%m.%Y\"), ?, ?, ?)";
    db_insert_data($link, $sql, $data);
}

// Функция для проверки даты на соответствие формату
/**
 * @param $date
 * @return bool
 */
function check_date_format($date)
{
    $result = false;
    $regexp = '/(\d{2})\.(\d{2})\.(\d{4})/m';
    if (preg_match($regexp, $date, $parts) && count($parts) == 4) {
        $result = checkdate($parts[2], $parts[1], $parts[3]);
    }
    RETURN $result;
}

/**
 * Adds a new user to a MySQL database / Добавляет нового юзера в БД
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $data - Values to insert instead of placeholders / Данные для вставки на место плейсхолдеров
 */
function add_user($link, $data)
{
    $sql = "INSERT INTO users (dt_add, name, email, image, password, message)
VALUES (NOW(), ?, ?, ?, ?, ?)";
    db_insert_data($link, $sql, $data);
}

/**
 * Adds a new bid to a MySQL database / Добавляет новую ставку по лоту в БД
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $data - Values to insert instead of placeholders
 */
function add_bet($link, $data)
{
    $sql = "INSERT INTO bets (dt_add, amount, user_id, lot_id)
VALUES (NOW(), ?, ?, ?)";
    db_insert_data($link, $sql, $data);
}

/**
 * Gets all lot bids
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $lot_id
 * @return array|null
 */
function get_bets($link, $lot_id)
{
    $sql = "SELECT 
    b.id, b.dt_add as dt_add, amount, u.name as name
    from bets b
    join users u
    on b.user_id = u.id
    where lot_id = ?
    order by b.id desc;";
    $bets = db_fetch_data($link, $sql, [$lot_id]);
    RETURN $bets;
}

// Поиск ставки юзера по лоту
/**
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $lot_id
 * @param $user_id
 * @return array|null
 */
function get_user_bets($link, $lot_id, $user_id)
{
    $sql = "SELECT 
    b.id
    from bets b
    where lot_id = ? and user_id = ?;";
    $bets = db_fetch_data($link, $sql, [$lot_id, $user_id]);
    RETURN $bets;
}

// Поиск всех ставок юзера
/**
 * Finds all user's bids
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $user_id
 * @return array|null - Array with all user's bids
 */
function get_my_rates($link, $user_id)
{
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
    RETURN $rates;
}

// Сколько дней, часов и минут осталось до окончания торгов по лоту
/**
 * @param $end_date
 * @return string
 */
function time_left($end_date)
{
    // date_default_timezone_set('Europe/Berlin');
    $cur_date = date_create("now");
    $dt_end = date_create($end_date);
    $diff = date_diff($cur_date, $dt_end);
    $days_count = date_interval_format($diff, "%a");
    $hours_count = date_interval_format($diff, "%h");
    $minutes_count = date_interval_format($diff, "%i");
    $result = "$days_count" . " дн. " . "$hours_count" . " ч. " . "$minutes_count" . " мин. ";
    RETURN $result;
}

// Сколько дней или часов и минут осталось до окончания торгов по лоту
/**
 * @param $end_date
 * @return DateInterval|string
 */
function time_left_short($end_date)
{
    // date_default_timezone_set('Europe/Berlin');
    $cur_date = date_create("now");
    $dt_end = date_create($end_date);
    $diff = date_diff($cur_date, $dt_end);
    $days_count = date_interval_format($diff, "%a");
    if ($days_count) {
        $result = "$days_count" . " дн. ";
    } else {
        $result = date_interval_format($diff, "%H:%i");
    }
    RETURN $result;
}

// Как давно была сделана запись
/**
 * @param $add_date
 * @return false|string
 */
function time_ago($add_date)
{
    // date_default_timezone_set('Europe/Berlin');
    $cur_date = date_create("now");
    $dt_add = date_create($add_date);
    $diff = date_diff($cur_date, $dt_add);
    $days_count = date_interval_format($diff, "%d");
    $hours_count = date_interval_format($diff, "%h");
    $minutes_count = date_interval_format($diff, "%i");
    if ($days_count) {
        $result = date('d.m.y \в H:i', strtotime($add_date));
    } elseif ($hours_count) {
        $result = "$hours_count" . " ч. назад";
    } else {
        $result = "$minutes_count" . " мин. назад";
    }
    RETURN $result;
}
