<?require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Записаться к мастеру на маникюр или педикюр");
$APPLICATION->SetPageProperty("keywords", "френч,френч на ногтях,френч с рисунком");
$APPLICATION->SetPageProperty("title", "Записаться");
$APPLICATION->SetTitle("Записаться на услугу");?>
<?$APPLICATION->IncludeComponent(
    "bitrix:main.include",
    "",
    Array(
        "AREA_FILE_SHOW" => "file",
        "PATH" => "/include_arear/index/service.php",
        "EDIT_TEMPLATE" => ""
    ),
    false
);?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>