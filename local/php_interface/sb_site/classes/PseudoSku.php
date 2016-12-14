<?php
/**
 * Created by PhpStorm.
 * User: dnkolosov
 * Date: 08.12.2016
 * Time: 15:02
 */

namespace SB;

class PseudoSku
{
    const PROPERTY_GROUP = 'IDENTIFIKATOR_GRUPPY';

    const SKU_PROPS = array(
        'Z_COLOR',
        'NOMER_TSVETA',
        'RAZMER1_INSTRUMENTY',
        'RAZMER2_INSTRUMENTY'
    );

    const SKU_PROPS_CONFIG = array(
        'Z_COLOR'        => array(
            'LINK'     => 'DETAIL_PICTURE',
            'PROPERTY' => false,
            'TYPE'     => 'F',
            'PERCENT'  => true
        ),
        'NOMER_TSVETA'        => array(
            'LINK'     => 'DETAIL_PICTURE',
            'PROPERTY' => false,
            'TYPE'     => 'F',
            'PERCENT'  => true
        ),
        'RAZMER1_INSTRUMENTY' => array(
            'LINK'     => array('RAZMER1_INSTRUMENTY', 'RAZMER2_INSTRUMENTY'),
            'PROPERTY' => true,
            'TYPE'     => 'S',
            'PERCENT'  => false
        )
    );


    static function fillProduct($iblock, &$filter)
    {
        $property_enums = \CIBlockPropertyEnum::GetList(Array("DEF"  => "DESC",
                                                              "SORT" => "ASC"
        ), Array("IBLOCK_ID" => $iblock, "CODE" => 'IS_PRODUCT', 'VALUE' => 'Y'));

        if($enum_fields = $property_enums->GetNext())
            $filter['=PROPERTY_' . $enum_fields['PROPERTY_ID']] = $enum_fields['ID'];
    }

    static function setPrices(&$arResult, $arParams, $arSelect, &$arFilter)
    {
        if($arResult["PRICES"] = \CIBlockPriceTools::GetCatalogPrices($arParams["IBLOCK_ID"], $arParams["PRICE_CODE"]))
        {
            $arResult['PRICES_ALLOW'] = \CIBlockPriceTools::GetAllowCatalogPrices($arResult["PRICES"]);

            foreach($arResult["PRICES"] as &$value)
            {
                if(!$value['CAN_VIEW'] && !$value['CAN_BUY'])
                    continue;
                $arSelect[] = $value["SELECT"];
                $arFilter["CATALOG_SHOP_QUANTITY_" . $value["ID"]] = $arParams["SHOW_PRICE_COUNT"];
            }
            unset($value);
        }

        return $arSelect;
    }

