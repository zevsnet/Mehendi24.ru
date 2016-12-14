<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
//$arSection = array(1305,1304,1296,1297,1316,1347,1250,1251,1252,1253,1254,1273,1274,1275,1276,1277,1291,1292,1327,1287,1299,1300,1301,1302,1294);
//$arSection = array(1257,1258,1259,1260,1261,1262,1263,1264,1265,1266,1267,1268,1269,1270,1271,1348);
$arSection = array(1310);
foreach($arSection as $id_section)
{

    $arSelect = Array();
    $arFilter = Array("IBLOCK_ID" => 66, "SECTION_ID" => $id_section, "INCLUDE_SUBSECTIONS" => 'Y');
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    $arResult = array();
    while($ob = $res->GetNextElement())
    {
        $arFields = $ob->GetFields();

        $arProps = $ob->GetProperties();
        $arResult[] = $arFields['ID'];
    }

    foreach($arResult as $id)
    {

        $arID = array();
        $arID = getIdAlL($arResult, $id);
        $prop = array("ASSOCIATED" => $arID);
        CIBlockElement::SetPropertyValuesEx($id, false, $prop);
    }
}
_::d('FINISH');
?>
<?
function getIdAlL($arResult, $id)
{
    $res = array();
    foreach($arResult as $item)
    {
        if($id != $item)
        {
            $res[] = $item;
        }
    }

    return $res;
}

?>
