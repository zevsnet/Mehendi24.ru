<?php
namespace SB;

class General
{

    CONST IBLOCK_COMPANY_ID = 28;
    /**
     * @var Partner
     */
    static $Partner = null;

    static function getSubDomain($removeWWW = true)
    {
        if($removeWWW)
            $arSearch[] = 'www';

        $arSearch[] = SITE_SERVER_NAME != null ? SITE_SERVER_NAME : $_SERVER['SERVER_NAME'];

        return trim(str_replace($arSearch, '', $_SERVER['SERVER_NAME']), '.');
    }

    static function decodeAlphabet($arAlphabet, $value)
    {
        $res = Tools::decToAlphabet($arAlphabet, $value);
        if(strlen($res) < 2)
            $res = $arAlphabet[0] . $res;

        if(strlen($res) != 2)
            throw new \Exception('Переполнение формированного SITE_ID');

        return $res;
    }

    static function encodeAlphabet($arAlphabet, $value)
    {
        return Tools::convertToDec($arAlphabet, $value);
    }

    /**
     * Разбивает входную строку и возвращает нужный кусок строки
     *
     * @param $delimiter
     * @param $string
     * @param $necessaryPart
     *
     * @return mixed
     */
    static function exploder($delimiter, $string, $necessaryPart)
    {
        $arRes = explode($delimiter, $string);

        return $arRes[$necessaryPart];
    }

    static function sortBySort($a, $b)
    {
        if($a['UF_SORT'] == $b['UF_SORT'])
            return 0;

        return $a['UF_SORT'] > $b['UF_SORT'] ? 1 : -1;
    }

    /**
     * проверяет активен ли выбранный раздел по коду, на основном доммене всегда возвращает try
     * @param $code
     *
     * @return bool
     * @throws \Exception
     */
    static function checkActivitySection($code)
    {
        if(!$code)
            throw new \Exception('invalid CODE');

        if(!General::$Partner)
            return true;

        return (bool)General::$Partner->getSetting($code)->getValue();
    }

    /**
     * проверяет активность раздела новостей
     * @return bool
     */
    static function checkActivityNews()
    {
        return self::checkActivitySection('news');
    }

    /**
     * проверяет активность раздела магазинов
     * @return bool
     */
    static function checkActivityStores()
    {
        return self::checkActivitySection('stores');
    }

    static function checkActivityCompany()
    {
        return self::checkActivitySection('about_company');
    }

    /**
     * проверяет активность раздела в котором находимся
     * @return bool
     */
    static function checkCategory()
    {
        if(!General::$Partner)
            return true;

        $url = $_SERVER['REAL_FILE_PATH'] ? $_SERVER['REAL_FILE_PATH'] : $_SERVER['PHP_SELF'];

        if($code = CIBlockPropertySettings::$arConfig[str_replace('index.php', '', $url)])
        {
            if(!self::checkActivitySection($code))
            {
                LocalRedirect('/');
                die;
            }
        }

        return true;
    }

    /**
     * исключает из массива разделов не активные разделы, используется для файлов меню
     * @param $arCategories
     */
    static function excludeCategories(&$arCategories)
    {
        if(!General::$Partner)
            return;

        foreach($arCategories as $key => $arCategory)
        {
            if($code = CIBlockPropertySettings::$arConfig[str_replace('index.php', '', $arCategory[1])])
            {
                if(!self::checkActivitySection($code))
                    unset($arCategories[$key]);
            }
        }
    }

    static function getSiteId()
    {
        return General::$Partner ? General::$Partner->getSiteId() : SITE_ID;
    }

    static function addPartnerFilter(&$arFilter)
    {
        $arFilter[] = [
            "LOGIC" => "OR",
            array("PROPERTY_FOR_ALL_VALUE" => 'Y'),
            array("PROPERTY_LINK" => self::$Partner ? self::$Partner->getId() : false),
        ];
    }
}