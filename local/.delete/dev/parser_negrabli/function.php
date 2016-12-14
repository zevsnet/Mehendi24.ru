<?

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
        "IBLOCK_ID"                 => $IBLOCK_ID,
        "SECTION_ID"                => $_REQUEST['SECTION_ID'],
        "INCLUDE_SUBSECTIONS"       => "Y",
        "ACTIVE"                    => "Y",
        /*"DETAIL_PICTURE"      => false*/
        "PROPERTY_MORE_PHOTO_VALUE" => false
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
function openFileCSV($url_file)
{
    $arRes = array();
    $fille = fopen($url_file, "r");
    while(($data = fgetcsv($fille, 1000, ";")) !== false)
    {
        $arRes[] = $data;
    }
    fclose($fille);

    return $arRes;
}

/*
 * Открываем на чтение файл и записываем его в массив
 */
function openFileCSV_ROW($url_file, $start_row = 0, $count_row = 30)
{
    $arRes = array();
    $fille = fopen($url_file, "r");
    $c_Row = 0;
    $c_Stop = 1;
    while(($data = fgetcsv($fille, 1000, ";")) !== false)
    {
        if($c_Row >= $start_row)
        {
            $arRes[] = $data;
            $c_Stop++;
        }

        if($c_Stop >= $count_row)
        {
            break;
        }
        $c_Row++;
    }
    fclose($fille);

    return $arRes;
}

/*
 *Вытягиваем картинки + таблицу articl
 */
function parserPage($arLink, $domain = '')
{
    $document = phpQuery::newDocument(file_get_contents($arLink));
    $hentry = $document->find('.page-table tbody tr');
    unset($document);

    $exportItem = array();

    foreach($hentry as $tr)
    {
        $arItem = array();
        foreach(pq($tr)->find('td') as $key => $td)
        {
            switch($key)
            {
                case 0:
                    $arItem['ID_PIC'] = pq($td)->html();
                    break;
                case 1:
                    $arItem['ARTICLE'] = pq(pq($td)->find('a'))->html();
                    break;
                case 2:
                    $arItem['DES'] = '"' . pq($td)->html() . '"';
                    break;
            }
        }
        //$exportItem['ITEMS'][] = $arItem;
        $exportItem[] = $arItem['ARTICLE'] . ';' . $arItem['ID_PIC'] . ';' . $domain . pq('.docs-pictures img')->attr('src') . "\n";
        unset($arItem);
    }

    unset($hentry);
    if($exportItem)
    {
        return $exportItem;
    }
    else
    {
        return false;
    }
}

?>