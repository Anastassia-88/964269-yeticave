<?php
$is_auth = rand(0, 1);
$user_name = 'Anastassia'; // укажите здесь ваше имя
$categories = ["Доски и лыжи", "Крепления", "Ботинки", "Одежда", "Инструменты", "Разное"];
$goods = [
    [
        'name' => '2014 Rossignol District Snowboard',
        'category' => 'Доски и лыжи',
        'price' => '10999',
        'image' => 'img/lot-1.jpg',
        'current_time' => strtotime(now) // время публикации лота
    ],
    [
        'name' => 'DC Ply Mens 2016/2017 Snowboard',
        'category' => 'Доски и лыжи',
        'price' => '159999',
        'image' => 'img/lot-2.jpg',
        'current_time' => strtotime(now) // время публикации лота
    ],
    [
        'name' => 'Крепления Union Contact Pro 2015 года размер L/XL',
        'category' => 'Крепления',
        'price' => '8000',
        'image' => 'img/lot-3.jpg',
        'current_time' => strtotime(now) // время публикации лота
    ],
    [
        'name' => 'Ботинки для сноуборда DC Mutiny Charocal',
        'category' => 'Ботинки',
        'price' => '10999',
        'image' => 'img/lot-4.jpg',
        'current_time' => strtotime(now) // время публикации лота
    ],
    [
        'name' => 'Куртка для сноуборда DC Mutiny Charocal',
        'category' => 'Одежда',
        'price' => '7500',
        'image' => 'img/lot-5.jpg',
        'current_time' => strtotime(now) // время публикации лота
    ],
    [
        'name' => 'Маска Oakley Canopy',
        'category' => 'Разное',
        'price' => '5400',
        'image' => 'img/lot-6.jpg',
        'current_time' => strtotime(now) // время публикации лота
    ]
];

require('functions.php');
$page_content = include_template('index.php', ['goods' => $goods, 'categories' => $categories]);
$layout_content = include_template('layout.php',
    ['content' => $page_content, 'categories' => $categories, 'title' => 'Главная']);

print($layout_content);