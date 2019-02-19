<?php
$is_auth = rand(0, 1);
$user_name = 'Anastassia';
require_once 'init.php';
require_once 'functions.php';

// добавление новой записи в таблицу категорий
$sql = "insert into categories (name) values (?), (?)";
$data = ["Очки", "Перчатки"];
db_insert_data($link, $sql, $data = []);

// добавление новой записи в таблицу лотов
$sql = "insert into lots (name, start_price, user_id, category_id)
values (?, ?, ?, ?)";
$data = ['Лыжные очки', 500, 2, 7];
db_insert_data($link, $sql, $data = []);

// добавление новой записи в таблицу лотов
$sql = "insert into lots
(name, start_price, user_id, category_id)
values
(?, ?, ?, ?)";
$data = ['Перчатки лыжные', 400, 2, 8];
db_insert_data($link, $sql, $data = []);

// SQL-запрос для получения списка новых лотов
$sql = "
select start_price, l.name as name, image, c.name as category
from lots l
join categories c
on l.category_id = c.id
left join bets b
on b.lot_id = l.id
where winner_id is null
group by l.id
order by l.id desc;
";
$lots = db_fetch_data($link, $sql);
var_dump($lots);

// SQL-запрос для получения списка категорий
$sql = "
select *
from categories;
";
$categories = db_fetch_data($link, $sql);
var_dump($categories);

$page_content = include_template('index.php', ['lots' => $lots, 'categories' => $categories]);
$layout_content = include_template('layout.php',
    ['content' => $page_content, 'categories' => $categories, 'title' => 'Главная']);

print($layout_content);