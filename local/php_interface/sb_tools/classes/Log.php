<?php
namespace SB;

/**
 * Класс создания логов. Работает как объект и также имеет статические методы
 *
 * Class Log
 * @package SB
 */
class Log
{
    public $filepath = '';
    public $file = null;

    function __construct($filepath, $checkExistenceDir = true, $mode='a+')
    {
        if ($checkExistenceDir)
        {
            self::checkExistenceDir($filepath);
        }

        $this->filepath = $filepath;

        $this->file = fopen($this->filepath, $mode);

        if (!$this->file)
            return null;
    }

    static function checkExistenceDir($filePath)
    {
        $arFilePath = explode(DIRECTORY_SEPARATOR, $filePath);

        array_pop($arFilePath);

        $dirPath = implode(DIRECTORY_SEPARATOR, $arFilePath);

        if (!file_exists($dirPath))
        {
            if (mkdir($dirPath, 0777, true))
                return false;
        }

        return true;
    }

    private static function makeRecord($text)
    {
        $result =
            date('Y-m-d H:i:s') . "\n".
            print_r($text, true).
            '----------' . "\n";

        return $result;
    }

    static function addToLog($filepath, $text)
    {
        $file = fopen($filepath, 'a+');
        if (!$file)
            return;
        fwrite($file, self::makeRecord($text));
        fclose($file);
    }

    function add($text)
    {
        if ($this->file)
            fwrite($this->file, self::makeRecord($text));
    }

    function printFile()
    {
        echo '<pre>';
        echo file_get_contents($this->filepath);
        echo '</pre>';
    }

    function __destruct()
    {
        if ($this->file)
            fclose($this->file);
    }
}