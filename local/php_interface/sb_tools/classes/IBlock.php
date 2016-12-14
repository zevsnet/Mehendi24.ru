<?
namespace SB;

if(!\CModule::IncludeModule("iblock"))
    die;

use Bitrix\Main\DB\Exception;
use \CIBlockElement as CIBlockElement;
use \CIBlockSection as CIBlockSection;

/**
 * Class SB
 *
 * Вспомагательные функции
 */
class IBlock
{
    /**
     * @author V. Shiryakov <21.01.2015, Implementation>
     *
     * Выводит свойство
     *
     * @param $arItem
     * @param $name
     * @param string $field
     *
     * @return null
     *
     */
    public static function getDisplayProperty($arItem, $name, $field = 'DISPLAY_VALUE')
    {
        return isset($arItem["DISPLAY_PROPERTIES"][$name][$field]) ? $arItem["DISPLAY_PROPERTIES"][$name][$field] : null;
    }

    public static function getProperty($arItem, $name, $field = 'VALUE')
    {
        return isset($arItem["PROPERTIES"][$name][$field]) ? $arItem["PROPERTIES"][$name][$field] : null;
    }

    public static function getElement($arFilter = Array(), $arSelectFields = array(), $arGroupBy = false)
    {
        $arOrder = array();
        $arNavStartParams = array('nPageSize'=>1);
        $res = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);

