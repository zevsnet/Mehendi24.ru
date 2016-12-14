<?php
/**
 * Created by PhpStorm.
 * User: dnkolosov
 * Date: 05.12.2016
 * Time: 16:36
 */

namespace SB;

class EventHandlers
{

    static public function addEventHandlers()
    {
        AddEventHandler("iblock", "OnIBlockPropertyBuildList", array(__CLASS__, "OnIBlockPropertyBuildList"));
        AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", Array(__CLASS__, "OnBeforeIBlockElementUpdate"));
        AddEventHandler("iblock", "OnBeforeIBlockElementAdd", Array(__CLASS__, "OnBeforeIBlockElementUpdate"));
        AddEventHandler("iblock", "OnAfterIBlockElementUpdate", Array(__CLASS__, "OnAfterIBlockElementUpdate"));
        AddEventHandler("iblock", "OnAfterIBlockElementAdd", Array(__CLASS__, "OnAfterIBlockElementUpdate"));
        AddEventHandler("sale", "OnSaleComponentOrderProperties", Array(__CLASS__, "OnSaleComponentOrderProperties"));
        AddEventHandler("main", "OnBeforeProlog", Array(__CLASS__, "OnBeforeProlog"));
        AddEventHandler("catalog", "OnSuccessCatalogImport1C", Array(__CLASS__, "OnSuccessCatalogImport1C"));

        AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", Array(__CLASS__, "OnBeforeIBlockElementUpdateImport"));
        AddEventHandler("iblock", "OnBeforeIBlockElementAdd", Array(__CLASS__, "OnBeforeIBlockElementUpdateImport"));

        AddEventHandler("catalog", "OnPriceUpdate", Array(__CLASS__, "OnPriceUpdate"));
        AddEventHandler("catalog", "OnPriceAdd", Array(__CLASS__, "OnPriceUpdate"));
    }

    static public function OnIBlockPropertyBuildList()
    {
        return CIBlockPropertySettings::GetUserTypeDescription();
    }

