<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */


if(!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 36000000;

$arParams["ID"] = intval($arParams["ID"]);
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);

$arParams["DEPTH_LEVEL"] = intval($arParams["DEPTH_LEVEL"]);
if($arParams["DEPTH_LEVEL"]<=0)
    $arParams["DEPTH_LEVEL"]=1;

$arResult["SECTIONS"] = array();
$arResult["ELEMENT_LINKS"] = array();

if($this->StartResultCache())
{
    if(!CModule::IncludeModule("iblock"))
    {
        $this->AbortResultCache();
    }
    else
    {
        $arFilter = array(
            "IBLOCK_ID"=>$arParams["IBLOCK_ID"],
            "GLOBAL_ACTIVE"=>"Y",
            "IBLOCK_ACTIVE"=>"Y",
            "<="."DEPTH_LEVEL" => $arParams["DEPTH_LEVEL"],
        );
        $arOrder = array(
            "left_margin"=>"asc",
        );

        $rsSections = CIBlockSection::GetList($arOrder, $arFilter, false, array(
            "ID",
            "DEPTH_LEVEL",
            "NAME",
            "SECTION_PAGE_URL",
        ));
        $rsSections->SetUrlTemplates("", $arParams["SEF_BASE_URL"].$arParams["SECTION_PAGE_URL"]);
        while($arSection = $rsSections->GetNext())
        {
            $arResult["SECTIONS"][] = array(
                "ID" => $arSection["ID"],
                "DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"],
                "~NAME" => $arSection["~NAME"],
                "~SECTION_PAGE_URL" => $arSection["~SECTION_PAGE_URL"],
            );

            if($arSection["DEPTH_LEVEL"] == 1)
            {
                $arElementSelect = ['ID'];
                $elements = [];
               // \SB\Menu::getProps($arElementSelect);
                //_::d($arElementSelect);
                $arElements = [];
                $rsElements = CIBlockElement::GetList([], [
                    "IBLOCK_ID"=>$arParams["IBLOCK_ID"],
                    'SECTION_ID' => $arSection["ID"],
                    'CATALOG_AVAILABLE' => 'Y',
                    \SB\Menu::getFilter()
                ], false, false, $arElementSelect);
                while($arElement = $rsElements->Fetch())
                {
                    $arElements[$arElement['ID']] = $arElement;
                    $elements[] = $arElement['ID'];
                }

                if($arElements)
                {
                    $arPropFilter = [
                        'ID' => $elements,
                        'IBLOCK_ID' => $arParams['IBLOCK_ID']
                    ];

                    \SB\IBlock::GetPropertyValuesArray($arElements, $arParams['IBLOCK_ID'], $arPropFilter, ['CODE' => \SB\Menu::PROPS]);
                }

                $arProps = [];
                $arPropsValues = [];

                $arUserType = CIBlockProperty::GetUserType('directory');

                foreach($arElements as $arElement)
                {
                    
                    foreach($arElements as $arElementProps)
                    {
                        foreach($arElementProps as &$props)
                        {
                            $props['USER_TYPE_SETTINGS'] = unserialize($props['USER_TYPE_SETTINGS']);
                            $arProps[$props['ID']] = $props;

                            foreach($props['VALUE'] as $value)
                            {
                                $arPropsValues[$props['ID']][$value] = $value;
                            }
                        }

                    }
                }

                foreach($arPropsValues as $key => $props)
                {
                    foreach($props as $prop)
                        \SB\Menu::fillItemValues($arProps[$key], $prop);
                }

                //$count = 0;
                foreach($arProps as $prop)
                {
//                    if($count == 0)
//                    {
                        $arResult["SECTIONS"][] = array(
                            "ID" => $arSection["ID"],
                            "DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"] + 1,
                            "~NAME" => $prop["NAME"],
                            "~SECTION_PAGE_URL" => $arSection["~SECTION_PAGE_URL"] ,
                        );
//                    }

                    foreach($prop['VALUES'] as $key => $value)
                    {
                        $arResult["SECTIONS"][] = array(
                            "ID" => $arSection["ID"],
                            "DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"] + 2,
                            "~NAME" => $value["VALUE"],
                            "~SECTION_PAGE_URL" => $arSection["~SECTION_PAGE_URL"] . 'filter/' . strtolower($prop['CODE']) . '-is-' . strtolower($key) . '/apply/',
                        );
                    }

                }

//                _::d($arProps);
            }

        }
        $this->EndResultCache();
    }
}

$aMenuLinksNew = array();
$menuIndex = 0;
$previousDepthLevel = 1;
foreach($arResult["SECTIONS"] as $arSection)
{
    if ($menuIndex > 0)
        $aMenuLinksNew[$menuIndex - 1][3]["IS_PARENT"] = $arSection["DEPTH_LEVEL"] > $previousDepthLevel;
    $previousDepthLevel = $arSection["DEPTH_LEVEL"];

    $arResult["ELEMENT_LINKS"][$arSection["ID"]][] = urldecode($arSection["~SECTION_PAGE_URL"]);
    $aMenuLinksNew[$menuIndex++] = array(
        htmlspecialcharsbx($arSection["~NAME"]),
        $arSection["~SECTION_PAGE_URL"],
        $arResult["ELEMENT_LINKS"][$arSection["ID"]],
        array(
            "FROM_IBLOCK" => true,
            "IS_PARENT" => false,
            "DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"],
        ),
    );
}



return $aMenuLinksNew;