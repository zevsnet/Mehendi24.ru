<?php
set_time_limit(0);

// ��������� ������

$botToken = "226901967:AAGWuuwGRRu6PYeUjee9dEf2GCOXkf5jwEo";
$website = "https://api.telegram.org/bot".$botToken;

// �������� ������ �� Telegram

$content = file_get_contents("php://input");
$update = json_decode($content, TRUE);
file_get_contents("/home/bitrix/www/telegram/elegram.log",'Test '.$update);
$message = $update["message"];

// �������� ���������� ����� ���� Telegram � �������, �������� ������������� �   ����

$chatId = $message["chat"]["id"];
$text = $message["text"];

// ������ ��������� ������� /start

if ($text == '/start') {
    $welcomemessage = 'idTelegrama: '.$message["chat"]["id"];

    // ���������� �������������� ��������� ������� � Telegram ������������

    file_get_contents($website."/sendmessage?chat_id=".$chatId."&text=".$welcomemessage);
}