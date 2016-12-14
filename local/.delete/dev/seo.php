<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$IBLOCK_ID = 66;
CModule::IncludeModule('iblock');
    $arSelect = Array("ID", "NAME","DETAIL_TEXT");
    $arFilter = Array("IBLOCK_ID" => $IBLOCK_ID, "ACTIVE" => "Y","PROPERTY_SEO_DESCRIPTION"=>false);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    $arQuery = array();
	$rus = array('а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
    while($ob = $res->GetNextElement())
    {
        $arFields = $ob->GetFields();
		$start_sub = multineedle_stripos($arFields['DETAIL_TEXT'],$rus);
		if($start_sub >=120){
            $start_sub=1;
        }
        $text_tmp = substr($arFields['DETAIL_TEXT'],$start_sub-1,strlen($arFields['DETAIL_TEXT']));
		$finish_sub = multineedle_stripos($text_tmp,array("!","."));

		$new_text = substr($text_tmp,0,$finish_sub+1);
		
		
		unset($start_su);
		unset($text_tmp);
		unset($finish_sub);

		
		if($new_text){
            _::d($new_text);
			//CIBlockElement::SetPropertyValuesEx($arFields['ID'], false, array("SEO_DESCRIPTION" => $arFields['NAME'].' - '.$new_text ));
		}else
		{
			_::d($new_text = str_Replace('&quot;','',$arFields['NAME']));
			CIBlockElement::SetPropertyValuesEx($arFields['ID'], false, array("SEO_DESCRIPTION" =>$new_text ));
			
		}
			//_::d($arFields['ID'],$new_text);
    }
unset($rus);

_::d('FINISH');
function multineedle_stripos($haystack, $needles, $offset=0) {
    foreach($needles as $needle) {
        $found[$needle] = stripos($haystack, $needle, $offset);
    }

	$min = 120;
	foreach($found as $item) {
		if($item !=false)
			if($item < $min){$min =$item;}
	}
	
    return $min;
}

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