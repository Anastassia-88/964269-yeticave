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
            } elseif(is_string($value)) {
                $type = 's';
            } elseif (is_double($value)) {
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

/**
 * Returns data from MySQL database as associative two-dimensional array
 * Возвращает записи из БД в виде двумерного ассоциативного массива (например, несколько лотов)
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $sql string - SQL query with placeholders instead of values / SQL запрос с плейсхолдерами вместо значений
 * @param array $data - Values to insert instead of placeholders / Данные для вставки на место плейсхолдеров
 * @return array|null - Data from MySQL database as associative two-dimensional array / Записи из БД в виде двумерного ассоциативного массива
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

/**
 * Returns data from MySQL database as associative one-dimensional array
 * Возвращает записи из БД в виде одномерного ассоциативного массива (например, один лот)
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $sql string - SQL query with placeholders instead of values / SQL запрос с плейсхолдерами вместо значений
 * @param array $data - Values to insert instead of placeholders / Данные для вставки на место плейсхолдеров
 * @return array|null - Data from MySQL database as associative one-dimensional array / Записи из БД в виде одномерного ассоциативного массива
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

/**
 * Inserts a new record to MySQL database / Добавляет новую запись в БД
 *
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
 * Includes template from the folder "templates" / Подключает шаблон из папки "templates"
 *
 * @param $name string - Template file name / Имя файла шаблона
 * @param $data - Values to insert instead of placeholders / Данные для вставки на место плейсхолдеров
 * @return false|string - Returns "true" if the template was included / Возвращает "true", если шаблон был подключен
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
    return $result;
}

/**
 * Formats price / Форматирует цену
 *
 * @param $price - Данные для форматирования
 * @return string - Отформатированная цена
 */
function price_format($price)
{
    return number_format($price, 0, ",", " ") . " &#8381;";
}

/**
 * Returns categories from MySQL database / Возвращает из БД категории
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @return array|null - Categories from MySQL database as associative two-dimensional array
 */
function get_categories($link)
{
    $sql = "SELECT * FROM categories;";
    $categories = db_fetch_data($link, $sql);
    return $categories;
}

/**
 * Returns category name from MySQL database by category id / Возвращает имя категории из БД по id категории
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $category_id - Category id / id категории
 * @return array|null - Category name / Имя категории
 */
function get_category_name($link, $category_id)
{
    $sql = "SELECT name FROM categories WHERE id = ?;";
    $category_name = db_fetch_data_1($link, $sql, [$category_id]);
    return $category_name;
}

/**
 * Returns 9 new lots from MySQL database / Возвращает из БД 9 новых лотов
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @return array|null - 9 lots from MySQL database as associative two-dimensional array
 */
function get_lots($link)
{
    $sql = "SELECT 
    l.id AS id, start_price, l.name AS name, image, c.name AS category, UNIX_TIMESTAMP(l.dt_add) AS dt_add, 
       description, dt_end
    FROM lots l
    JOIN categories c
      ON l.category_id = c.id
    WHERE dt_end > now()
    ORDER BY l.id DESC
    LIMIT 9;";
    $lots = db_fetch_data($link, $sql);
    return $lots;
}

/**
 * Returns max lot bid from MySQL database / Возвращает из БД макс. ставку по лоту
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $lot_id - Lot id / id лота
 * @return array|null - Max lot bid
 */
function get_max_bid($link, $lot_id)
{
    $sql = "SELECT amount FROM bets WHERE lot_id = ? ORDER BY amount DESC LIMIT 1;";
    $max_bid = db_fetch_data_1($link, $sql, [$lot_id]);
    return $max_bid;
}

/**
 * Counts lots by category / Считает количество лотов в категории
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $category_id - Category id / id категории
 * @return array|null - Количество лотов в категории / Number of lots by category
 */
function count_lots_by_cat($link, $category_id)
{
    $sql = "SELECT COUNT(*) AS cnt FROM lots WHERE category_id = ? AND dt_end > now();";
    $result = db_fetch_data_1($link, $sql, [$category_id]);
    return $result;
}

/**
 * Counts lots by search / Считает количество лотов по результатам поиска
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $search string - Search query / Поисковой запрос
 * @return array|null - Количество лотов по результатам поиска / Number of lots by search
 */
function count_lots_by_search($link, $search)
{
    $sql = "SELECT COUNT(*) AS cnt FROM lots 
    WHERE dt_end > now() AND match(name, description) against(?);";
    $result = db_fetch_data_1($link, $sql, [$search]);
    return $result;
}

/**
 * Returns lots by category / Возвращает лоты по категории
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $category_id - Category id / id категории
 * @param $page_items - Number of lots per page / Количество лотов на странице
 * @param $offset - Смещение выборки
 * @return array|null - Лоты в выбранной категории / Lots in the chosen category
 */
function get_lots_by_cat($link, $category_id, $page_items, $offset)
{
    $sql = "SELECT l.id AS id, start_price, l.name AS name, image, c.name AS category, 
       UNIX_TIMESTAMP(l.dt_add) AS dt_add, description, dt_end FROM lots l
    JOIN categories c 
      ON l.category_id = c.id
    WHERE category_id = ? AND dt_end > now()
    ORDER BY l.id DESC
    LIMIT ? OFFSET ?;";
    $lots = db_fetch_data($link, $sql, [$category_id, $page_items, $offset]);
    return $lots;
}

/**
 * Returns lots by search / Полнотекстовый поиск лотов по описанию и по названию
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $search string - Search query / Поисковой запрос
 * @param $page_items - Number of lots per page / Количество лотов на странице
 * @param $offset - Смещение выборки
 * @return array|null - Lots by search / Лоты по результатам поиска
 */
function search_lot($link, $search, $page_items, $offset)
{
    $sql = "SELECT 
    l.id AS id, start_price, l.name AS name, image, c.name AS category, UNIX_TIMESTAMP(l.dt_add) AS dt_add, description
    FROM lots l
    JOIN categories c
     ON l.category_id = c.id
    WHERE dt_end > now() AND match(l.name, description) against(?)
    ORDER BY l.id DESC
    LIMIT ? OFFSET ?;";
    $lots = db_fetch_data($link, $sql, [$search, $page_items, $offset]);
    return $lots;
}

/**
 * Returns lot data by id / Возвращает данные лота по id
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $lot_id - Lot id / id лота
 * @return array|null - Lot data from MySQL database as associative one-dimensional array
 */
function get_lot($link, $lot_id)
{
    $sql = "SELECT
    l.id AS id, start_price, l.name AS name, image, c.name AS category, UNIX_TIMESTAMP(l.dt_add) AS dt_add, 
       description, bet_step, dt_end, user_id
    FROM lots l
    JOIN categories c
     ON l.category_id = c.id
    WHERE l.id = ?;";
    $lot = db_fetch_data_1($link, $sql, [$lot_id]);
    return $lot;
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

/**
 * Checks date to be in format «dd.mm.yyyy» / Проверяет дату на соответствие формату «ДД.ММ.ГГГГ»
 *
 * @param $date - Данные для проверки
 * @return bool - Returns "true" if date is in format «dd.mm.yyyy» / Возвращает "true", если дата соответствует формату «ДД.ММ.ГГГГ»
 */
function check_date_format($date)
{
    $result = false;
    $regexp = '/(\d{2})\.(\d{2})\.(\d{4})/m';
    if (preg_match($regexp, $date, $parts) && count($parts) === 4) {
        $result = checkdate($parts[2], $parts[1], $parts[3]);
    }
    return $result;
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
 * Returns all lot bids / Возвращает все ставки по лоту
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $lot_id - Lot id / id лота
 * @return array|null - Lot bids / Ставки по лоту
 */
function get_bets($link, $lot_id)
{
    $sql = "SELECT 
    b.id, b.dt_add AS dt_add, amount, u.name AS name
    FROM bets b
    JOIN users u
     ON b.user_id = u.id
    WHERE lot_id = ?
    ORDER BY b.id DESC;";
    $bets = db_fetch_data($link, $sql, [$lot_id]);
    return $bets;
}

/**
 * Returns all user bids by lot id / Возвращает ставки юзера по id лота
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $lot_id - Lot id / id лота
 * @param $user_id - User id / id юзера
 * @return array|null - Ставки юзера по лоту
 */
function get_user_bets($link, $lot_id, $user_id)
{
    $sql = "SELECT 
    b.id
    FROM bets b
    WHERE lot_id = ? AND user_id = ?;";
    $bets = db_fetch_data($link, $sql, [$lot_id, $user_id]);
    return $bets;
}

/**
 * Returns all user bids / Возвращает все ставки юзера
 *
 * @param $link mysqli - Connection to a MySQL database server / Ресурс соединения
 * @param $user_id - User id / id юзера
 * @return array|null - Array with all user's bids
 */
function get_my_rates($link, $user_id)
{
    $sql = "SELECT b.dt_add, amount, b.user_id, l.name AS lot_name, c.name AS category_name, message, l.image, 
       l.id AS lot_id, l.dt_end, winner_id
    FROM bets b
    JOIN lots l
     ON b.lot_id = l.id
    JOIN categories c
     ON l.category_id = c.id
    JOIN users u
     ON l.user_id = u.id
    WHERE b.user_id = ?;";
    $rates = db_fetch_data($link, $sql, [$user_id]);
    return $rates;
}

/**
 * Возвращает сколько дней, часов и минут осталось до окончания торгов по лоту
 *
 * @param $end_date - Дата окончания торгов по лоту
 * @return string - Сколько дней, часов и минут осталось до окончания торгов по лоту
 */
function time_left($end_date)
{
    $cur_date = date_create("now");
    $dt_end = date_create($end_date);
    $diff = date_diff($cur_date, $dt_end);
    $days_count = date_interval_format($diff, "%a");
    $hours_count = date_interval_format($diff, "%h");
    $minutes_count = date_interval_format($diff, "%i");
    $result = "$days_count" . " дн. " . "$hours_count" . " ч. " . "$minutes_count" . " мин. ";
    return $result;
}

/**
 * Возвращает сколько дней или часов и минут осталось до окончания торгов по лоту
 *
 * @param $end_date - Дата окончания торгов по лоту
 * @return DateInterval|string - Сколько дней или часов и минут осталось до окончания торгов по лоту
 */
function time_left_short($end_date)
{
    $cur_date = date_create("now");
    $dt_end = date_create($end_date);
    $diff = date_diff($cur_date, $dt_end);
    $days_count = date_interval_format($diff, "%a");
    if ($days_count) {
        $result = "$days_count" . " дн. ";
    } else {
        $result = date_interval_format($diff, "%H:%i");
    }
    return $result;
}

/**
 * Возвращает как давно была сделана запись
 *
 * @param $add_date - Дата добавления записи
 * @return false|string - Как давно была сделана запись
 */
function time_ago($add_date)
{
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
    return $result;
}
