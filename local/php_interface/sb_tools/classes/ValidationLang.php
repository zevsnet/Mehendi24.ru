<?php
/**
 * Created by PhpStorm.
 * User: viktor
 * Date: 31.12.2015
 * Time: 13:38
 */

namespace SB;


class ValidationLang
{
    const MODE_DEFAULT = 1;
    const MODE_NEGATIVE = 2;
    const STANDARD = 0;
    const EXTRA = 1;

    private static $arRu = array(
        'alnum'=>[
            self::MODE_DEFAULT => [
                self::STANDARD => '{{name}} должно содержать только символы (a-z), цифры (0-9)',
                self::EXTRA => '{{name}} должно содержать только символы (a-z), цифры (0-9) и {{additionalChars}}',
            ],
            self::MODE_NEGATIVE => [
                self::STANDARD => '{{name}} не должно содержать символы (a-z), цифры (0-9))',
                self::EXTRA => '{{name}} не должно содержать символы (a-z), цифры (0-9) и {{additionalChars}}',
            ],
        ],

        'numeric' => [
                self::MODE_DEFAULT => [
                    self::STANDARD => '{{name}} должно быть числом',
                ],
                self::MODE_NEGATIVE => [
                    self::STANDARD => '{{name}} не должно быть числом',
                ],
            ],
    );

    /**
     * @param $validator
     * @param bool $standard
     * @param bool $negative
     *
     * @return null
     */
    public static function getMessage($validator, $standard = true, $negative = false)
    {
        $arrayName = 'arRu';

        if ($negative)
            $mode = self::MODE_NEGATIVE;
        else
            $mode = self::MODE_DEFAULT;


        if ($standard)
            $type = self::STANDARD;
        else
            $type = self::EXTRA;

        $arValidatorMessage = \SB\Tools::get(static::$$arrayName, $validator);

        $arValidatorMessage = \SB\Tools::get($arValidatorMessage, $mode);
        $arValidatorMessage = \SB\Tools::get($arValidatorMessage, $type);


        return $arValidatorMessage;
    }

    /**
     * @param $arValidators
     *
     * @return array
     */
    public static function getMessages($arValidators)
    {
        $arErrors = array();
        foreach($arValidators as $key=>$item)
        {
            if (is_numeric($key))
            {
                $arErrors[$item] = self::getMessage($item);
            }
            else
            {
                $arErrors[$key] = $item;
            }
        }

        return $arErrors;
    }
}