<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("catalog");

$arSelect = Array("ID", "NAME");
$arFilter = Array("IBLOCK_ID" => IntVal(66), "SECTION_ID" => 1256, "INCLUDE_SUBSECTIONS" => "Y");
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
$arIdElements = array();

while($ob = $res->GetNextElement())
{
    $arFields = $ob->GetFields();
    $arIdElements[] = $arFields['ID'];
}

foreach($arIdElements as $ID)
{

    $PRODUCT_ID = $ID;
    $PRICE_TYPE_ID = 1;

    $arFields = Array(
        "PRODUCT_ID"       => $PRODUCT_ID,
        "CATALOG_GROUP_ID" => $PRICE_TYPE_ID,
        "PRICE"            => 160,
        "CURRENCY"         => "RUB",
        "QUANTITY_FROM" => 1,
        "QUANTITY_TO " => 10
    );

    $res = CPrice::GetList(array(), array(
            "PRODUCT_ID"       => $PRODUCT_ID,
            "CATALOG_GROUP_ID" => $PRICE_TYPE_ID
        ));

    if($arr = $res->Fetch())
    {
        CPrice::Update($arr["ID"], $arFields);

    }
    else
    {
        CPrice::Add($arFields);
    }
    //CCatalogProduct::Update($ID, array('QUANTITY' => 10));
}

_::d('FINISH Upload price');

?>