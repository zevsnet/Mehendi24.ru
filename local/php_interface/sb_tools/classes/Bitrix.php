<?php
namespace SB;

use Bitrix\Main\DB\Exception;

class Bitrix
{
    /**
     * Делает дамп только для определенного пользователя
     * первый аргумент функции user_id, остальные переменыые для вывода
     *
     */
    static function userDump()
    {
        global $USER;
        $arArgs = func_get_args();
        $userId = array_shift($arArgs);

        if ($USER->getId() && $USER->getId() == $userId)
        {
            \debugger\dumper::dump($arArgs);
        }
    }


    /**
     * @return bool
     */
    public static function isHome()
    {
        $attr = Tools::getAttrUrl(0);

        if (!$attr)
            return true;

        if ($attr=='index.php')
            return true;

        return false;
    }

    /**
     * Добавляет в лог файла /upload/tmp/logs/$filename
     *
     * @param $filename - имя файла в каталоге /upload/tmp/logs/
     * @param $mData
     *
     * @throws Exception
     */
    public static function log($filename, $mData)
    {
        if (!$filename)
            throw new Exception('filename is empty');

        $logDirPath = $_SERVER['DOCUMENT_ROOT'] . '/upload/tmp/logs/';

        if (!file_exists($logDirPath))
        {
            mkdir($logDirPath, 0770, true);
        }

        $logFilePath = $logDirPath . $filename;

        \SB\Log::addToLog($logFilePath, $mData);
    }

    /**
     * Проверяет проходит ли обмен с 1с
     *
     * @return bool
     */
    static function isCMLImport()
    {
        static $isCMLImport;

        if ($isCMLImport===null)
        {
            $isCMLImport = false;
            $arDebug = debug_backtrace();

            foreach($arDebug as $arItem)
            {
                if($arItem['class'] == 'CIBlockCMLImport' && $arItem['function'] == 'ImportElements')
                {
                    $isCMLImport = true;
                    break;
                }
            }
        }

        return $isCMLImport;
    }
}