<?
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . "/../../..");
//echo $_SERVER["DOCUMENT_ROOT"];
//die();
//@set_time_limit(0);
//@ignore_user_abort(true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
/*
 * Загружаем фотографии с  сайта kupiudachno.ru
 */
$domane_Site = "http:/instrument-husqvarna.ru/";
$DIR_LOCATION_FOLDER = $_SERVER["DOCUMENT_ROOT"] . '/local/dev/parser_negrabli/';
require_once($DIR_LOCATION_FOLDER . 'phpQuery/phpQuery.php');
require_once($DIR_LOCATION_FOLDER . 'function.php');
$arQuery = array();
$arQuery = openFileCSV_ROW($_SERVER["DOCUMENT_ROOT"] . '/local/dev/parser_negrabli/link_export.csv',$_REQUEST['C_ROW'],$_REQUEST['C_ROW_MAX']);
//$arQuery = openFileCSV($_SERVER["DOCUMENT_ROOT"] . '/local/dev/parser_negrabli/link_export.csv');
$arResult = array();

foreach($arQuery as $row => $arCol)
{

    $arResult = parserPage($arCol[0], $domane_Site);
    if($arResult)
    {
        file_put_contents('Result.csv', $arResult, FILE_APPEND);
    }

    unset($arResult);
    unset($arQuery[$row]);
}
unset($arQuery);
echo 'FINISH';
?>