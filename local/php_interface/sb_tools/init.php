<?php
namespace SB;

define('SB\ROOT', $_SERVER['DOCUMENT_ROOT']);
define('SB\PHP_INTERFACE_PATH', ROOT . '/bitrix/php_interface');

include('classes/debugger/debugger.php');

// Подключаем автозагрузчик классов
spl_autoload_register(function ($class)
{
    if (($pos = strpos($class, __NAMESPACE__.'\\')) !== false)
    {
        $arClass = explode('\\', trim($class, '\\'));
        array_shift($arClass);
        $className = array_pop($arClass);

        $dir = '';

        if ($arClass)
            $dir = implode(DIRECTORY_SEPARATOR, $arClass) . DIRECTORY_SEPARATOR;

        $filePath = __DIR__ . '/' .'classes/' . $dir . $className . '.php';

        if (file_exists($filePath))
        {
            include $filePath;
        }
    }
});