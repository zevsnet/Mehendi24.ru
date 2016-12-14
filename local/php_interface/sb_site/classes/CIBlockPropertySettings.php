<?php
/**
 * Created by PhpStorm.
 * User: viktor
 * Date: 29.11.2016
 * Time: 15:45
 */

namespace SB;

class CIBlockPropertySettings
{
    static $arSettings = [
        'amenities'     => [
            'name'  => 'amenities',
            'text'  => 'Раздел "Услуги":',
            'type'  => 'bool',
            'value' => true
        ],
        'news'          => [
            'name'  => 'news',
            'text'  => 'Раздел "Новости":',
            'type'  => 'bool',
            'value' => true
        ],
        'about_company' => [
            'name'  => 'about_company',
            'text'  => 'Раздел "О компании":',
            'type'  => 'bool',
            'value' => true
        ],
        'stores'        => [
            'name'  => 'stores',
            'text'  => 'Раздел "Магазины":',
            'type'  => 'bool',
            'value' => true
        ],
        'staff'         => [
            'name'  => 'staff',
            'text'  => 'Раздел "Сотрудники":',
            'type'  => 'bool',
            'value' => true
        ],
        'jobs'          => [
            'name'  => 'jobs',
            'text'  => 'Раздел "Вакансии":',
            'type'  => 'bool',
            'value' => true
        ],
        'color_scheme'  => [
            'name'   => 'color_scheme',
            'text'   => 'Цветовая схема:',
            'type'   => 'string',
            'value'  => 'BLUE',
            'values' => false
        ]
    ];

    static $arConfig = [
        '/services/'        => 'amenities',
        '/company/news/'    => 'news',
        '/company/'         => 'about_company',
        '/contacts/stores/' => 'stores',
        '/company/staff/'   => 'staff',
        '/company/jobs/'    => 'jobs'
    ];

    static $arAlphabet = [/*'0,'*/'1','2','3','4','5','6','7','8','9','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','t','u','v','w','x','y','z'];

    static function GetUserTypeDescription()
    {
        $class = __CLASS__;

        return array(
            "PROPERTY_TYPE"        => "S",
            "USER_TYPE"            => "Settings",
            "DESCRIPTION"          => "Настройки",
            "GetPropertyFieldHtml" => array($class, "GetPropertyFieldHtml"),
            "GetPublicViewHTML"    => array($class, "GetPublicViewHTML"),
            "ConvertToDB"          => array($class, "ConvertToDB"),
            "ConvertFromDB"        => array($class, "ConvertFromDB"),
            "GetAdminListViewHTML" => array($class, "GetAdminListViewHTML")
        );
    }

    static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {

        if($strHTMLControlName['MODE'] == 'FORM_FILL')
        {
            $arRes = \CMShop::getModuleOptionsList()['TEMPLATE_OPTIONS'];
            $arColorTheme = false;
            foreach($arRes as $arProp)
            {
                if($arProp['ID'] == 'COLOR_THEME')
                    $arColorTheme = $arProp['VALUES'];
            }

            $arColorTheme = Tools::getList($arColorTheme, 'VALUE', 'NAME');

            $html = '';
            foreach(self::$arSettings as $key => &$setting)
            {
                if($setting['name'] == 'color_scheme')
                    $setting['values'] = $arColorTheme;

                if(array_key_exists($setting['name'], $value['VALUE']))
                    $setting['value'] = $value['VALUE'][$setting['name']];

                $attrs = [
                    'name'  => $strHTMLControlName["VALUE"] . "[{$setting['name']}]",
                    'value' => $setting['value'],
                    'type'  => $setting['type'] == 'bool' ? 'checkbox' : 'text',
                ];
                if($setting['type'] == 'bool' && $setting['value'])
                    $attrs['checked'] = 'checked';

                $text = $setting['name'];

                if (!empty($setting['text']))
                    $text = $setting['text'];

                $html .= Html::tag('label', $text);
                $html .= $setting['type'] == 'bool' ? Html::tag('input', false, $attrs, false) : Html::select($arColorTheme, ['name' => $strHTMLControlName["VALUE"] . "[{$setting['name']}]"], $setting['value']);
                $html .= Html::tag('br') . Html::tag('br');
            }

            return $html;
        }
        elseif($strHTMLControlName['MODE'] == 'iblock_element_admin')
        {
            $attrs = [
                'name'  => $strHTMLControlName["VALUE"],
                'value' => implode(',', $value['VALUE'])
            ];

            return Html::tag('input', false, $attrs, false);
        }
    }

    static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        if($value["VALUE"])
            return implode(',', $value["VALUE"]);
    }

    static function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
    {
        if(strlen($value["VALUE"]) > 0)
            return str_replace(" ", "&nbsp;", htmlspecialcharsex($value["VALUE"]));
        else
            return '';
    }


    static function ConvertFromDB($arProperty, $value)
    {
        if(isset($value["VALUE"]))
        {
            $value["VALUE"] = unserialize($value["VALUE"]);
        }

        return $value;
    }

    static function ConvertToDB($arProperty, $value)
    {
        if(isset($value["VALUE"]))
        {
            if(!is_array($value["VALUE"]))
            {
                $value["VALUE"] = explode(',', $value["VALUE"]);
            }

            foreach(self::$arSettings as $arSetting)
            {
                $name = $arSetting['name'];

                if (!array_key_exists($name, $value["VALUE"]))
                {
                    $value["VALUE"][$name] = false;
                }
                else
                {
                    if ($arSetting['type'] == 'bool')
                    {
                        $value["VALUE"][$name] = true;
                    }
                }
            }

            $value["VALUE"] = serialize($value["VALUE"]);
        }

        return $value;
    }
}