    static function getSku(&$arItems, $iblock, $template, $arParams, $arrFilter = array())
    {
        foreach($arItems as &$arItem)
        {
            if(!$propertyGroup = $arItem['PROPERTIES'][self::PROPERTY_GROUP]['VALUE'])
                continue;

            $arFilter = array(
                'IBLOCK_ID'                        => $iblock,
                'PROPERTY_' . self::PROPERTY_GROUP => $propertyGroup,
                'CATALOG_AVAILABLE'                => 'Y'
            );

            $arSelect = array(
                'ID',
                'IBLOCK_ID',
                'CODE',
                'CATALOG_QUANTITY',
                'NAME',
                'PREVIEW_PICTURE',
                'DETAIL_PICTURE',
                'DETAIL_PAGE_URL',
            );

            $arSelect = self::setPrices($arItem, $arParams, $arSelect, $arFilter);

            $arItem['SKU'] = IBlock::getElements(array('id'=>'asc'), array_merge($arFilter, $arrFilter), false, false, $arSelect);

            foreach($arItem['SKU'] as &$arSku)
            {

                $arSku['PRICES'] = $arItem['PRICES'];
                $arSku['PRICES_ALLOW'] = $arItem['PRICES_ALLOW'];
                if($arSku["PRICES"] = \CIBlockPriceTools::GetItemPrices($iblock, $arItem["PRICES"], $arSku, $arParams['PRICE_VAT_INCLUDE'], array('CURRENCY_ID' => 'RUB')))
                {
                    $arSku['MIN_PRICE'] = \CIBlockPriceTools::getMinPriceFromList($arSku['PRICES']);
                    $arSku["PRICES"]['MIN_PRICE'] = \CIBlockPriceTools::getMinPriceFromList($arSku['PRICES']);
                }

                $arSku['PROPERTIES'] = [];

                $arElementLink[$arSku['ID']] = &$arSku;

                \CIBlockElement::GetPropertyValuesArray($arElementLink, $arParams["IBLOCK_ID"], array(
                    'ID'        => $arSku['ID'],
                    'IBLOCK_ID' => $arParams['IBLOCK_ID']
                ), ['CODE' => array_merge($arParams['PROPERTY_CODE'], self::SKU_PROPS)]);

                foreach($arSku['PROPERTIES'] as $arProperty)
                {
                    if(in_array($arProperty['CODE'], self::SKU_PROPS))
                        continue;

                    $prop = &$arProperty;
                    $boolArr = is_array($arProperty["VALUE"]);
                    if(($boolArr && !empty($arProperty["VALUE"])) || (!$boolArr && strlen($arProperty["VALUE"]) > 0))
                    {
                        $arSku["DISPLAY_PROPERTIES"][$arProperty['CODE']] = \CIBlockFormatProperties::GetDisplayValue($arSku, $prop, "catalog_out");
                    }
                }

                $res = \CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $arSku['ID'], array("sort" => "asc"), array("CODE" => "NASHI_PREDLOZHENIYA"));
                while($ob = $res->GetNext())
                {
                    if($ob['VALUE_ENUM'])
                    {
                        $arSku['PROPERTIES'][$ob['CODE']]['VALUE_XML_ID'][] = $ob['VALUE_XML_ID'];
                        $arSku['PROPERTIES'][$ob['CODE']]['VALUE'][] = $ob['VALUE_ENUM'];
                    }
                }

                if($arSku['PREVIEW_PICTURE'])
                {
                    if(!$arItem['PREVIEW_PICTURE'])
                        $arItem["PREVIEW_PICTURE"] = (0 < $arSku["PREVIEW_PICTURE"] ? \CFile::GetFileArray($arSku["PREVIEW_PICTURE"]) : false);

                    $arSku['PREVIEW_PICTURE'] = \CFile::GetFileArray($arSku["PREVIEW_PICTURE"]);
                }

                if($arSku['DETAIL_PICTURE'])
                {
                    if(!$arItem['DETAIL_PICTURE'])
                        $arItem["DETAIL_PICTURE"] = (0 < $arSku["DETAIL_PICTURE"] ? \CFile::GetFileArray($arSku["DETAIL_PICTURE"]) : false);

                    $arSku['DETAIL_PICTURE'] = \CFile::GetFileArray($arSku["DETAIL_PICTURE"]);
                }

                $skuTemplate = $arItem['PROPERTIES'][self::SKU_PROPS[0]]['VALUE'] ? 'COLOR' : 'SIZE';
            }

            try
            {
                $arItem['SKU_TEMPLATE'] = self::getSkuTemplate($arItem['SKU'], $iblock, $arItem['PROPERTIES'], $template, $arItem['QUANTITY_HTML'], $skuTemplate);
            }
            catch(\Exception $e)
            {
                echo $e->getMessage();
            }
        }
    }

    static function getSkuTemplate($arSku, $iblock, $arProps, $template, $quantity = false, $skuTemplate)
    {
        $arResult = '';
        switch($skuTemplate)
        {
            case 'COLOR':
                switch($template)
                {
                    case 'block':
                        $arResult .= self::blockColorTemplate($iblock, $arSku, $arProps, $quantity);
                        break;
                    case 'list':
                        $arResult .= self::listColorTemplate($iblock, $arSku, $arProps, $quantity);
                        break;
                    case 'detail':
                        $arResult .= '';
                        break;
                    default:
                        throw new \Exception('incorrect template');
                }
                break;
            case 'SIZE':
                switch($template)
                {
                    case 'block':
                        $arResult .= self::blockSizeTemplate($iblock, $arSku, $arProps, $quantity);
                        break;
                    case 'list':
                        $arResult .= self::listSizeTemplate($iblock, $arSku, $arProps, $quantity);
                        break;
                    case 'detail':
                        $arResult .= self::detailSizeTemplate($iblock, $arSku, $arProps, $quantity);
                        break;
                    default:
                        throw new \Exception('incorrect template');
                }
                break;
        }

        return $arResult;
    }

    static function getColorNumber($arSku, $id)
    {
        foreach($arSku as $sku)
        {
            if($sku['ID'] == $id)
                return $sku['PROPERTIES']['Z_COLOR']['VALUE'] ? $sku['PROPERTIES']['Z_COLOR']['VALUE'] : $sku['PROPERTY_Z_COLOR_VALUE'];
        }

        return false;
    }

    static function blockColorTemplate($iblock, $arSku, $arProps, $quantity)
    {
        $prop = self::SKU_PROPS[0];
        $arResult = '<div class="sb_offers">';

        if(!$arProps[$prop]['NAME'])
        {
            $arResult .= '</div>';

            return $arResult;
        }

        $arResult .= '<div class="item_detail_scu_block"><span class="with_quantity"><span class="show_class bx_item_section_name">' . $arProps[$prop]['NAME'] . ': ' . $arProps[$prop]['VALUE'] . '</span>' . $quantity . '</span>';

        $arResult .= '<div class="bx_scu_scroller_container"><div class=""><ul class="sb_sku_container prop-type-' . strtolower(self::SKU_PROPS_CONFIG[$prop]['TYPE']) . ' ' . strtolower(self::SKU_PROPS_CONFIG[$prop]['LINK']) . '">';

        foreach($arSku as $sku)
        {
            if(!$sku['PROPERTIES'][$prop]['VALUE'])
                continue;

            $select = $arProps[$prop]['VALUE'] == $sku['PROPERTIES'][$prop]['VALUE'] ? 'active' : '';

            $arResult .= '<li class="' . $select . '" data-value="' . $sku['PROPERTIES'][$prop]['VALUE'] . '" data-id="' . $sku['ID'] . '" data-prop-id="' . $arProps[$prop]['ID'] . '"><a href="' . $sku["DETAIL_PAGE_URL"] . '"><span class="cnt">';

            $select = $arProps[$prop]['VALUE'] == $sku['PROPERTIES'][$prop]['VALUE'] ? 'active sb_border_color sb_background_color' : '';

            $percent = self::SKU_PROPS_CONFIG[$prop]['PERCENT'] ? '<i class="fa fa-percent sb_background_color" aria-hidden="true"></i>' : '';

            $arResult .= '<span class="cnt_item ' . $select . '" style="background-image:url(' . $sku[self::SKU_PROPS_CONFIG[$prop]['LINK']]['SRC'] . ');" title="' . $sku['PROPERTIES'][$prop]['NAME'] . ': ' . $sku['PROPERTIES'][$prop]['VALUE'] . '">' . $percent . '</span><div class="sku_code">' . $sku['PROPERTIES'][$prop]['VALUE'] . '</div>';

            $arResult .= '</span></a></li>';
        }

        $arResult .= '</ul></div></div>';

        $arResult .= '</div>';

        $arResult .= '</div>';

        return $arResult;
    }

    static function listColorTemplate($iblock, $arSku, $arProps, $quantity)
    {
        $prop = self::SKU_PROPS[0];
        $arResult = '';

        if(!$arProps[$prop]['NAME'])
            return $arResult;

        $arResult .= '<div class="show_props with_quantity"><a class="opened"><i class="icon"><b></b></i><span>' . $arProps[$prop]['NAME'] . ': ' . $arProps[$prop]['VALUE'] . '</span></a>' . $quantity . '</div>';
        $arResult .= '<div class="props_list_wrapp item_detail_scu_list sb_offers" style="display: block;">';

        $arResult .= '<ul class="sb_sku_container prop-type-' . strtolower(self::SKU_PROPS_CONFIG[$prop]['TYPE']) . ' ' . strtolower(self::SKU_PROPS_CONFIG[$prop]['LINK']) . '">';

        foreach($arSku as $sku)
        {
            if(!$sku['PROPERTIES'][$prop]['VALUE'])
                continue;

            $select = $arProps[$prop]['VALUE'] == $sku['PROPERTIES'][$prop]['VALUE'] ? 'active' : '';

            $arResult .= '<li class="' . $select . '" data-value="' . $sku['PROPERTIES'][$prop]['VALUE'] . '" data-id="' . $sku['ID'] . '" data-prop-id="' . $arProps[$prop]['ID'] . '"><a href="' . $sku["DETAIL_PAGE_URL"] . '"><span class="cnt">';

            $select = $arProps[$prop]['VALUE'] == $sku['PROPERTIES'][$prop]['VALUE'] ? 'active sb_border_color sb_background_color' : '';

            $percent = self::SKU_PROPS_CONFIG[$prop]['PERCENT'] ? '<i class="fa fa-percent sb_background_color" aria-hidden="true"></i>' : '';

            $arResult .= '<span class="cnt_item ' . $select . '" style="background-image:url(' . $sku[self::SKU_PROPS_CONFIG[$prop]['LINK']]['SRC'] . ');" title="' . $sku['PROPERTIES'][$prop]['NAME'] . ': ' . $sku['PROPERTIES'][$prop]['VALUE'] . '">' . $percent . '</span><div class="sku_code">' . $sku['PROPERTIES'][$prop]['VALUE'] . '</div>';

            $arResult .= '</span></a></li>';
        }

        $arResult .= '</ul>';

        $arResult .= '</div>';

        return $arResult;
    }

    static function blockSizeTemplate($iblock, $arSku, $arProps, $quantity)
    {
        $prop = self::SKU_PROPS[1];
        $prop1 = self::SKU_PROPS_CONFIG[$prop]['LINK'][0];
        $prop2 = self::SKU_PROPS_CONFIG[$prop]['LINK'][1];
        $arResult = '<div class="sb_offers">';

        if(!$arProps[$prop]['NAME'])
        {
            $arResult .= '</div>';

            return $arResult;
        }

        $arIds = [];
        foreach($arSku as $sku)
        {
            $arIds[] = $sku['ID'];
        }

        $arResult .= '<div class="item_detail_scu_block"><span class="with_quantity"><span class="show_class bx_item_section_name">' . $arProps[$prop]['NAME'] . ': ' . '</span>' . $quantity . '</span>';

        $arResult .= '<div class="bx_scu_scroller_container"><div class=""><ul data-id="' . implode(',', $arIds) . '" class="sb_sku_container prop-type-' . strtolower(self::SKU_PROPS_CONFIG[$prop]['TYPE']) . ' ' . strtolower(self::SKU_PROPS_CONFIG[$prop]['LINK'][0]) . '">';

        $arDifference = [];

        foreach($arSku as $sku)
        {
            if(!$sku['PROPERTIES'][$prop1]['VALUE'] || !$sku['PROPERTIES'][$prop2]['VALUE'])
                continue;

            if(in_array($sku['PROPERTIES'][$prop1]['VALUE'], $arDifference))
                continue;

            $arDifference[] = $sku['PROPERTIES'][$prop1]['VALUE'];

            $select = $arProps[$prop1]['VALUE'] == $sku['PROPERTIES'][$prop1]['VALUE'] ? 'active' : '';

            $arResult .= '<li class="' . $select . '" data-value="' . $sku['PROPERTIES'][$prop1]['VALUE'] . '" data-id="' . implode(',', $arIds) . '" data-prop-id="' . $arProps[$prop1]['ID'] . '"><a href="' . $sku["DETAIL_PAGE_URL"] . '"><span class="cnt">';

            $select = $arProps[$prop1]['VALUE'] == $sku['PROPERTIES'][$prop1]['VALUE'] ? 'active sb_border_color sb_background_color' : '';

            $percent = self::SKU_PROPS_CONFIG[$prop]['PERCENT'] ? '<i class="fa fa-percent sb_background_color" aria-hidden="true"></i>' : '';

            $arResult .= '<span class="cnt_item ' . $select . '" title="' . $sku['PROPERTIES'][$prop1]['NAME'] . ': ' . $sku['PROPERTIES'][$prop1]['VALUE'] . '">' . $percent . $sku['PROPERTIES'][$prop1]['VALUE'] . '</span>';

            $arResult .= '</span></a></li>';
        }

        $arResult .= '</ul></div></div>';

        $arResult .= '<div class="bx_scu_scroller_container"><div class=""><ul data-id="' . implode(',', $arIds) . '" class="sb_sku_container prop-type-' . strtolower(self::SKU_PROPS_CONFIG[$prop]['TYPE']) . ' ' . strtolower(self::SKU_PROPS_CONFIG[$prop]['LINK'][1]) . '">';

        $arDifference = [];

        foreach($arSku as $sku)
        {
            if(!$sku['PROPERTIES'][$prop1]['VALUE'] || !$sku['PROPERTIES'][$prop2]['VALUE'] || $arProps[$prop1]['VALUE'] != $sku['PROPERTIES'][$prop1]['VALUE'])
                continue;

            if(in_array($sku['PROPERTIES'][$prop2]['VALUE'], $arDifference))
                continue;

            $arDifference[] = $sku['PROPERTIES'][$prop2]['VALUE'];

            $select = $arProps[$prop2]['VALUE'] == $sku['PROPERTIES'][$prop2]['VALUE'] ? 'active' : '';

            $arResult .= '<li class="' . $select . '" data-value="' . $sku['PROPERTIES'][$prop2]['VALUE'] . '" data-id="' . implode(',', $arIds) . '" data-prop-id="' . $arProps[$prop2]['ID'] . '"><a href="' . $sku["DETAIL_PAGE_URL"] . '"><span class="cnt">';

            $select = $arProps[$prop2]['VALUE'] == $sku['PROPERTIES'][$prop2]['VALUE'] ? 'active sb_border_color sb_background_color' : '';

            $percent = self::SKU_PROPS_CONFIG[$prop]['PERCENT'] ? '<i class="fa fa-percent sb_background_color" aria-hidden="true"></i>' : '';

            $arResult .= '<span class="cnt_item ' . $select . '" title="' . $sku['PROPERTIES'][$prop2]['NAME'] . ': ' . $sku['PROPERTIES'][$prop2]['VALUE'] . '">' . $percent . $sku['PROPERTIES'][$prop2]['VALUE'] . '</span>';

            $arResult .= '</span></a></li>';
        }

        $arResult .= '</ul></div></div>';

        $arResult .= '</div>';

        $arResult .= '</div>';

        return $arResult;
    }

    static function listSizeTemplate($iblock, $arSku, $arProps, $quantity)
    {
        $prop = self::SKU_PROPS[1];
        $prop1 = self::SKU_PROPS_CONFIG[$prop]['LINK'][0];
        $prop2 = self::SKU_PROPS_CONFIG[$prop]['LINK'][1];
        $arResult = '';

        if(!$arProps[$prop]['NAME'])
            return $arResult;

        $arIds = [];
        foreach($arSku as $sku)
        {
            $arIds[] = $sku['ID'];
        }

        $arResult .= '<div class="show_props with_quantity"><a class="opened"><i class="icon"><b></b></i><span>' . $arProps[$prop]['NAME'] . ': ' . '</span></a>' . $quantity . '</div>';
        $arResult .= '<div class="props_list_wrapp item_detail_scu_list sb_offers" style="display: block;">';

        $arResult .= '<ul data-id="' . implode(',', $arIds) . '" class="sb_sku_container prop-type-' . strtolower(self::SKU_PROPS_CONFIG[$prop]['TYPE']) . ' ' . strtolower($prop1) . '">';

        $arDifference = [];

        foreach($arSku as $sku)
        {
            if(!$sku['PROPERTIES'][$prop1]['VALUE'] || !$sku['PROPERTIES'][$prop2]['VALUE'])
                continue;

            if(in_array($sku['PROPERTIES'][$prop1]['VALUE'], $arDifference))
                continue;

            $arDifference[] = $sku['PROPERTIES'][$prop1]['VALUE'];

            $select = $arProps[$prop1]['VALUE'] == $sku['PROPERTIES'][$prop1]['VALUE'] ? 'active' : '';

            $arResult .= '<li class="' . $select . '" data-value="' . $sku['PROPERTIES'][$prop1]['VALUE'] . '" data-id="' . implode(',', $arIds) . '" data-prop-id="' . $arProps[$prop1]['ID'] . '"><a href="' . $sku["DETAIL_PAGE_URL"] . '"><span class="cnt">';

            $select = $arProps[$prop1]['VALUE'] == $sku['PROPERTIES'][$prop1]['VALUE'] ? 'active sb_border_color sb_background_color' : '';

            $percent = self::SKU_PROPS_CONFIG[$prop]['PERCENT'] ? '<i class="fa fa-percent sb_background_color" aria-hidden="true"></i>' : '';

            $arResult .= '<span class="cnt_item ' . $select . '" title="' . $sku['PROPERTIES'][$prop1]['NAME'] . ': ' . $sku['PROPERTIES'][$prop1]['VALUE'] . '">' . $percent . $sku['PROPERTIES'][$prop1]['VALUE'] . '</span>';

            $arResult .= '</span></a></li>';
        }

        $arResult .= '</ul>';

        $arResult .= '<ul data-id="' . implode(',', $arIds) . '" class="sb_sku_container prop-type-' . strtolower(self::SKU_PROPS_CONFIG[$prop]['TYPE']) . ' ' . strtolower($prop2) . '">';

        $arDifference = [];

        foreach($arSku as $sku)
        {
            if(!$sku['PROPERTIES'][$prop1]['VALUE'] || !$sku['PROPERTIES'][$prop2]['VALUE'] || $arProps[$prop1]['VALUE'] != $sku['PROPERTIES'][$prop1]['VALUE'])
                continue;

            if(in_array($sku['PROPERTIES'][$prop2]['VALUE'], $arDifference))
                continue;

            $arDifference[] = $sku['PROPERTIES'][$prop2]['VALUE'];

            $select = $arProps[$prop2]['VALUE'] == $sku['PROPERTIES'][$prop2]['VALUE'] ? 'active' : '';

            $arResult .= '<li class="' . $select . '" data-value="' . $sku['PROPERTIES'][$prop2]['VALUE'] . '" data-id="' . implode(',', $arIds) . '" data-prop-id="' . $arProps[$prop2]['ID'] . '"><a href="' . $sku["DETAIL_PAGE_URL"] . '"><span class="cnt">';

            $select = $arProps[$prop2]['VALUE'] == $sku['PROPERTIES'][$prop2]['VALUE'] ? 'active sb_border_color sb_background_color' : '';

            $percent = self::SKU_PROPS_CONFIG[$prop]['PERCENT'] ? '<i class="fa fa-percent sb_background_color" aria-hidden="true"></i>' : '';

            $arResult .= '<span class="cnt_item ' . $select . '" title="' . $sku['PROPERTIES'][$prop2]['NAME'] . ': ' . $sku['PROPERTIES'][$prop2]['VALUE'] . '">' . $percent . $sku['PROPERTIES'][$prop2]['VALUE'] . '</span>';

            $arResult .= '</span></a></li>';
        }

        $arResult .= '</ul>';

        $arResult .= '</div>';

        return $arResult;
    }

    static function detailSizeTemplate($iblock, $arSku, $arProps, $quantity)
    {
        $prop = self::SKU_PROPS[1];
        $prop1 = self::SKU_PROPS_CONFIG[$prop]['LINK'][0];
        $prop2 = self::SKU_PROPS_CONFIG[$prop]['LINK'][1];
        $arResult = '';

        if(!$arProps[$prop]['NAME'])
            return $arResult;

        $arResult .= '<div class="show_props with_quantity"><span>' . $arProps[$prop]["NAME"] . '</span>' . $quantity . '</div>';
        $arResult .= '<div class="props_list_wrapp item_detail_scu_list item_detail_scu_detail sb_offers" style="display: block;">';

        $arResult .= '<ul class="sb_sku_container prop-type-' . strtolower(self::SKU_PROPS_CONFIG[$prop]['TYPE']) . ' ' . strtolower(self::SKU_PROPS_CONFIG[$prop]['LINK'][0]) . '">';

        $arDifference = [];

        $arIds = [];
        foreach($arSku as $sku)
        {
            $arIds[] = $sku['ID'];
        }

        foreach($arSku as $sku)
        {
            if(!$sku['PROPERTIES'][$prop1]['VALUE'] || !$sku['PROPERTIES'][$prop2]['VALUE'])
                continue;

            if(in_array($sku['PROPERTIES'][$prop1]['VALUE'], $arDifference))
                continue;

            $arDifference[] = $sku['PROPERTIES'][$prop1]['VALUE'];

            $select = $arProps[$prop1]['VALUE'] == $sku['PROPERTIES'][$prop1]['VALUE'] ? 'active' : '';

            $arResult .= '<li class="' . $select . '" data-value="' . $sku['PROPERTIES'][$prop1]['VALUE'] . '" data-id="' . implode(',', $arIds) . '" data-prop-id="' . $arProps[$prop1]['ID'] . '"><a href="' . $sku["DETAIL_PAGE_URL"] . '"><span class="cnt">';

            $select = $arProps[$prop1]['VALUE'] == $sku['PROPERTIES'][$prop1]['VALUE'] ? 'active sb_border_color sb_background_color' : '';

            $percent = self::SKU_PROPS_CONFIG[$prop]['PERCENT'] ? '<i class="fa fa-percent sb_background_color" aria-hidden="true"></i>' : '';

            $arResult .= '<span class="cnt_item ' . $select . '" title="' . $sku['PROPERTIES'][$prop1]['NAME'] . ': ' . $sku['PROPERTIES'][$prop1]['VALUE'] . '">' . $percent . $sku['PROPERTIES'][$prop1]['VALUE'] . '</span>';

            $arResult .= '</span></a></li>';
        }

        $arResult .= '</ul>';

        $arResult .= '<ul class="sb_sku_container prop-type-' . strtolower(self::SKU_PROPS_CONFIG[$prop]['TYPE']) . ' ' . strtolower(self::SKU_PROPS_CONFIG[$prop]['LINK'][1]) . '">';

        $arDifference = [];

        foreach($arSku as $sku)
        {
            if(!$sku['PROPERTIES'][$prop1]['VALUE'] || !$sku['PROPERTIES'][$prop2]['VALUE'] || $arProps[$prop1]['VALUE'] != $sku['PROPERTIES'][$prop1]['VALUE'])
                continue;

            if(in_array($sku['PROPERTIES'][$prop2]['VALUE'], $arDifference))
                continue;

            $arDifference[] = $sku['PROPERTIES'][$prop2]['VALUE'];

            $select = $arProps[$prop2]['VALUE'] == $sku['PROPERTIES'][$prop2]['VALUE'] ? 'active' : '';

            $arResult .= '<li class="' . $select . '" data-value="' . $sku['PROPERTIES'][$prop2]['VALUE'] . '" data-id="' . implode(',', $arIds) . '" data-prop-id="' . $arProps[$prop2]['ID'] . '"><a href="' . $sku["DETAIL_PAGE_URL"] . '"><span class="cnt">';

            $select = $arProps[$prop2]['VALUE'] == $sku['PROPERTIES'][$prop2]['VALUE'] ? 'active sb_border_color sb_background_color' : '';

            $percent = self::SKU_PROPS_CONFIG[$prop]['PERCENT'] ? '<i class="fa fa-percent sb_background_color" aria-hidden="true"></i>' : '';

            $arResult .= '<span class="cnt_item ' . $select . '" title="' . $sku['PROPERTIES'][$prop2]['NAME'] . ': ' . $sku['PROPERTIES'][$prop2]['VALUE'] . '">' . $percent . $sku['PROPERTIES'][$prop2]['VALUE'] . '</span>';

            $arResult .= '</span></a></li>';
        }

        $arResult .= '</ul>';

        $arResult .= '</div>';

        //        $res = \CIBlockProperty::GetByID($prop, $iblock);
        //        if($ar_res = $res->Fetch())
        //        {
        //            if(!empty(self::SKU_PROPS_CONFIG[$prop]) )
        //            {
        //                $arLinks = !is_array(self::SKU_PROPS_CONFIG[$prop]['LINK']) ? array(self::SKU_PROPS_CONFIG[$prop]['LINK']) : self::SKU_PROPS_CONFIG[$prop]['LINK'];
        //                $arResult .= '<div class="show_props with_quantity"><span>' . $ar_res["NAME"] . '</span>' . $quantity . '</div>';
        //                $arResult .= '<div class="props_list_wrapp item_detail_scu_list item_detail_scu_detail sb_offers" style="display: block;">';
        //
        //                $arProps = array();
        //
        //                foreach($arLinks as $k => $link)
        //                {
        //                    $arProps[$k] = '<ul class="sb_sku_container prop-type-' . strtolower(self::SKU_PROPS_CONFIG[$prop]['TYPE']) . ' ' . strtolower($link ) . '">';
        //
        //                    if($skuTemplate = self::printSku($arSku, $id, $iblock, $prop, $link))
        //                        $arProps[$k] .= $skuTemplate;
        //                    else
        //                    {
        //                        unset($arProps[$k]);
        //                        continue;
        //                    }
        //
        //                    $arProps[$k] .= '</ul>';
        //                }
        //
        //                $arResult .= implode('', $arProps);
        //
        //                $arResult .= '</div>';
        //
        //                if(count($arProps) == 0)
        //                    $arResult = '';
        //            }
        //        }

        return $arResult;
    }

    static protected function printSku($arSku, $id, $iblock, $prop, $link)
    {
        $arResult = '';
        foreach($arSku as $sku)
        {
            $res = \CIBlockElement::GetProperty($iblock, $sku['ID'], array("sort" => "asc"), array("CODE" => $prop));
            if($ob = $res->Fetch())
            {
                if(!$ob['VALUE'])
                    continue;

                $active = $id == $sku['ID'] ? 'active' : '';
                $arResult .= '<li class="' . $active . '" data-id="' . $sku['ID'] . '" data-prop-id="' . $ob['ID'] . '"><a href="' . $sku["DETAIL_PAGE_URL"] . '"><span class="cnt">';

                $active = $id == $sku['ID'] ? 'active sb_border_color sb_background_color' : '';

                $name = self::SKU_PROPS_CONFIG[$prop]['PROPERTY'] ? 'PROPERTY_' . $link . '_VALUE' : $link;

                $percent = self::SKU_PROPS_CONFIG[$prop]['PERCENT'] ? '<i class="fa fa-percent sb_background_color" aria-hidden="true"></i>' : '';

                switch(self::SKU_PROPS_CONFIG[$prop]['TYPE'])
                {
                    case 'F':
                        $code = $sku['PROPERTIES']['Z_COLOR']['VALUE'] ? $sku['PROPERTIES']['Z_COLOR']['VALUE'] : $sku['PROPERTY_Z_COLOR_VALUE'];
                        $arResult .= '<span class="cnt_item ' . $active . '" style="background-image:url(' . $sku[$name]['SRC'] . ');" title="' . $ob['NAME'] . ': ' . $ob['VALUE'] . '">' . $percent . '</span><div class="sku_code">' . $code . '</div>';
                        break;
                    case 'S':
                        $value = $sku[$name] ? $sku[$name] : $sku['PROPERTIES'][$link]['VALUE'];
                        $arResult .= '<span class="cnt_item ' . $active . '" title="' . $ob['NAME'] . ': ' . $ob['VALUE'] . '">' . $percent . $value . '</span>';
                        break;
                }

                $arResult .= '</span></a></li>';
            }
        }

        return $arResult;
    }
}