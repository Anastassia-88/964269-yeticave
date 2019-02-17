-- делаем базу данных активной
use yeticave;

-- заполняем таблицы
insert into categories (name)
values
('Доски и лыжи'), ('Крепления'), ('Ботинки'), ('Одежда'), ('Инструменты'), ('Разное');

insert into users (email, name, password, contacts)
values
("anastassia.russak@gmail.com", "AR", "123", "Моя почта - anastassia.russak@gmail.com"),
("oleg.russak88@gmail.com", "OR", "123", "Моя почта - oleg.russak88@gmail.com");

insert into lots (name, image, start_price, user_id, category_id)
values
("2014 Rossignol District Snowboard", "img/lot-1.jpg", 10999, 1, 1),
("DC Ply Mens 2016/2017 Snowboard", "img/lot-2.jpg", 159999, 1, 1),
("Крепления Union Contact Pro 2015 года размер L/XL", "img/lot-3.jpg", 8000, 1, 2),
("Ботинки для сноуборда DC Mutiny Charocal", "img/lot-4.jpg", 10999, 2, 3),
("Куртка для сноуборда DC Mutiny Charocal", "img/lot-5.jpg", 7500, 2, 4),
("Маска Oakley Canopy", "img/lot-6.jpg", 5400, 2, 6);

insert into bets (amount, user_id, lot_id)
values
(11100, 1, 1),
(12000, 2, 1);

-- получить все категории
select *
from categories;

-- получить самые новые, открытые лоты. Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, название категории;
select l.name, start_price, image, c.name, amount
from lots l
join bets b
on b.lot_id = l.id
join categories c
on l.category_id = c.id
where winner_id  is null
order by l.dt_add desc;

-- показать лот по его id. Получите также название категории, к которой принадлежит лот
select l.id, l.name, c.name
from lots l
join categories c
on l.category_id = c.id
where l.id = 1;

-- обновить название лота по его идентификатору;
update lots
set name = 'Rossignol District Snowboard 2014'
where id = 1;

-- получить список самых свежих ставок для лота по его идентификатору;
select *
from bets b
where lot_id = 1
order by dt_add desc;









