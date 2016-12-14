<?php
/**
 * Created by PhpStorm.
 * User: dnkolosov
 * Date: 24.11.2016
 * Time: 12:51
 */

namespace SB;
use \Bitrix\Highloadblock as HL;
class Highload extends \CIBlockPropertyDirectory
{
    public static function GetExtendedValue($tableName, $value, $arSelect = array())
    {

        if (!isset($value))
            return false;
        if (empty($tableName))
            return false;

        if (!isset(parent::$arItemCache[$tableName]))
            parent::$arItemCache[$tableName] = array();

//        if (!isset(parent::$arItemCache[$tableName][$value]))
//        {
            $arData = self::getEntityFieldsByFilter(
                $tableName,
                array(
                    'select' => array_merge(array('UF_XML_ID', 'UF_NAME'), $arSelect),
                    'filter' => array('=UF_XML_ID' => $value)
                )
            );
            if (!empty($arData))
            {
                $arData = current($arData);
                if (isset($arData['UF_XML_ID']) && $arData['UF_XML_ID'] == $value)
                {
                    $arData['VALUE'] = $arData['UF_NAME'];
                    if (isset($arData['UF_FILE']))
                        $arData['FILE_ID'] = $arData['UF_FILE'];
                    parent::$arItemCache[$tableName][$value] = $arData;
                }
            }
        //}

        if (isset(parent::$arItemCache[$tableName][$value]))
        {
            return parent::$arItemCache[$tableName][$value];
        }
        return false;
    }

    /**
     * @param array $filter
     * @return array
     */
    private static function getEntityFieldsByFilter($tableName, $listDescr = array())
    {
        $arResult = array();
        $tableName = (string)$tableName;
        if (!is_array($listDescr))
            $listDescr = array();
        if (!empty($tableName))
        {
            $hlblock = HL\HighloadBlockTable::getList(
                array(
                    'select' => array('TABLE_NAME', 'NAME', 'ID'),
                    'filter' => array('=TABLE_NAME' => $tableName)
                )
            )->fetch();
            if (isset($hlblock['ID']))
            {
                $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                $entityDataClass = $entity->getDataClass();
                if (!isset(parent::$directoryMap[$tableName]))
                {
                    parent::$directoryMap[$tableName] = $entityDataClass::getEntity()->getFields();
                }

                if (!isset(parent::$directoryMap[$tableName]['UF_XML_ID']))
                {
                    return $arResult;
                }

                $nameExist = isset(parent::$directoryMap[$tableName]['UF_NAME']);
                if (!$nameExist)
                    $listDescr['select'] = array('UF_XML_ID', 'ID');
                $fileExists = isset(parent::$directoryMap[$tableName]['UF_FILE']);
                if ($fileExists)
                    $listDescr['select'][] = 'UF_FILE';
                $sortExist = isset(parent::$directoryMap[$tableName]['UF_SORT']);
                $listDescr['order'] = array();
                if ($sortExist)
                    $listDescr['order']['UF_SORT'] = 'ASC';
                if ($nameExist)
                    $listDescr['order']['UF_NAME'] = 'ASC';
                else
                    $listDescr['order']['UF_XML_ID'] = 'ASC';
                $listDescr['order']['ID'] = 'ASC';
                
                $rsData = $entityDataClass::getList($listDescr);
                while($arData = $rsData->fetch())
                {
                    if (!$nameExist)
                        $arData['UF_NAME'] = $arData['UF_XML_ID'];
                    $arResult[] = $arData;
                }
            }
        }
        return $arResult;
    }

    public static function deleteDeactivated(&$arValues)
    {
        foreach($arValues as $key => $value)
        {
            if($value['UF_ACTIVE'] != '1')
                unset($arValues[$key]);
        }
    }
}