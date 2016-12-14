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