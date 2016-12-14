<?
@set_time_limit(0);
@ignore_user_abort(true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once('classes/phpQuery/phpQuery.php');

$file_open = getArrData($_SERVER["DOCUMENT_ROOT"] . '/local/dev/link.csv');
foreach($file_open as $link)
{
    $str_link = getStatus($link[0]);
    //$str_link = getStatusCurl($link[0]);
    if($str_link)
        file_put_contents('status.csv', $str_link . "\r\n", FILE_APPEND);
}

_::d("FINISH");
//_::d($file_open);

/*
 *Находим ссылку на товар
 */
function getArrLink_Search($url, $domain = '')
{
    $offSite = file_get_contents($url);
    phpQuery::newDocumentHTML($offSite);
    $exportItem = array();
    foreach(pq('.name a.gray-link') as $a)
    {
        $exportItem[] = pq($a)->attr('href');
    }

    return $exportItem;
}

function getArrLink($url, $domain = '')
{
    $offSite = file_get_contents($url);
    $temp = phpQuery::newDocumentHTML($offSite);
    $exportItem = array();
    foreach(pq('.mainInset a.red') as $a)
    {
        $domain = pq($a)->attr('href');

        if(filter_var($domain, FILTER_VALIDATE_URL))
        {
            $pos_ = stripos($domain, '/');
            //_::dd($pos_,$domain);
            if($pos_ != strlen($domain) + 1)
            {
                $exportItem[] = $domain . '/restore.php';
            }
            else
            {
                $exportItem[] = $domain . 'restore.php';
            }
        }
    }
    unset($temp);

    return $exportItem;
}

function getStatus($url, $domain = '')
{
    if($url)
    {
        $offSite = file_get_contents($url);
        phpQuery::newDocumentHTML($offSite);
        $exportItem = array();
        foreach(pq('form#restore') as $restore)
        {
            return $url . ' = OK';
        }
    }

    return false;
}

function getStatusCurl($url)
{
    if($url)
    {
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        /* Get the HTML or whatever is linked in $url. */
        $response = curl_exec($handle);

        /* Check for 404 (file not found). */
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        if($httpCode != 404)
        {
            /* Handle 404 here. */
            return $url . ' = OK';
        }
        curl_close($handle);

        return false;
    }

    return false;
}

function getArticl($url)
{
    $url = str_replace(array('\\'), "/", $url);
    $offSite = file_get_contents($url);

    phpQuery::newDocumentHTML($offSite);
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

    phpQuery::newDocumentHTML($offSite);
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
function getArrData($url_file)
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
return;
?>


---------
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->SetTitle("Помощь проекту");
$APPLICATION->SetPageProperty('MAIN_PAGE', 'Y');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_after.php");
?>
    <style>
        #bg {

            height: 695px;
        }
    </style>
    <div class="help_project">
        <p>Сайт «Я помню» существует за счет частных пожертвований и грантов. Мы благодарны всем, кто может финансово поддержать нашу деятелность по сохранению памяти о ветеранах Великой Отечественной войны.<br />
            Ваши пожертвования можно перечислить либо на нашу банковскую карту (с обязательной пометкой "благотворительное пожертвование"), либо на счет на Яндекс-деньгах.</p>
        <div align="center" >
            <? global $USER;
            if($USER->IsAdmin()):?>
                <iframe frameborder="0" allowtransparency="true" scrolling="no" src="https://money.yandex.ru/embed/donate.xml?account=41001206852538&quickpay=donate&payment-type-choice=on&default-sum=100&targets=%D0%B1%D0%BB%D0%B0%D0%B3%D0%BE%D1%82%D0%B2%D0%BE%D1%80%D0%B8%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D0%B5+%D0%BF%D0%BE%D0%B6%D0%B5%D1%80%D1%82%D0%B2%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5&project-name=&project-site=&button-text=05&successURL=" width="508" height="78"></iframe>
            <? else:?>
                <iframe frameborder="0" allowtransparency="true" scrolling="no" src="https://money.yandex.ru/embed/donate.xml?account=410011397464164&quickpay=donate&payment-type-choice=on&default-sum=100&targets=%D0%B1%D0%BB%D0%B0%D0%B3%D0%BE%D1%82%D0%B2%D0%BE%D1%80%D0%B8%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D0%B5+%D0%BF%D0%BE%D0%B6%D0%B5%D1%80%D1%82%D0%B2%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5&project-name=&project-site=&button-text=05&successURL=" width="508" height="78"></iframe>
            <? endif; ?>
        </div>

    </div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>