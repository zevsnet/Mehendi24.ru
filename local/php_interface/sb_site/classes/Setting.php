<?php
/**
 * Created by PhpStorm.
 * User: dnkolosov
 * Date: 08.12.2016
 * Time: 16:13
 */

namespace SB;

class Setting
{
    protected $arData = [];

    function __construct($arData, $arParams = [])
    {
        $this->arData = $arData;
    }

    public function getData($key, $throwException = true)
    {
        if (!array_key_exists($key, $this->arData) && $throwException)
            throw new \InvalidArgumentException("key \"{$key}\" does not exist in arData");

        if ($key !== false)
            return $this->arData[$key];

        return $this->arData;
    }

    public function getValue()
    {
        return $this->arData['value'];
    }
}