<?php
namespace SB;

class Html
{
    static $closeSingleTags = true;

    /**
     * Выводит тег a
     *
     * @param $sHref
     * @param $sContent
     * @param array $aAttrs
     *
     * @return string
     *
     * @author V. Shiryakov <02.11.2015, Implementation>
     */
    public static function a($sHref, $sContent, $aAttrs = array())
    {
        $aAttrs['href'] = $sHref;

        return self::tag('a', $sContent, $aAttrs);
    }

    /**
     * выводит атрибуты тега
     *
     * @param array $aAttrs - массив атрибутув
     *              $aAttrs[<название атрибута>] = <значение атрибута>. Если <значение атрибута>===true, то выводится атрибут без значения, например selected
     *
     * @return string
     *
     * @author V. Shiryakov <02.11.2015, Implementation>
     */
    public static function renderAttributes($aAttrs)
    {
        $sHtml = '';
        foreach($aAttrs as $sAttr => $sVal)
        {
            if ($sVal===true)
            {
                $sHtml .= " {$sAttr}";
            }
            else
            {
                $sHtml .= " {$sAttr}=\"{$sVal}\"";
            }
        }

        return $sHtml;
    }

    /**
     * Выводит тег
     *
     * @param $sTag - название тега
     * @param bool $sContent - содержимое
     * @param array $aAttrs - атрибуты тега
     * @param bool $bCloseTag - выводить закрвабщийся тег
     *
     * @return string

     * @author V. Shiryakov <02.11.2015, Implementation>
     */
    public static function tag($sTag, $sContent = false, $aAttrs = array(), $bCloseTag = true)
    {
        $sHtml = '<' . $sTag . self::renderAttributes($aAttrs);
        if($sContent === false)
            return $bCloseTag && self::$closeSingleTags ? $sHtml . ' />' : $sHtml . '>';
        else
            return $bCloseTag ? $sHtml . '>' . $sContent . '</' . $sTag . '>' : $sHtml . '>' . $sContent;
    }

    /**
     *
     * Выводит <select>
     *
     * Пример:
     * $arElements = \SB\IBlock::getElements(array(),array('IBLOCK_ID'=>32), false, false, array('ID','NAME'));
     * $arItems = \SB\Tools::getList($arElements,'ID','NAME');
     * echo \SB\Html::select($arItems, array('name'=>'select1'));
     *
     * @param $arItems - список элементов
     *          $arItems[<value>] = <text>
     *          $arItems[<value>] = array('text'=><text>, 'attrs'=><массив атрибутов>).
     * @param $arAttrs - атрибуты <select>
     * @param bool $selected
     *
     * @return string

     * @author V. Shiryakov <02.11.2015, Implementation>
     */
    public static function select($arItems, $arAttrs, $selected = false, $arOptionsAttrs = array())
    {
        $options = '';

        foreach($arItems as $value=>$mItem)
        {
            $arOptionAttrs = array();

            if (is_array($mItem))
            {
                $sText = $mItem['text'];
                $arOptionAttrs = Tools::get($mItem, 'attrs', array());
            }
            else
            {
                $sText = $mItem;
            }

            $arOptionAttrs['value'] = $value;

            if ($selected!==false && $selected==$value)
            {
                $arOptionAttrs['selected'] = true;
            }

            // Если переданы параметры для options, то мерджим их
            if (array_key_exists($value, $arOptionsAttrs))
            {
                $arOptionAttrs = array_merge($arOptionAttrs, $arOptionsAttrs[$value]);
            }

            $options .= self::tag('option', $sText, $arOptionAttrs);
        }

        $options =  self::tag('select', $options, $arAttrs);

        return $options;
    }
}