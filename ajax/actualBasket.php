<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("iblock")){
	echo "failure";
	return;
}

$dbBasketItems = CSaleBasket::GetList(array("NAME" => "ASC", "ID" => "ASC"), array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"), false, false, array("ID", "PRODUCT_ID", "DELAY", "SUBSCRIBE", "CAN_BUY", "TYPE", "SET_PARENT_ID"));

$basket_items = array();
$delay_items = array();
$subscribe_items = array();
$arItems=array();
global $compare_items;
if(!is_array($compare_items)){
	$compare_items =array();
	if(isset($_GET["iblockID"])){
		$compare_items = array_keys($_SESSION["CATALOG_COMPARE_LIST"][$_GET["iblockID"]]["ITEMS"]);
	}
}

while($arBasketItems = $dbBasketItems->GetNext()){
	if(CSaleBasketHelper::isSetItem($arBasketItems)) // set item
		continue;
	if($arBasketItems["DELAY"]=="N" && $arBasketItems["CAN_BUY"] == "Y" && $arBasketItems["SUBSCRIBE"] == "N"){
		$basket_items[] = $arBasketItems["PRODUCT_ID"];
	}
	elseif($arBasketItems["DELAY"]=="Y" && $arBasketItems["CAN_BUY"] == "Y" && $arBasketItems["SUBSCRIBE"] == "N"){
		$delay_items[] = $arBasketItems["PRODUCT_ID"];
	}
	elseif($arBasketItems["SUBSCRIBE"]=="Y"){
		$subscribe_items[] = $arBasketItems["PRODUCT_ID"];
	}
}
$arItems["BASKET"]=array_flip($basket_items);
$arItems["DELAY"]=array_flip($delay_items);
$arItems["SUBSCRIBE"]=array_flip($subscribe_items);
$arItems["COMPARE"]=array_flip($compare_items);

?>
<script type="text/javascript">
	var arBasketAspro = <? echo CUtil::PhpToJSObject($arItems, false, true); ?>;
</script>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>