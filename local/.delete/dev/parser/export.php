<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
/*
 * Загружаем фотографии с  сайта triya.com
 */

$query_Search = "https://www.triya.ru/search/index.php?q=";
$domane_Site = "https://www.triya.ru";

$IBLOCK_ID = 17;

require_once('phpQuery/phpQuery.php');

$arQuery = array();
$arQuery = getArrBase($IBLOCK_ID);
//_::dd($arQuery);
//$arQuery[] = array("ID"=>98945, "NAME" => 'Двухъярусная кровать Индиго №4');

foreach($arQuery as $key => $query)
{
    $url = $query_Search . str_replace(" ", "+", $query['NAME']);

    $arLink_search = getArrLink_Search($url, $query['NAME'], $domane_Site);

    $arLink_SRC = getArrImages($arLink_search[0], $domane_Site);

    $arQuery[$key]['SRC'] = $arLink_SRC;
_::d($arQuery);
    SetUpdateProduct($arQuery[$key]);
    if($key ==1){break;}
}

_::d('FINISH');
function getArrLink_Search($url, $text_find, $domain)
{
    $offSite = file_get_contents($url);

    phpQuery::newDocument($offSite);
    $exportItem = array();
    foreach(pq('.resize-gap li') as $li)
    {
        $pqA = pq($li)->find('.name');
        $strLINK = $domain . pq($pqA)->attr('href');
        $strName = trim(str_replace(array('"','«','»'), '', pq($pqA)->html()));
        if($strName == $text_find)
            $exportItem[] = array("NAME" => $strName, "LINK" => $strLINK);
    }

    return $exportItem;
}

function getArrImages($arLink, $domain)
{

    $offSite = file_get_contents($arLink['LINK']);

    phpQuery::newDocument($offSite);
    $exportItem = array();

    foreach(pq('.swiper-wrapper .more-photo-thumb-sm') as $img)
    {
        $SRC = $domain . pq($img)->attr('data-image');
        $exportItem[] = array("SRC" => $SRC);
    }
    if(count($exportItem) == 0)
    {
        $SRC = $domain . pq('.product-photo-image')->attr('src');
        $exportItem[] = array("SRC" => $SRC);
    }

    return $exportItem;
}

function getArrBase($IBLOCK_ID)
{
    CModule::IncludeModule('iblock');
    $arSelect = Array("ID", "NAME");
    $arFilter = Array("IBLOCK_ID" => $IBLOCK_ID, "SECTION_ID"=>862,"PROPERTY_CML2_MANUFACTURER" => 21, "INCLUDE_SUBSECTIONS" => "Y", "ACTIVE" => "Y");
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

function SetUpdateProduct($product)
{
    CModule::IncludeModule('iblock');
    $DetailImages = '';
    $arImages = array();
    foreach($product['SRC'] as $key => $item)
    {
        $arImages[] = CFile::MakeFileArray($item['SRC']);
        if($key == 0)
        {
            $DetailImages = $item['SRC'];
        }
    }

    $el = new CIBlockElement;

    $PROP = array();
    $PROP['MORE_PHOTO'] = $arImages;

    $arLoadProductArray = Array(
        "PROPERTY_VALUES" => $PROP,
        "DETAIL_PICTURE"  => CFile::MakeFileArray($DetailImages)
    );

    $res = $el->Update($product['ID'], $arLoadProductArray);
}

?>