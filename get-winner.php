<?php
require_once 'init.php';
require_once 'functions.php';
require_once 'vendor/autoload.php';

// Retrieve closed lots without winner
$sql = "SELECT id, name from lots where dt_end <= now() and winner_id is null;";
$closed_lots_without_winner = db_fetch_data($link, $sql);

if ($closed_lots_without_winner) {
    foreach ($closed_lots_without_winner as $lot) {
        $sql = "
        SELECT amount as max_rate, user_id as winner_id, name as winner_name, email
        FROM bets b
        JOIN users u
          ON b.user_id = u.id
        WHERE lot_id = ?
        ORDER BY amount DESC 
        LIMIT 1;";
        $winner_data = db_fetch_data_1($link, $sql, [$lot['id']]);

        if (!empty($winner_data)) {
            $sql = "UPDATE lots SET winner_id = ? where id = ?;";
            db_insert_data($link, $sql, [$winner_data['winner_id'], $lot['id']]);


            // E-Mail notification to the winner

            // Сообщения электронной почты отправляются по протоколу SMTP
            //Поэтому нам понадобятся данные для доступа к SMTP-серверу
            //Указываем его адрес и логин с паролем
            $transport = new Swift_SmtpTransport('phpdemo.ru', 25);
            $transport->setUsername('keks@phpdemo.ru');
            $transport->setPassword('htmlacademy');

            // Создадим главный объект библиотеки SwiftMailer, ответственный за отправку сообщений
            //Передадим туда созданный объект с SMTP-сервером
            $mailer = new Swift_Mailer($transport);

            // Чтобы иметь максимально подробную информацию о процессе отправки сообщений
            // мы попросим SwiftMailer журналировать все происходящее внутри массива
            $logger = new Swift_Plugins_Loggers_ArrayLogger();
            $mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

            // Message options
            $message = new Swift_Message();
            $message->setSubject("Ваша ставка победила");
            $message->setFrom(['keks@phpdemo.ru' => 'Аукцион YetiCave']);
            $message->setTo([$winner_data['email'] => $winner_data['winner_name']]);

            // Message
            $msg_content = include_template('email.php', [
                'lot_name' => $lot['name'],
                'lot_id' => $lot['id'],
                'winner_name' => $winner_data['winner_name']
            ]);

            $message->setBody($msg_content, 'text/html');

            $result = $mailer->send($message);

            if ($result) {
                print("Рассылка успешно отправлена");
            } else {
                print("Не удалось отправить рассылку: " . $logger->dump());
            }
        }
    }
}
