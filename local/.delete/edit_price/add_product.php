<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("catalog");
return;
$file_open = getArrData($_SERVER["DOCUMENT_ROOT"] . '/local/edit_price/list.csv');
foreach($file_open as $link)
{
    _::d($link);

    $el = new CIBlockElement;

    $arLoadProductArray = Array(
        "MODIFIED_BY"    => $USER->GetID(), // ������� ������� ������� �������������
        "IBLOCK_SECTION_ID" => 1328,          // ������� ����� � ����� �������
        "IBLOCK_ID"      => 66,

        "NAME"           => $link[0],
        "ACTIVE"         => "Y",            // �������

        "DETAIL_PICTURE" => CFile::MakeFileArray('http:'.$link[1])
    );

    if($PRODUCT_ID = $el->Add($arLoadProductArray))
        echo "New ID: ".$PRODUCT_ID;
    else
        echo "Error: ".$el->LAST_ERROR;

}




/*
 * ��������� �� ������ ���� � ���������� ��� � ������
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
?>