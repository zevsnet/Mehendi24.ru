<? if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

if(is_array($arResult['ITEMS']) && count($arResult['ITEMS']) > 0)
{
    CModule::IncludeModule('iblock');
    $arrProduct = array();
    foreach($arResult['ITEMS'] as $key => $item)
    {
        //_::d($item);
        //if()

        $arrProduct[] = $item['PROPERTIES']['Z_PRODUCT']['VALUE'];

    }
    $IBLOCK_ID = 11;
    $arSelect = Array('NAME', 'DETAIL_PAGE_URL', 'PREVIEW_PICTURE', 'DETAIL_PICTURE','PROPERTIES_92','PROPERTIES_99','PROPERTIES_100');
    $arFilter = Array("IBLOCK_ID" => $IBLOCK_ID, "ID" => $arrProduct);
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    $arrRes = array();
    while($ob = $res->GetNextElement())
    {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperty();
        $arTempPic = array();
        if($arFields['DETAIL_PICTURE'])
        {
            $arTempPic = $arFields['DETAIL_PICTURE'];
        }
        elseif($arFields['PREVIEW_PICTURE'])
        {
            $arTempPic = $arFields['PREVIEW_PICTURE'];
        }
        $arWaterMark = array(
            array(
                "name"     => "watermark",
                "position" => "bc",
                "file"     => $_SERVER["DOCUMENT_ROOT"] . "/img/water_name_site.png"
            )
        );
        $file = CFile::ResizeImageGet($arTempPic, array(
            'width'  => 150,
            'height' => 150
        ), BX_RESIZE_IMAGE_PROPORTIONAL, true, $arWaterMark);
        $DETAIL_PICTURE = array();
        $DETAIL_PICTURE['SRC'] = $file['src'];
        $DETAIL_PICTURE['WIDTH'] = $file['width'];
        $DETAIL_PICTURE['HEIGHT'] = $file['height'];
        $arFields['DETAIL_PICTURE'] = $DETAIL_PICTURE;

        $arrRes[$arFields['ID']] = $arFields;
        //_::d($arFields);
      //  $arrRes[$item['PROPERTIES']['Z_PRODUCT']['VALUE']]['PROPERTIES']['CML2_ARTICLE']['VALUE']
    }

    foreach($arResult['ITEMS'] as $key => $item)
    {
        $arResult['ITEMS'][$key]['PROPERTIES']['CML2_ARTICLE']['VALUE']=$arrRes[$item['PROPERTIES']['Z_PRODUCT']['VALUE']]['PROPERTIES']['CML2_ARTICLE']['VALUE'];
        $arResult['ITEMS'][$key]['PROPERTIES']['CML2_MANUFACTURER']['VALUE']=$arrRes[$item['PROPERTIES']['Z_PRODUCT']['VALUE']]['PROPERTIES']['CML2_MANUFACTURER']['VALUE'];
        $arResult['ITEMS'][$key]['PROPERTIES']['STRANA']['VALUE']=$arrRes[$item['PROPERTIES']['Z_PRODUCT']['VALUE']]['PROPERTIES']['STRANA']['VALUE'];

        $arResult['ITEMS'][$key]['DETAIL_PICTURE'] = $arrRes[$item['PROPERTIES']['Z_PRODUCT']['VALUE']]['DETAIL_PICTURE'];
        $arResult['ITEMS'][$key]['PREVIEW_PICTURE'] = $arrRes[$item['PROPERTIES']['Z_PRODUCT']['VALUE']]['DETAIL_PICTURE'];
        $arResult['ITEMS'][$key]['DETAIL_PAGE_URL'] = $arrRes[$item['PROPERTIES']['Z_PRODUCT']['VALUE']]['DETAIL_PAGE_URL'];
    }
}
?>

