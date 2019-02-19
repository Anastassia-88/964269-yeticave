<?php
$is_auth = rand(0, 1);
$user_name = 'Anastassia';
require_once 'init.php';
require_once 'functions.php';

// SQL-запрос для получения списка новых лотов
$sql = "
select l.name, start_price, image, max(amount) as price, c.name
from lots l
join categories c
on l.category_id = c.id
left join bets b
on b.lot_id = l.id
where winner_id is null
group by l.id
order by l.id desc;
";
$data = ["", "", ""];
db_fetch_data($link, $sql);

// SQL-запрос для получения списка категорий
$sql = "select 
select *
from categories;
";
$data = ["", "", ""];
db_fetch_data($link, $sql);

// добавление новой записи в таблицу лотов
$sql = "
insert into lots
(name, start_price, user_id, category_id)
values
(?, ?, ?, ?)
";
$data = ["Очки", "500", "2", "7"];
db_insert_data($link, $sql, $data = []);

$sql = "
insert into lots
(name, start_price, user_id, category_id)
values
(?, ?, ?, ?)
";
$data = ["Перчатки", "400", "2", "8"];
db_insert_data($link, $sql, $data = []);

// добавление новой записи в таблицу категорий
$sql = "
insert into categories 
(name)
values
(?), (?) 
";
$data = ["Очки", "Перчатки"];
db_insert_data($link, $sql, $data = []);

$page_content = include_template('index.php', ['lots' => $lots, 'categories' => $categories]);
$layout_content = include_template('layout.php',
    ['content' => $page_content, 'categories' => $categories, 'title' => 'Главная']);

print($layout_content);