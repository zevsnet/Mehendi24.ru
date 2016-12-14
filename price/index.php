<?require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Услуги маникюра, педикюру, рисования хной");
$APPLICATION->SetPageProperty("keywords", "мехенди, фрэнч, маникюр, педикюр, хна, рисование, хной");?>
<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    Array(
        "AREA_FILE_SHOW" => "file",
        "PATH" => "/include_arear/index/price.php",
        "EDIT_TEMPLATE" => ""
    ),
    false
);?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>