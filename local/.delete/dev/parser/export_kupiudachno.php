<?
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . "/../../..");
//echo $_SERVER["DOCUMENT_ROOT"];
//die();
@set_time_limit(0);
@ignore_user_abort(true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
/*
 * Загружаем фотографии с  сайта kupiudachno.ru
 */

$query_Search = "http://www.kupiudachno.ru/search/result.html?setsearchdata=1&category_id=0&search_type=all&search=";
$domane_Site = "http://www.kupiudachno.ru";

$IBLOCK_ID = 66;

require_once('phpQuery/phpQuery.php');

$arQuery = array();
$arQuery = getArrBase($IBLOCK_ID);
$arResult = array();
$f_status = 0;
foreach($arQuery as $key => $query)
{
    $query['NAME'] = str_replace('&quot;', '', $query['NAME']);
    $query['NAME'] = str_replace("'", "%27", $query['NAME']);

    $url = $query_Search . str_replace(" ", "+", $query['NAME']);
    //$url = str_replace('&quot;', '', $url);
    $arLink = array();
    $arLink['ID'] = $query['ID'];
    $arLink['LINK'] = getArrLink_Search($url, $query['NAME'], $domane_Site);
    if($arLink['LINK'])
    {
        $arLink['IMAGES'] = getArrImages($arLink['LINK'], $domane_Site);

        if($arLink['IMAGES'])
        {
            SetUpdateProduct($arLink);
            $f_status++;
        }
    }
}
echo count($arQuery) . '<br/> ' . $f_status;

/*
 *Находим ссылку на товар
 */
function getArrLink_Search($url, $text_find = '', $domain = '')
{
    $offSite = file_get_contents($url);
    _::d($url);
    phpQuery::newDocument($offSite);
    $exportItem = array();
    foreach(pq('.product_title a') as $a)
    {
        //$strName = str_replace(array('"','0'), '', pq($a)->html());
        $strName = str_replace("'", "%27", pq($a)->html());
        if($strName == $text_find)
        {
            $exportItem = $domain . pq($a)->attr('href');
        }
    }

    return $exportItem;
}

/*
 * Вернет Артикл
 */
function getArticl($url)
{
    $url = str_replace(array('\\'), "/", $url);
    $offSite = file_get_contents($url);

    phpQuery::newDocument($offSite);
    $f_date = false;
    foreach(pq('.product__specs tbody tr td') as $td)
    {
        $text = pq($td)->html();
        if($f_date)
        {
            return $text;
        }
        if($text == 'Артикул (SKU)')
        {
            $f_date = true;
        }
    }

    return false;
}

/*
 *Вытягиваем картинки 
 */
function getArrImages($arLink, $domain)
{
    $offSite = file_get_contents($arLink);

    phpQuery::newDocument($offSite);
    $exportItem = array();

    foreach(pq('.lightbox') as $lightbox)
    {

        $exportItem[] = pq($lightbox)->attr('href');
    }

    return $exportItem;
}

/*
 *
 */
function getArrBase($IBLOCK_ID)
{
    CModule::IncludeModule('iblock');
    $arSelect = Array("ID", "NAME");
    $arFilter = Array(
        "IBLOCK_ID"           => $IBLOCK_ID,
        "SECTION_ID"          => $_REQUEST['SECTION_ID'],
        "INCLUDE_SUBSECTIONS" => "Y",
        "ACTIVE"              => "Y",
        /*"DETAIL_PICTURE"      => false*/
        "PROPERTY_MORE_PHOTO_VALUE"      => false
    );
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    $arQuery = array();
    while($ob = $res->GetNextElement())
    {
        $arFields = $ob->GetFields();
        $arQuery[] = array(
            "ID"   => $arFields['ID'],
            "NAME" => $arFields['NAME'],
        );
    }

    return $arQuery;
}

/*
 *
 */
function SetUpdateProduct($product)
{
    CModule::IncludeModule('iblock');
    $DetailImages = '';
    $arImages = array();

    foreach($product['IMAGES'] as $key => $item)
    {

        $arImages[] = CFile::MakeFileArray($item);
        if($key == 0)
        {
            $DetailImages = $item;
        }
    }

    $el = new CIBlockElement;

    $PROP = array();
    $PROP['MORE_PHOTO'] = $arImages;

    $arLoadProductArray = Array(
        "PROPERTY_VALUES" => $PROP,
        "DETAIL_PICTURE"  => $PROP['MORE_PHOTO'][0]/*CFile::MakeFileArray($DetailImages)*/
    );
    //print_r($arLoadProductArray);
    $res = $el->Update($product['ID'], $arLoadProductArray);
}

/*
 * Открываем на чтение файл и записываем его в массив
 */
function getArrData($url_file)
{
    $arRes = array();
    $fille = fopen($_SERVER["DOCUMENT_ROOT"] . $url_file, "r");
    while(($data = fgetcsv($fille, 1000, ";")) !== false)
    {
        $arRes[] = $data;
    }
    fclose($fille);

    return $arRes;
}

?>