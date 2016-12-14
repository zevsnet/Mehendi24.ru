<?php
/**
 * Created by PhpStorm.
 * User: dnkolosov
 * Date: 22.11.2016
 * Time: 13:42
 */

namespace SB;

class Exchange
{
    const POSTFIX = '_HL';
    
    const AR_XML_ID = [
        '7e80243a-b001-11e6-9fae-00259033f3f1', //категории
        '8e791990-ac9e-11e6-9fae-00259033f3f1', //оттенок
        '2047e82a-b091-11e6-9fae-00259033f3f1', //производитель
        '6c45f934-ae30-11e6-9fae-00259033f3f1' //состав пряжи
    ];
    
    public $name;
    public $id;
    public $multi;
    public $arValues;
    public $hl_name;
    public $hlblock;
    public $hl_id;
    private $dataClass;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getHlName()
    {
        return $this->hl_name;
    }
    
    function __construct($arProps)
    {
        foreach($arProps as $arProp)
        {
            switch($arProp['NAME'])
            {
                case 'Наименование':
                    $this->name = $arProp['VALUE'];
                    break;
                case 'Ид':
                    $this->id = $arProp['VALUE'];
                    break;
                case 'Множественное':
                    $this->multi = $arProp['VALUE'];
                    break;
                default:
                    if($arProp['DEPTH_LEVEL'] != '6')
                        break;

                    if($arProp['NAME'] == 'ИдЗначения')
                        $this->arValues[$arProp['PARENT_ID']]['ID'] = $arProp['VALUE'];

                    if($arProp['NAME'] == 'Значение')
                        $this->arValues[$arProp['PARENT_ID']]['VALUE'] = $arProp['VALUE'];
                    break;
            }
        }
    }
    
    function getHighload()
    {
        \CModule::IncludeModule('highloadblock');
        global $APPLICATION, $DB;
        
        $this->hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array(
            "filter" => array(
                "=NAME" => $this->hl_name,
            )
        ))->fetch();

        if ($this->hlblock)
        {
            $this->hl_id = $this->hlblock["ID"];
        }
        else
        {
            $result = \Bitrix\Highloadblock\HighloadBlockTable::add(array(
                'NAME' => $this->hl_name,
                'TABLE_NAME' => 'b_'.strtolower($this->getHlName()),
            ));
            $this->hl_id = $result->getId();

            $arFieldsName = array(
                'UF_NAME' => array("Y", "string", "Y"),
                'UF_XML_ID' => array("Y", "string", "N"),
                //'UF_VERSION' => array("Y", "string", "N"),
                'UF_DESCRIPTION' => array("N", "string", "Y"),
            );
            $obUserField = new \CUserTypeEntity();
            $sort = 100;
            foreach($arFieldsName as $fieldName => $fieldValue)
            {
                $arUserField = array(
                    "ENTITY_ID" => "HLBLOCK_".$this->hl_id,
                    "FIELD_NAME" => $fieldName,
                    "USER_TYPE_ID" => $fieldValue[1],
                    "XML_ID" => "",
                    "SORT" => $sort,
                    "MULTIPLE" => "N",
                    "MANDATORY" => $fieldValue[0],
                    "SHOW_FILTER" => "N",
                    "SHOW_IN_LIST" => $fieldValue[2],
                    "EDIT_IN_LIST" => $fieldValue[2],
                    "IS_SEARCHABLE" => "N",
                    "SETTINGS" => array(),
                );
                $res = $obUserField->Add($arUserField);
                if ($res)
                {
                    $sort += 100;
                }
                else
                {
                    if ($ex = $APPLICATION->GetException())
                        ;//\_::dd($ex->GetString());
                    else
                        ;//\_::dd('throw Exception');

                    return 0;
                }
            }
            if ($DB->type === "MYSQL")
                $len = "(50)";
            else
                $len = "";
            $DB->Query("create index IX_HLBLOCK_".$this->hl_id."_XML_ID on b_".strtolower($this->hl_name)."(UF_XML_ID$len)");
        }
        
        return $this->hl_id;
    }

    public function getDataClass()
    {
        if (!$this->dataClass)
        {
            $this->hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array(
                "filter" => array(
                    "=ID" => $this->hl_id,
                )))->fetch();
            $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($this->hlblock);
            $this->dataClass = $entity->getDataClass();
        }
        return $this->dataClass;
    }

    static function xml2id($xml)
    {
        $id = \CUtil::translit($xml, LANGUAGE_ID, array(
            "max_len" => 50,
            "change_case" => false, // 'L' - toLower, 'U' - toUpper, false - do not change
            "replace_space" => '_',
            "replace_other" => '_',
            "delete_repeat_replace" => true,
        ));
        $id = trim($id);
        $id = preg_replace("/([^A-Za-z0-9]+)/", "", $id);
        return $id;
    }
}