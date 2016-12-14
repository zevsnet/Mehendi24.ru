<?php
namespace SB;

define('SB\ROOT', $_SERVER['DOCUMENT_ROOT']);
define('SB\PHP_INTERFACE_PATH', ROOT . '/bitrix/php_interface');

include('classes/debugger/debugger.php');

// Подключаем автозагрузчик классов
spl_autoload_register(function ($class)
{
    if (($pos = strpos($class,'SB\\')) !== false)
    {
        $len = strlen($class);
        $className =  substr($class, $pos+3, $len);
        
        $filePath = __DIR__ . '/' .'classes/' . $className . '.php';

        if (file_exists($filePath))
            include $filePath;
    }
});