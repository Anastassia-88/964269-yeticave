-- создаем базу данных
create database yeticave
  default character set utf8
  default collate utf8_general_ci;

-- создаем таблицы на каждую сущность

create table categories (
                          id int auto_increment primary key, -- первичный ключ
                          name char(128) not null -- название
);

create table lots (
                    id int auto_increment primary key, -- первичный ключ
                    dt_add timestamp default current_timestamp, -- дата создания
                    name char(128) not null, -- название
                    description text(280), -- описание
                    image text, -- изображение
                    start_price decimal not null, -- начальная цена
                    dt_end timestamp, -- дата завершения
                    bet_step decimal not null, -- шаг ставки
                    user_id int not null, -- автор, связь с таблицей users
                    category_id tinyint not null, -- категория, связь с таблицей categories
                    winner_id int -- победитель, связь с таблицей users
);

create table bets (
                    id int auto_increment primary key, -- первичный ключ
                    dt timestamp, -- дата
                    amount decimal not null, -- сумма
                    user_id int not null, -- пользователь, связь с таблицей users
                    lot_id tinyint not null -- лот, связь с таблицей lots
);

create table users (
                     id int auto_increment primary key, -- первичный ключ
                     dt_registration timestamp default current_timestamp,
                     email char(128) not null unique, -- email
                     name char not null unique, -- имя
                     password char(64) not null, -- пароль
                     avatar text, -- аватар
                     contacts text not null, -- контакты
                     lots_id char, -- созданные лоты, связь с таблицей lots
                     bets_id char -- ставки, связь с таблицей bets
);

-- заполняем таблицы
insert into categories (name)
values ('Доски и лыжи'), ('Крепления'), ('Ботинки'), ('Одежда'), ('Инструменты'), ('Разное');

-- добавляем уникальные индексы полям, где должны быть только уникальные значения
create unique index u_email on users(email);
create unique index u_name on users(name);

-- добавляем обычные индексы полям, по которым будет происходить поиск
create index l_name on lots(name);
create index l_category_id on lots(category_id);

