<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); ?>
<?
$cIP = new IP\ip_status();
$cIP->find_ip();
?>