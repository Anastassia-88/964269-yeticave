<?php
session_start();
$user_name = $_SESSION['user']['name'] ?? '';
$user_id = $_SESSION['user']['id'] ?? '';

// date_default_timezone_set('Europe/Berlin');
// Подключение базы данных
$link = mysqli_connect("localhost", "root", "", "yeticave");
// Сообщение при ошиибке подключения;
if ($link == false) {
    print("Ошибка подключения: " . mysqli_connect_error());
}
// Устанавливаем кодировку в utf8
mysqli_set_charset($link, "utf8");