    static public function OnBeforeIBlockElementUpdate(&$arFields)
    {
        if($arFields['IBLOCK_ID'] != Order::IBLOCK_ID)
            return;

        $properties = \CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>Order::IBLOCK_ID, 'CODE' => 'SITE_ID'));
        if ($prop_fields = $properties->GetNext())
        {
            foreach($arFields['PROPERTY_VALUES'][$prop_fields['ID']] as &$value)
            {
                $lastID = \COption::GetOptionString(\CMShop::moduleID, 'LAST_PARTNER_ID', '0');

                $lastID = General::encodeAlphabet(CIBlockPropertySettings::$arAlphabet, $lastID);

                $lastID = General::decodeAlphabet(CIBlockPropertySettings::$arAlphabet, $lastID + 1);

                if(empty($value['VALUE']))
                {
                    \COption::SetOptionString(\CMShop::moduleID, "LAST_PARTNER_ID", $lastID);
                }

                $value['VALUE'] = empty($value['VALUE']) ? $lastID  : $value['VALUE'];
            }
        }
    }

    static public function OnAfterIBlockElementUpdate(&$arFields)
    {

        if($arFields['IBLOCK_ID'] != Order::IBLOCK_ID)
            return;


        $siteId = false;

        if($prop_fields = \CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>Order::IBLOCK_ID, 'CODE' => 'SITE_ID'))->GetNext())
        {
            foreach($arFields['PROPERTY_VALUES'][$prop_fields['ID']] as $value)
            {
                $siteId = $value['VALUE'];
            }
        }

        if($prop_fields = \CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>Order::IBLOCK_ID, 'CODE' => 'SETTINGS'))->GetNext())
        {
            foreach($arFields['PROPERTY_VALUES'][$prop_fields['ID']] as $values)
            {
                foreach($values['VALUE'] as $key => $value)
                {
                    if($key == 'color_scheme')
                    {
                        \COption::SetOptionString(\CMShop::moduleID, "COLOR_THEME", $value, false, $siteId);
                    }
                }
            }
        }

        $arProp = \CIBlockElement::GetProperty($arFields['IBLOCK_ID'], $arFields['ID'], array("sort" => "asc"), Array("CODE"=>"LOGO_IMAGE"))->Fetch();
        if($arProp)
            \COption::SetOptionString(\CMShop::moduleID, "LOGO_IMAGE", serialize($arProp['VALUE'] ? $arProp['VALUE'] : []), false, $siteId);

        \COption::SetOptionString(\CMShop::moduleID, "HEAD", 'TYPE_3', false, $siteId);
        \COption::SetOptionString(\CMShop::moduleID, "VIEWED_TYPE", 'BX', false, $siteId);
    }

    static public function OnSaleComponentOrderProperties(&$arUserResult, $request, &$arParams, &$arResult)
    {
        // получение ид свойства в которое необходимо записать инн магазина
        $db_props = \CSaleOrderProps::GetList(array("SORT" => "ASC"), array(
            "PERSON_TYPE_ID" => $arUserResult['PERSON_TYPE_ID'],
            "UTIL"           => "Y",
            "CODE"           => Order::INN_ORDER_CODE
        ), false, false, array());

        if($props = $db_props->Fetch())
        {
            if($arElement = IBlock::getElement(array(
                "IBLOCK_ID" => Order::IBLOCK_ID,
                "CODE"      => General::getSubDomain()
            ), ['ID', 'PROPERTY_INN', 'NAME'])
            )
            {
                $arUserResult['ORDER_PROP'][$props['ID']] = $arElement['PROPERTY_INN_VALUE'];
            }
        }
    }

    static public function OnBeforeProlog()
    {
        if($subDomain = General::getSubDomain())
        {

            //$arElement = DomainSettings::getSettings();

            $code = General::getSubDomain();

            if(!$Partner = Partner::getByCode($code))
            {
                $siteServerName = SITE_SERVER_NAME != null ? SITE_SERVER_NAME : $_SERVER['SERVER_NAME'];

                header("HTTP/1.1 301 Moved Permanently");
                header("Location: http://" . $siteServerName . $_SERVER['REQUEST_URI']);
                die;
            }

            General::$Partner = $Partner;
        }
    }

    static public function OnSuccessCatalogImport1C($arParams, $arFields)
    {
        $xmlFile = new \CIBlockXMLFile();

        //выбираем xml id инфоблока
        $iblock_xml_id = $xmlFile->GetList([], ['ID' => '3'], ['VALUE'])->Fetch()['VALUE'];

        $res = \CIBlock::GetList(Array(), Array(
            'XML_ID' => $iblock_xml_id
        ));
        $iblock_id = false;

        if($ar_res = $res->Fetch())
        {
            $iblock_id = $ar_res['ID'];
        }

        if(!$iblock_id)
            return;

        //берём все свойства
        $arIblockProps = [];

        $res = \CIBlock::GetProperties($iblock_id, Array(), Array());
        while($res_arr = $res->Fetch())
        {
            $arIblockProps[$res_arr['CODE']] = $res_arr['ID'];
        }

        $arProps = [];
        $arFilter = ['NAME' => 'Свойство'];
        $arFile = $xmlFile->GetList([], $arFilter);
        //выбор всех свойств
        while($arStr = $arFile->Fetch())
        {
            $arId = $xmlFile->GetList([], ['><LEFT_MARGIN' => [$arStr['LEFT_MARGIN'], $arStr['RIGHT_MARGIN']]]);
            $arProp = [];
            while($arStrProp = $arId->Fetch())
            {
                $arProp[$arStrProp['ID']] = $arStrProp;
            }
            $arProps[] = $arProp;//array_reverse($arProp, true);
        }
        //удаляем свойства, которые не входят в массив нужных значений
        foreach($arProps as $key => $arProp)
        {
            foreach($arProp as $arLine)
            {
                if($arLine['NAME'] == 'Ид' && !in_array($arLine['VALUE'], Exchange::AR_XML_ID))
                    unset($arProps[$key]);
            }
        }

        if(!$arProps)
            return;

        foreach($arProps as $arProp)
        {
            $arSbProp = new Exchange($arProp);

            $arSbProp->hl_name = Exchange::xml2id($arSbProp->name);

            if($arSbProp->getHighload() > 0) //получение highload блока или создание если не найден
            {
                $entity_data_class = $arSbProp->getDataClass();

                /**
                 * обновление/добавление свойств в highload
                 */
                foreach($arSbProp->arValues as $arValue)
                {
                    $rsData = $entity_data_class::getList(array(
                        "select" => array("ID", "UF_XML_ID"),
                        "filter" => array("=UF_XML_ID" => $arValue['ID']),
                    ));
                    $arFields = [
                        'UF_XML_ID' => $arValue['ID'],
                        'UF_NAME'   => $arValue['VALUE']
                    ];
                    if($arData = $rsData->fetch())
                        $entity_data_class::update($arData["ID"], $arFields);
                    else
                        $entity_data_class::add($arFields);
                }

                if(array_key_exists(strtoupper($arSbProp->getHlName()) . $arSbProp::POSTFIX, $arIblockProps))
                {
                    $arFields = Array(
                        'NAME'               => $arSbProp->getName(),
                        'MULTIPLE'           => $arSbProp->multi == 'true' ? 'Y' : 'N',
                        'PROPERTY_TYPE'      => 'S',
                        'USER_TYPE'          => 'directory',
                        'USER_TYPE_SETTINGS' => Array(
                            'size'       => 1,
                            'width'      => 0,
                            'group'      => 'N',
                            'multiple'   => 'N',
                            'TABLE_NAME' => 'b_' . strtolower($arSbProp->getHlName())
                        )
                    );

                    $ibp = new \CIBlockProperty;
                    if(!$ibp->Update($arIblockProps[strtoupper($arSbProp->getHlName()) . $arSbProp::POSTFIX], $arFields))
                        ;//\_::dd($ibp->LAST_ERROR);
                }
                else
                {
                    $arFields = Array(
                        "NAME"               => $arSbProp->getName(),
                        "ACTIVE"             => "Y",
                        "SORT"               => "500",
                        "CODE"               => strtoupper($arSbProp->getHlName()) . $arSbProp::POSTFIX,
                        "PROPERTY_TYPE"      => "S",
                        "USER_TYPE"          => "directory",
                        "IBLOCK_ID"          => $iblock_id,//номер вашего инфоблока
                        "LIST_TYPE"          => "L",
                        "MULTIPLE"           => $arSbProp->multi == 'true' ? 'Y' : 'N',
                        "USER_TYPE_SETTINGS" => array(
                            "size"       => "1",
                            "width"      => "0",
                            "group"      => "N",
                            "multiple"   => "N",
                            "TABLE_NAME" => 'b_' . strtolower($arSbProp->getHlName())
                        )
                    );

                    $ibp = new \CIBlockProperty;
                    $PropID = $ibp->Add($arFields);
                }
            }

            $arSbProp = null;
        }
    }

    static public function OnBeforeIBlockElementUpdateImport(&$arFields)
    {
        if(!Bitrix::isCMLImport())
            return;

        $iblock_id = $arFields['IBLOCK_ID'];
        $id = $arFields['ID'];

        $res = \CIBlockElement::GetList([], ['ID' => $id, 'IBLOCK_ID' => $iblock_id], false, false, [
            'ID',
            'IBLOCK_ID',
            "PROPERTY_*"
        ]);
        if($ob = $res->GetNextElement())
        {
            $arProps = $ob->GetProperties();
            foreach($arProps as $key => $arProp)
            {
                if($arProp['CODE'] == 'DLINA_MOTKA') //пересчёт длинны за грамм
                {
                    $arValues = $arFields['PROPERTY_VALUES'][$arProp['ID']];
                    $arWeight = $arFields['PROPERTY_VALUES'][$arProps['VES_MOTKA']['ID']];

                    $propValue = false;
                    foreach($arValues as $value)
                    {
                        foreach($arWeight as $weight)
                        {
                            $propValue[] = [
                                'VALUE' => $value['VALUE'] / $weight['VALUE']
                            ];
                        }
                    }

                    $arFields['PROPERTY_VALUES'][$arProps['DLINA_PER_GRAM']['ID']] = $propValue;
                }

                if($arProp['CODE'] == 'SKEIN')
                {
                    $arWeight = $arFields['PROPERTY_VALUES'][$arProps['VES_MOTKA']['ID']];
                    $arLength = $arFields['PROPERTY_VALUES'][$arProps['DLINA_MOTKA']['ID']];

                    $propValue = false;

                    foreach($arWeight as $weight)
                    {
                        if(!$weight['VALUE'])
                            continue;

                        foreach($arLength as $length)
                        {
                            if(!$length['VALUE'])
                                continue;

                            $propValue[] = [
                                'VALUE' => $weight['VALUE'] . 'г/' . $length['VALUE'] . 'м'
                            ];
                        }
                    }

                    $arFields['PROPERTY_VALUES'][$arProps['SKEIN']['ID']] = $propValue;
//                    Bitrix::log('test.txt', $arFields['PROPERTY_VALUES'][$arProps['SKEIN']['ID']]);
                }

                if(!in_array($arProp['XML_ID'], Exchange::AR_XML_ID))
                    continue;

                $arValues = !is_array($arProp['VALUE_XML_ID']) ? array($arProp['VALUE_XML_ID']) : $arProp['VALUE_XML_ID'];

                $propValue = false;
                foreach($arValues as $value)
                {
                    $propValue[] = array('VALUE' => $value);
                }

                $arFields['PROPERTY_VALUES'][$arProps[Exchange::xml2id($key) . Exchange::POSTFIX]['ID']] = $propValue;
            }
        }
    }

    static public function OnPriceUpdate($id, $arFields) //перерасчёт цены за грамм
    {
        if(!$arFields['PRODUCT_ID'])
            return;

        $arFilter = [
            'ID' => $arFields['PRODUCT_ID']
        ];
        $arSelect = [
            'ID',
            'IBLOCK_ID',
            'PROPERTY_VES_MOTKA'
        ];

        $arElement = IBlock::getElement($arFilter, $arSelect);

        if(!$arElement['PROPERTY_VES_MOTKA_VALUE'])
            return;

        \CIBlockElement::SetPropertyValuesEx($arFields['PRODUCT_ID'], false, array('PRICE_PER_GRAM' => $arFields['PRICE'] / $arElement['PROPERTY_VES_MOTKA_VALUE']));
    }
}