        while($ob = $res->GetNextElement())
        {
            return $ob->GetFields();
        }
    }

    public static function getElements($arOrder = array("SORT"=>"ASC"), $arFilter = Array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array())
    {
        $res = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
        $arResult = array();
        while($ob = $res->GetNextElement())
        {
            $arResult[] = $ob->GetFields();
        }

        return $arResult;
    }

    public static function GetPropertyValuesArray(&$result, $iblockID, $filter, $propertyFilter = array())
    {
        $propertiesList = array();
        $mapCodes = array();
        $userTypesList = array();
        $existList = array();

        $selectListMultiply = array('SORT' => SORT_ASC, 'VALUE' => SORT_STRING);
        $selectAllMultiply = array('PROPERTY_VALUE_ID' => SORT_ASC);

        $propertyID = array();
        if (isset($propertyFilter['ID']))
        {
            $propertyFilter['ID'] = (is_array($propertyFilter['ID']) ? $propertyFilter['ID'] : array($propertyFilter['ID']));
            \Bitrix\Main\Type\Collection::normalizeArrayValuesByInt($propertyFilter['ID']);
        }
        $propertyListFilter = array(
            'IBLOCK_ID' => $iblockID
        );

        $propertyListFilter = array_merge($propertyListFilter, $propertyFilter);

        if (!empty($propertyFilter['ID']))
            $propertyListFilter['ID'] = $propertyFilter['ID'];
        $propertyListFilter['ACTIVE'] = (
        isset($propertyFilter['ACTIVE']) && ($propertyFilter['ACTIVE'] == 'Y' || $propertyFilter['ACTIVE'] == 'N')
            ? $propertyFilter['ACTIVE']
            : 'Y'
        );

        $propertyIterator = \Bitrix\Iblock\PropertyTable::getList(array(
            'select' => array(
                'ID', 'NAME', 'CODE', 'PROPERTY_TYPE',
                'MULTIPLE', 'LINK_IBLOCK_ID', 'USER_TYPE', 'DEFAULT_VALUE',
                'USER_TYPE_SETTINGS'
            ),
            'filter' => $propertyListFilter,
            'order' => array('SORT'=>'ASC','ID'=>'ASC')
        ));
        while ($property = $propertyIterator->fetch())
        {
            $property['CODE'] = trim((string)$property['CODE']);
            if ($property['CODE'] === '')
            {
                $property['CODE'] = $property['ID'];
            }

            $propertyID[] = $property['ID'];

            $code = $property['CODE'];
            $property['~NAME'] = $property['NAME'];
            if (preg_match("/[;&<>\"]/", $property['NAME']))
                $property['NAME'] = htmlspecialcharsex($property['NAME']);

            if ($property['USER_TYPE'])
            {
                $userType = \CIBlockProperty::GetUserType($property['USER_TYPE']);
                if (isset($userType["ConvertFromDB"]))
                {
                    $userTypesList[$property['ID']] = $userType;
                    if(array_key_exists("DEFAULT_VALUE", $property))
                    {
                        $value = array("VALUE" => $property["DEFAULT_VALUE"], "DESCRIPTION" => "");
                        $value = call_user_func_array($userType["ConvertFromDB"], array($property, $value));
                        $property["DEFAULT_VALUE"] = $value["VALUE"];
                    }
                }
            }

            if ($property['PROPERTY_TYPE'] == \Bitrix\Iblock\PropertyTable::TYPE_LIST)
            {
                $existList[] = $property['ID'];
            }
            $mapCodes[$property['ID']] = $code;
            $propertiesList[$code] = $property;
        }
        unset($property, $propertyIterator);

        if (empty($propertiesList))
            return;

        if (!empty($existList))
        {
            $enumList = array();
            $enumIterator = \Bitrix\Iblock\PropertyEnumerationTable::getList(array(
                'select' => array('ID', 'PROPERTY_ID', 'VALUE', 'SORT', 'XML_ID'),
                'filter' => array('PROPERTY_ID' => $existList),
                'order' => array('PROPERTY_ID' => 'ASC', 'SORT' => 'ASC', 'VALUE' => 'ASC')
            ));
            while ($enum = $enumIterator->fetch())
            {
                if (!isset($enumList[$enum['PROPERTY_ID']]))
                {
                    $enumList[$enum['PROPERTY_ID']] = array();
                }
                $enumList[$enum['PROPERTY_ID']][$enum['ID']] = array(
                    'ID' => $enum['ID'],
                    'VALUE' => $enum['VALUE'],
                    'SORT' => $enum['SORT'],
                    'XML_ID' => $enum['XML_ID']
                );
            }
            unset($enum, $enumIterator);
        }

        if (!empty($propertyID))
            $valuesRes = CIBlockElement::GetPropertyValues($iblockID, $filter, true, array('ID' => $propertyID));
        else
            $valuesRes = CIBlockElement::GetPropertyValues($iblockID, $filter, true);
        while ($value = $valuesRes->Fetch())
        {
            $elementID = $value['IBLOCK_ELEMENT_ID'];
            if (!isset($result[$elementID]))
            {
                continue;
            }
            $elementValues = array();
            $existDescription = isset($value['DESCRIPTION']);
            foreach ($propertiesList as $code => $property)
            {
                $existElementDescription = isset($value['DESCRIPTION']) && array_key_exists($property['ID'], $value['DESCRIPTION']);
                $existElementPropertyID = isset($value['PROPERTY_VALUE_ID']) && array_key_exists($property['ID'], $value['PROPERTY_VALUE_ID']);
                $elementValues[$code] = $property;

                $elementValues[$code]['VALUE_ENUM'] = null;
                $elementValues[$code]['VALUE_XML_ID'] = null;
                $elementValues[$code]['VALUE_SORT'] = null;
                $elementValues[$code]['VALUE'] = null;

                if ('Y' === $property['MULTIPLE'])
                {
                    $elementValues[$code]['PROPERTY_VALUE_ID'] = false;
                    if (!isset($value[$property['ID']]) || empty($value[$property['ID']]))
                    {
                        $elementValues[$code]['DESCRIPTION'] = false;
                        $elementValues[$code]['VALUE'] = false;
                        $elementValues[$code]['~DESCRIPTION'] = false;
                        $elementValues[$code]['~VALUE'] = false;
                        if ('L' == $property['PROPERTY_TYPE'])
                        {
                            $elementValues[$code]['VALUE_ENUM_ID'] = false;
                            $elementValues[$code]['VALUE_ENUM'] = false;
                            $elementValues[$code]['VALUE_XML_ID'] = false;
                            $elementValues[$code]['VALUE_SORT'] = false;
                        }
                    }
                    else
                    {
                        if ($existElementPropertyID)
                        {
                            $elementValues[$code]['PROPERTY_VALUE_ID'] = $value['PROPERTY_VALUE_ID'][$property['ID']];
                        }
                        if (isset($userTypesList[$property['ID']]))
                        {
                            foreach ($value[$property['ID']] as $valueKey => $oneValue)
                            {
                                $raw = call_user_func_array(
                                    $userTypesList[$property['ID']]['ConvertFromDB'],
                                    array(
                                        $property,
                                        array(
                                            'VALUE' => $oneValue,
                                            'DESCRIPTION' => ($existElementDescription ? $value['DESCRIPTION'][$property['ID']][$valueKey] : ''),
                                        )
                                    )
                                );
                                $value[$property['ID']][$valueKey] = $raw['VALUE'];
                                if (!$existDescription)
                                {
                                    $value['DESCRIPTION'] = array();
                                    $existDescription = true;
                                }
                                if (!$existElementDescription)
                                {
                                    $value['DESCRIPTION'][$property['ID']] = array();
                                    $existElementDescription = true;
                                }
                                $value['DESCRIPTION'][$property['ID']][$valueKey] = (string)$raw['DESCRIPTION'];
                            }
                            if (isset($oneValue))
                                unset($oneValue);
                        }
                        if ('L' == $property['PROPERTY_TYPE'])
                        {
                            if (empty($value[$property['ID']]))
                            {
                                $elementValues[$code]['VALUE_ENUM_ID'] = $value[$property['ID']];
                                $elementValues[$code]['DESCRIPTION'] = ($existElementDescription ? $value['DESCRIPTION'][$property['ID']] : array());
                            }
                            else
                            {
                                $selectedValues = array();
                                foreach ($value[$property['ID']] as $listKey => $listValue)
                                {
                                    if (isset($enumList[$property['ID']][$listValue]))
                                    {
                                        $selectedValues[$listKey] = $enumList[$property['ID']][$listValue];
                                        $selectedValues[$listKey]['DESCRIPTION'] = (
                                        $existElementDescription && array_key_exists($listKey, $value['DESCRIPTION'][$property['ID']])
                                            ? $value['DESCRIPTION'][$property['ID']][$listKey]
                                            : ''
                                        );
                                        $selectedValues[$listKey]['PROPERTY_VALUE_ID'] = (
                                        $existElementPropertyID && array_key_exists($listKey, $value['PROPERTY_VALUE_ID'][$property['ID']])
                                            ? $value['PROPERTY_VALUE_ID'][$property['ID']][$listKey]
                                            : ''
                                        );
                                    }
                                }
                                if (empty($selectedValues))
                                {
                                    $elementValues[$code]['VALUE_ENUM_ID'] = $value[$property['ID']];
                                    $elementValues[$code]['DESCRIPTION'] = ($existElementDescription ? $value['DESCRIPTION'][$property['ID']] : array());
                                }
                                else
                                {
                                    \Bitrix\Main\Type\Collection::sortByColumn($selectedValues, $selectListMultiply);
                                    $elementValues[$code]['VALUE_ENUM_ID'] = array();
                                    $elementValues[$code]['VALUE'] = array();
                                    $elementValues[$code]['VALUE_ENUM'] = array();
                                    $elementValues[$code]['VALUE_XML_ID'] = array();
                                    $elementValues[$code]['DESCRIPTION'] = array();
                                    $elementValues[$code]['PROPERTY_VALUE_ID'] = array();
                                    foreach ($selectedValues as $listValue)
                                    {
                                        if (!isset($elementValues[$code]['VALUE_SORT']))
                                        {
                                            $elementValues[$code]['VALUE_SORT'] = array($listValue['SORT']);
                                        }
                                        $elementValues[$code]['VALUE_ENUM_ID'][] = $listValue['ID'];
                                        $elementValues[$code]['VALUE'][] = $listValue['VALUE'];
                                        $elementValues[$code]['VALUE_ENUM'][] = $listValue['VALUE'];
                                        $elementValues[$code]['VALUE_XML_ID'][] = $listValue['XML_ID'];
                                        $elementValues[$code]['PROPERTY_VALUE_ID'][] = $listValue['PROPERTY_VALUE_ID'];
                                        $elementValues[$code]['DESCRIPTION'][] = $listValue['DESCRIPTION'];
                                    }
                                    unset($selectedValues);
                                }
                            }
                        }
                        else
                        {
                            if (empty($value[$property['ID']]) || !$existElementPropertyID || isset($userTypesList[$property['ID']]))
                            {
                                $elementValues[$code]['VALUE'] = $value[$property['ID']];
                                $elementValues[$code]['DESCRIPTION'] = ($existElementDescription ? $value['DESCRIPTION'][$property['ID']] : array());
                            }
                            else
                            {
                                $selectedValues = array();
                                foreach ($value['PROPERTY_VALUE_ID'][$property['ID']] as $propKey => $propValueID)
                                {
                                    $selectedValues[$propKey] = array(
                                        'PROPERTY_VALUE_ID' => $propValueID,
                                        'VALUE' => $value[$property['ID']][$propKey],
                                    );
                                    if ($existElementDescription)
                                    {
                                        $selectedValues[$propKey]['DESCRIPTION'] = $value['DESCRIPTION'][$property['ID']][$propKey];
                                    }
                                }
                                unset($propValueID, $propKey);

                                \Bitrix\Main\Type\Collection::sortByColumn($selectedValues, $selectAllMultiply);
                                $elementValues[$code]['PROPERTY_VALUE_ID'] = array();
                                $elementValues[$code]['VALUE'] = array();
                                $elementValues[$code]['DESCRIPTION'] = array();
                                foreach ($selectedValues as &$propValue)
                                {
                                    $elementValues[$code]['PROPERTY_VALUE_ID'][] = $propValue['PROPERTY_VALUE_ID'];
                                    $elementValues[$code]['VALUE'][] = $propValue['VALUE'];
                                    if ($existElementDescription)
                                    {
                                        $elementValues[$code]['DESCRIPTION'][] = $propValue['DESCRIPTION'];
                                    }
                                }
                                unset($propValue, $selectedValues);
                            }
                        }
                    }
                    $elementValues[$code]['~VALUE'] = $elementValues[$code]['VALUE'];
                    if (is_array($elementValues[$code]['VALUE']))
                    {
                        foreach ($elementValues[$code]['VALUE'] as &$oneValue)
                        {
                            $isArr = is_array($oneValue);
                            if ($isArr || ('' !== $oneValue && null !== $oneValue))
                            {
                                if ($isArr || preg_match("/[;&<>\"]/", $oneValue))
                                {
                                    $oneValue = htmlspecialcharsEx($oneValue);
                                }
                            }
                        }
                        if (isset($oneValue))
                            unset($oneValue);
                    }
                    else
                    {
                        if ('' !== $elementValues[$code]['VALUE'] && null !== $elementValues[$code]['VALUE'])
                        {
                            if (preg_match("/[;&<>\"]/", $elementValues[$code]['VALUE']))
                            {
                                $elementValues[$code]['VALUE'] = htmlspecialcharsEx($elementValues[$code]['VALUE']);
                            }
                        }
                    }

                    $elementValues[$code]['~DESCRIPTION'] = $elementValues[$code]['DESCRIPTION'];
                    if (is_array($elementValues[$code]['DESCRIPTION']))
                    {
                        foreach ($elementValues[$code]['DESCRIPTION'] as &$oneDescr)
                        {
                            $isArr = is_array($oneDescr);
                            if ($isArr || (!$isArr && '' !== $oneDescr && null !== $oneDescr))
                            {
                                if ($isArr || preg_match("/[;&<>\"]/", $oneDescr))
                                {
                                    $oneDescr = htmlspecialcharsEx($oneDescr);
                                }
                            }
                        }
                        if (isset($oneDescr))
                            unset($oneDescr);
                    }
                    else
                    {
                        if ('' !== $elementValues[$code]['DESCRIPTION'] && null !== $elementValues[$code]['DESCRIPTION'])
                        {
                            if (preg_match("/[;&<>\"]/", $elementValues[$code]['DESCRIPTION']))
                            {
                                $elementValues[$code]['DESCRIPTION'] = htmlspecialcharsEx($elementValues[$code]['DESCRIPTION']);
                            }
                        }
                    }
                }
                else
                {
                    $elementValues[$code]['VALUE_ENUM'] = '';
                    $elementValues[$code]['PROPERTY_VALUE_ID'] = $elementID.':'.$property['ID'];

                    if (!isset($value[$property['ID']]) || false === $value[$property['ID']])
                    {
                        $elementValues[$code]['DESCRIPTION'] = '';
                        $elementValues[$code]['VALUE'] = '';
                        $elementValues[$code]['~DESCRIPTION'] = '';
                        $elementValues[$code]['~VALUE'] = '';
                        if ('L' == $property['PROPERTY_TYPE'])
                        {
                            $elementValues[$code]['VALUE_ENUM_ID'] = null;
                        }
                    }
                    else
                    {
                        if ($existElementPropertyID)
                        {
                            $elementValues[$code]['PROPERTY_VALUE_ID'] = $value['PROPERTY_VALUE_ID'][$property['ID']];
                        }
                        if (isset($userTypesList[$property['ID']]))
                        {
                            $raw = call_user_func_array(
                                $userTypesList[$property['ID']]['ConvertFromDB'],
                                array(
                                    $property,
                                    array(
                                        'VALUE' => $value[$property['ID']],
                                        'DESCRIPTION' => ($existElementDescription ? $value['DESCRIPTION'][$property['ID']] : '')
                                    )
                                )
                            );
                            $value[$property['ID']] = $raw['VALUE'];
                            if (!$existDescription)
                            {
                                $value['DESCRIPTION'] = array();
                                $existDescription = true;
                            }
                            $value['DESCRIPTION'][$property['ID']] = (string)$raw['DESCRIPTION'];
                            $existElementDescription = true;
                        }
                        if ('L' == $property['PROPERTY_TYPE'])
                        {
                            $elementValues[$code]['VALUE_ENUM_ID'] = $value[$property['ID']];
                            if (isset($enumList[$property['ID']][$value[$property['ID']]]))
                            {
                                $elementValues[$code]['VALUE'] = $enumList[$property['ID']][$value[$property['ID']]]['VALUE'];
                                $elementValues[$code]['VALUE_ENUM'] = $elementValues[$code]['VALUE'];
                                $elementValues[$code]['VALUE_XML_ID'] = $enumList[$property['ID']][$value[$property['ID']]]['XML_ID'];
                                $elementValues[$code]['VALUE_SORT'] = $enumList[$property['ID']][$value[$property['ID']]]['SORT'];
                            }
                            $elementValues[$code]['DESCRIPTION'] = ($existElementDescription ? $value['DESCRIPTION'][$property['ID']] : null);
                        }
                        else
                        {
                            $elementValues[$code]['VALUE'] = $value[$property['ID']];
                            $elementValues[$code]['DESCRIPTION'] = ($existElementDescription ? $value['DESCRIPTION'][$property['ID']] : '');
                        }
                    }
                    $elementValues[$code]['~VALUE'] = $elementValues[$code]['VALUE'];
                    $isArr = is_array($elementValues[$code]['VALUE']);
                    if ($isArr || ('' !== $elementValues[$code]['VALUE'] && null !== $elementValues[$code]['VALUE']))
                    {
                        if ($isArr || preg_match("/[;&<>\"]/", $elementValues[$code]['VALUE']))
                        {
                            $elementValues[$code]['VALUE'] = htmlspecialcharsEx($elementValues[$code]['VALUE']);
                        }
                    }

                    $elementValues[$code]['~DESCRIPTION'] = $elementValues[$code]['DESCRIPTION'];
                    $isArr = is_array($elementValues[$code]['DESCRIPTION']);
                    if ($isArr || ('' !== $elementValues[$code]['DESCRIPTION'] && null !== $elementValues[$code]['DESCRIPTION']))
                    {
                        if ($isArr || preg_match("/[;&<>\"]/", $elementValues[$code]['DESCRIPTION']))
                            $elementValues[$code]['DESCRIPTION'] = htmlspecialcharsEx($elementValues[$code]['DESCRIPTION']);
                    }
                }
            }
            if (isset($result[$elementID]['PROPERTIES']))
            {
                $result[$elementID]['PROPERTIES'] = $elementValues;
            }
            else
            {
                $result[$elementID] = $elementValues;
            }
            unset($elementValues);
        }
    }

    public static function getElementById($id)
    {
        $res = CIBlockElement::GetByID($id);
        if($arRes = $res->GetNext())
            return $arRes;
    }

    public static function getSection($arFilter = Array(), $arSelect = array())
    {
        $bIncCnt = false;
        $arNavStartParams = false;
        $arOrder  = array("SORT"=>"ASC");
        $res = CIBlockSection::GetList($arOrder, $arFilter, $bIncCnt, $arSelect, $arNavStartParams);

        while ($arSection = $res->Fetch())
        {
            return $arSection;
        }

        return null;
    }

    public static function getSections($arOrder = array("SORT"=>"ASC"), $arFilter = Array(), $bIncCnt = false, $arSelect = array(), $arNavStartParams = false)
    {
        $res = CIBlockSection::GetList($arOrder, $arFilter, $bIncCnt, $arSelect, $arNavStartParams);

        $arResult = array();
        while ($arSection = $res->Fetch())
        {
            $arResult[] = $arSection;
        }

        return $arResult;
    }

    public static function getSectionById($id)
    {
        $res = CIBlockSection::GetByID($id);

        return $res->GetNext();
    }

    /**
     * @param $arFields - Поля елемента. Если указан ['ID'] - то выполняется Update
     * @param bool $prepareFields - Флаг обработки полей, заполняет некоторые поля (IBLOCK_ID,ACTIVE,CODE,MODIFIED_BY)
     * @param bool $checkXmlId - Флаг Получения эле елемента по XML_ID. В случаи нахождения элемента выполняется Update
     *
     * @return int|null
     * @throws \Exception
     */
    public static function saveElement($arFields, $prepareFields = true, $checkXmlId = true)
    {
        // Для совместимости со старым форматом функци. saveElement($iBlockId, &$arFields, $prepareFields = true, $checkXmlId = true)
        if (!is_array($arFields))
        {
            $arArgs = func_get_args();

            $iBlockId = $arFields;
            $arFields = $prepareFields;
            $prepareFields = $checkXmlId;
            $checkXmlId = \SB\Tools::get($arArgs, 3);
            $arFields['IBLOCK_ID'] = $iBlockId;
        }

        static $Element;

        $elementId = null;
        if (!empty($arFields['ID']))
        {
            $elementId = $arFields['ID'];
            unset($arFields['ID']);
        }
        else
        {
            if (empty($arFields['IBLOCK_ID']))
                throw new \Exception('Field IBLOCK_ID doesn`t set');
        }

        $iBlockId = $arFields['IBLOCK_ID'];

        if ($Element === null)
            $Element = new \CIBlockElement;

        $arProperties = array();

        if (!empty($arFields['PROPERTIES']))
        {
            $arProperties = $arFields['PROPERTIES'];
            unset($arFields['PROPERTIES']);
        }



        // Если не передан ИД элемента и параметр проверять  по XML_ID установлен, то ищем элемент по XML_ID, и получаем ИД элемента для update
        if (!$elementId && $checkXmlId && !empty($arFields['XML_ID']))
        {
            $arElement = self::getElement(array('IBLOCK_ID'=>$iBlockId, 'XML_ID'=>$arFields['XML_ID']), array('ID'));
            if (!empty($arElement['ID']))
            {
                $elementId = $arElement['ID'];
            }
        }



        if($prepareFields)
        {
            if(!array_key_exists('MODIFIED_BY', $arFields))
            {
                global $USER;
                if($USER->GetID())
                    $arFields['MODIFIED_BY'] = $USER->GetID();
            }
        }

        if ($elementId)
        {
            $Element->Update($elementId, $arFields);
        }
        else
        {
            // Обрабатываем поля
            if($prepareFields)
            {
                if(!array_key_exists('ACTIVE', $arFields))
                {
                    $arFields['ACTIVE'] = 'Y';
                }

                if(!array_key_exists('CODE', $arFields) && array_key_exists('NAME', $arFields))
                {
                    $arFields['CODE'] = \Cutil::translit($arFields['NAME'], "ru");
                }
            }

            $elementId = $Element->Add($arFields);
        }

        // Сохраняем свойства
        if($arProperties && $elementId)
        {
            \CIBlockElement::SetPropertyValuesEx($elementId, $iBlockId, $arProperties);
        }

        return $elementId;
    }

    public static function saveElementOld($iBlockId, &$arFields, $prepareFields = true, $checkXmlId = true)
    {
        static $Element;

        if ($Element === null)
            $Element = new \CIBlockElement;

        $arProperties = array();

        if (!empty($arFields['PROPERTIES']))
        {
            $arProperties = $arFields['PROPERTIES'];
            unset($arFields['PROPERTIES']);
        }

        $elementId = null;

        // Если параметр проверять по XML_ID установлен, то ищем элемент по XML_ID, и получаем ИД элемента для update
        if ($checkXmlId && !empty($arFields['XML_ID']))
        {
            $arElement = self::getElement(array('IBLOCK_ID'=>$iBlockId, 'XML_ID'=>$arFields['XML_ID']), array('ID'));
            if (!empty($arElement['ID']))
            {
                $elementId = $arElement['ID'];
            }
        }

        if (!empty($arFields['ID']))
        {
            $elementId = $arFields['ID'];
            unset($arFields['ID']);
        }

        if($prepareFields)
        {
            if(!array_key_exists('MODIFIED_BY', $arFields))
            {
                global $USER;
                if($USER->GetID())
                    $arFields['MODIFIED_BY'] = $USER->GetID();
            }
        }

        if ($elementId)
        {
            $Element->Update($elementId, $arFields);
        }
        else
        {
            // Обрабатываем поля
            if($prepareFields)
            {
                if(!array_key_exists('IBLOCK_ID', $arFields))
                {
                    $arFields['IBLOCK_ID'] = $iBlockId;
                }

                if(!array_key_exists('ACTIVE', $arFields))
                {
                    $arFields['ACTIVE'] = 'Y';
                }

                if(!array_key_exists('CODE', $arFields) && array_key_exists('NAME', $arFields))
                {
                    $arFields['CODE'] = \Cutil::translit($arFields['NAME'], "ru");
                }
            }

            $elementId = $Element->Add($arFields);
        }


        // Сохраняем свойства
        if($arProperties && $elementId)
        {
            \CIBlockElement::SetPropertyValuesEx($elementId, $iBlockId, $arProperties);
        }

        return $elementId;
    }

    /**
     * Заполняет массивами картинок ключи с ид картинок
     *
     * @param $arItem
     * @param array $arFields
     */
    static function fillImageArray(&$arItem, $arFields = array('PREVIEW_PICTURE','DETAIL_PICTURE'))
    {
        foreach($arFields as $field)
        {
            if (!empty($arItem[$field]))
            {
                $arItem[$field] = \CFile::GetFileArray($arItem[$field]);
            }
        }
    }

    static function clearIBlock($iBlockId)
    {
        if (!$iBlockId)
            return false;

        $Result = CIBlockElement::GetList(array("ID"=>"ASC"), array ('IBLOCK_ID'=>$iBlockId), false, false, array('ID','NAME'));

        while($arElement = $Result->Fetch())
        {
            CIBlockElement::Delete($arElement['ID']);
        }

        return true;
    }
}

?>