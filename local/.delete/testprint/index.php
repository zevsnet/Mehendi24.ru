<?

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once("orderprint.php");

$obj = new CASDOrderPrintUtil();

echo '<pre>';
print_r($obj->GetPrintReports());
echo '</pre>';
?>