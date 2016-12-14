<?php
/**
 * Created by PhpStorm.
 * User: dnkolosov
 * Date: 07.12.2016
 * Time: 11:44
 */

namespace SB;

/**
 * содержит настройки поддомена, через getSetting получаются параметры, первый раз выбираются из базы
 * Class Partner
 * @package SB
 */
class Partner
{
    const IBLOCK_ID = 20;

    static public $arSelect = [
        'ID',
        'PROPERTY_INN',
        'NAME',
        'PROPERTY_SITE_ID',
        'PROPERTY_SETTINGS'
    ];


    protected $arData = [];


    function __construct($arData, $arParams = [])
    {
        $this->arData = $arData;
    }

    static public $arFilter = [
        "ACTIVE"      => "Y",
        "ACTIVE_DATE" => "Y",
        "IBLOCK_ID"   => Order::IBLOCK_ID,
    ];

    static public function getById($id)
    {
        if (!$id)
            throw new \InvalidArgumentException('Invalid ID');

        $arFilter = ['ID'=>$id];

        return new self(IBlock::getElement($arFilter, self::$arSelect));
    }


    static public function getByCode($code)
    {
        if (!$code)
            throw new \InvalidArgumentException('Invalid CODE');

        $arFilter = ['CODE'=>$code, 'IBLOCK_ID'=>self::IBLOCK_ID];

        return new self(IBlock::getElement($arFilter, self::$arSelect));
    }

    static public function getByFilter($arFilter)
    {
        $arFilter = self::$arFilter + $arFilter;


        $arElements = IBlock::getElements([], $arFilter, false, false, self::$arSelect);

        $arPartners = [];
        foreach($arElements as $arElement)
        {
            $arPartners[] = new self($arElement);
        }

        return $arPartners;
    }

    public function getData($key, $throwException = true)
    {
        if (!array_key_exists($key, $this->arData) && $throwException)
            throw new \InvalidArgumentException("key \"{$key}\" does not exist in arData");

        if ($key !== false)
            return $this->arData[$key];

        return $this->arData;
    }

    /**
     * @param $code
     *
     * @return Setting
     */
    public function getSetting($code)
    {
        $arSettingValues = $this->getData('PROPERTY_SETTINGS_VALUE');

        $arSetting = CIBlockPropertySettings::$arSettings[$code];

        if (!$arSetting)
            throw new \InvalidArgumentException("Invalid setting {$code}");

        if(!is_object($arSettingValues[$code]))
        {
            $arSetting['value'] = isset($arSettingValues[$code]) ? $arSettingValues[$code] : null;

            $this->arData['PROPERTY_SETTINGS_VALUE'][$code] = new Setting($arSetting);
        }

        return $this->arData['PROPERTY_SETTINGS_VALUE'][$code];
    }

    public function getSiteId()
    {
        return $this->getData('PROPERTY_SITE_ID_VALUE');
    }

    public function getId()
    {
        return $this->getData('ID');
    }
}