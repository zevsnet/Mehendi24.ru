<?php
namespace SB;

class Validator
{
    static public $messages;

    static function init()
    {
        self::$messages = array(
            'email'             => 'Не корректный email адрес (%s)',
            'required'          => 'Не должно быть пустым',
            'fileExtension'     => 'Не допустимое расширение файла',
        );
    }

    static function email($value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL))
        {
            return sprintf(self::$messages['email'], $value);
        }

        return false;
    }

    static function required($value)
    {

        if (empty($value))
        {
            return self::$messages['required'];
        }

        return false;
    }

    static function fileExtention($file, $arExtentions)
    {
        $arTmp = explode('.', $file);

        $ext = array_pop($arTmp);

        if ($arExtentions && array_pop($arTmp) && in_array($ext, $arExtentions))
        {
            return false;
        }

        return self::$messages['fileExtention'];
    }
}

Validator::init();