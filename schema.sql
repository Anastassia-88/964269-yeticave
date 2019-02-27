-- создаем базу данных
create database yeticave
                          default character set utf8
                          default collate utf8_general_ci;

-- делаем базу данных активной
use yeticave;

-- создаем таблицы на каждую сущность

create table categories (
                          id int auto_increment primary key, -- первичный ключ
                          name char(255) not null -- название
);

create table lots (
                          id int auto_increment primary key, -- первичный ключ
                          dt_add timestamp default current_timestamp, -- дата создания
                          name char(255) not null, -- название
                          description text not null, -- описание
                          image char(255) not null, -- изображение
                          start_price decimal not null, -- начальная цена
                          dt_end timestamp not null, -- дата завершения
                          bet_step decimal not null default 100, -- шаг ставки
                          user_id int not null, -- автор, связь с таблицей users
                          category_id int not null, -- категория, связь с таблицей categories
                          winner_id int -- победитель, связь с таблицей users
);

create table bets (
                          id int auto_increment primary key, -- первичный ключ
                          dt_add timestamp default current_timestamp, -- дата
                          amount decimal not null, -- сумма
                          user_id int not null, -- пользователь, связь с таблицей users
                          lot_id int not null -- лот, связь с таблицей lots
);

create table users (
                          id int auto_increment primary key, -- первичный ключ
                          dt_add timestamp default current_timestamp, -- дата регистрации
                          email char(255) not null unique, -- email
                          name char(255) not null, -- имя
                          password char(255) not null, -- пароль
                          image text, -- аватар
                          message text not null -- контакты
);

-- добавляем уникальные индексы полям, где должны быть только уникальные значения
create unique index u_email on users(email);

-- добавляем обычные индексы полям, по которым будет происходить поиск
create index l_name on lots(name);
create index l_user on lots(user_id);
create index l_category_id on lots(category_id);
create index l_winner on lots(winner_id);
create index b_user on bets(user_id);
create index b_lot on bets(lot_id);




