<?php
set_time_limit(0);

// Установка токена

$botToken = "270715108:AAG06m7OPCvVe7lbY7j9m6SGdSIou-rpW98";
$website = "https://api.telegram.org/bot" . $botToken;

// Получаем запрос от Telegram

$content = file_get_contents("php://input");
$update = json_decode($content, true);
file_put_contents("telegram.log", 'Test ' . serialize($update));
$message = $update["message"];

// Получаем внутренний номер чата Telegram и команду, введённую пользователем в   чате

$chatId = $message["chat"]["id"];
$text = $message["text"];

// Пример обработки команды /start

if($text == '/start')
{
    $welcomemessage = 'Немного тестов';

    // Отправляем сформированное сообщение обратно в Telegram пользователю

    file_get_contents($website . "/sendmessage?chat_id=" . $chatId . "&text=" . $welcomemessage);
}