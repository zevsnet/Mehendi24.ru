<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$IBLOCK_ID = 66;

Cmodule::IncludeModule('catalog');
   /* $arSelect = Array("ID", "NAME","DETAIL_TEXT");
    $arFilter = Array("IBLOCK_ID" => $IBLOCK_ID, "ACTIVE" => "Y","SECTION_ID" => 1328, "INCLUDE_SUBSECTIONS" => "Y");
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    
    while($ob = $res->GetNextElement())
    {
        $arFields = $ob->GetFields();

    }
*/
$PRODUCT_ID = 11559; // id товара
$arFields = array('QUANTITY' => 10);
CCatalogProduct::Update($PRODUCT_ID, $arFields);

_::d('FINISH');

?>