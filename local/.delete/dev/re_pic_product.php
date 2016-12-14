<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
return 0;
try
{
    $finishID=80587;


    for($ID = 80547; $ID <= $finishID; $ID++)
    {
        $LINK = "http://nailmag.ru/image/cache/data/goods/" . $ID . "-294x294.jpg";

        $file_headers = @get_headers($LINK);
        if($file_headers[0] == 'HTTP/1.1 404 Not Found')
        {
            $exists = false;
        }
        else
        {
            $exists = true;
        }

        if($exists == 1)
        {
            $arID = getID("%" . $ID);
            if($arID)
                setNewPic($LINK, $arID);
        }
    }
    _::d('FINISH');
}
catch(Exception $e)
{
}

function getID($name)
{
    $IBLOCK_ID = 66;
    CModule::IncludeModule('iblock');
    Cmodule::IncludeModule('catalog');
    $arSelect = Array("ID", "NAME", "DETAIL_TEXT");
    $arFilter = Array(
        "IBLOCK_ID"           => $IBLOCK_ID,
        "ACTIVE"              => "Y",
        "SECTION_ID"          => 1261,
        "INCLUDE_SUBSECTIONS" => "Y",
        "NAME"                => $name
    );

    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

    while($ob = $res->GetNextElement())
    {
        $arFields = $ob->GetFields();

        return $arFields['ID'];
    }

    return false;
}

function setNewPic($url, $id)
{
    $el = new CIBlockElement;
$filePic = CFile::MakeFileArray($url);
    $arLoadProductArray = Array(
        "DETAIL_PICTURE" => $filePic,
        "PREVIEW_PICTURE" =>$filePic
    );

    $res = $el->Update($id, $arLoadProductArray);
}

?>