<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<?$arViewedIDs=CMShop::getViewedProducts((int)CSaleBasket::GetBasketUserID(false), SITE_ID);
if($arViewedIDs){?>
	<div class="similar_products_wrapp">
		<?$GLOBALS['arrFilterViewed'] = array( "ID" => $arViewedIDs );?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:catalog.top", 
			"products_slider_viewed", 
			array(
				"TITLE_BLOCK" => GetMessage("VIEWED_BEFORE"),
				"IBLOCK_TYPE" => "aspro_mshop_catalog",
				"IBLOCK_ID" => "#IBLOCK_ID#",
				"FILTER_NAME" => "arrFilterViewed",
				"ELEMENT_SORT_FIELD" => "sort",
				"ELEMENT_SORT_ORDER" => "asc",
				"ELEMENT_SORT_FIELD2" => "id",
				"ELEMENT_SORT_ORDER2" => "asc",
				"SECTION_URL" => "",
				"DETAIL_URL" => "",
				"BASKET_URL" => $arParams["BASKET_URL"],
				"ACTION_VARIABLE" => "action",
				"PRODUCT_ID_VARIABLE" => "id",
				"SECTION_ID_VARIABLE" => "",
				"PRODUCT_QUANTITY_VARIABLE" =>"quantity",
				"PRODUCT_PROPS_VARIABLE" => "prop",
				"DISPLAY_COMPARE" => "Y",
				"DISPLAY_WISH_BUTTONS" => "Y",
				"ELEMENT_COUNT" => "10",
				"SHOW_MEASURE" => "Y",
				"LINE_ELEMENT_COUNT" => 3,
				"PROPERTY_CODE" => array(
					0 => "HIT",
					1 => "",
				),
				"PRICE_CODE" => array(
					0 => "BASE",
				),
				"USE_PRICE_COUNT" => "N",
				"PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
				"USE_PRODUCT_QUANTITY" => "N",
				"ADD_PROPERTIES_TO_BASKET" => "N",
				"PARTIAL_PRODUCT_PROPERTIES" => "N",
				"PRODUCT_PROPERTIES" => array(
				),
				"SHOW_PRICE_COUNT" => "1",
				"PRICE_VAT_INCLUDE" => "Y",
				"CONVERT_CURRENCY" => "Y",
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "86400",
				"CACHE_GROUPS" => "N",
				"CACHE_FILTER" => "Y",
				"OFFERS_CART_PROPERTIES" => array(
				),
				"OFFERS_FIELD_CODE" => array(
					0 => "ID",
					1 => "",
				),
				"OFFERS_PROPERTY_CODE" => array(
					0 => "",
				),
				"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
				"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
				"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
				"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
				"OFFERS_LIMIT" => "10",
				"CURRENCY_ID" => "RUB",
				"HIDE_NOT_AVAILABLE" => "Y",
				"VIEW_MODE" => (isset($arParams["TOP_VIEW_MODE"])?$arParams["TOP_VIEW_MODE"]:""),
				"ROTATE_TIMER" => (isset($arParams["TOP_ROTATE_TIMER"])?$arParams["TOP_ROTATE_TIMER"]:""),
				"TEMPLATE_THEME" => (isset($arParams["TEMPLATE_THEME"])?$arParams["TEMPLATE_THEME"]:""),
				"LABEL_PROP" => $arParams["LABEL_PROP"],
				"ADD_PICT_PROP" => $arParams["ADD_PICT_PROP"],
				"PRODUCT_DISPLAY_MODE" => $arParams["PRODUCT_DISPLAY_MODE"],
				"OFFER_ADD_PICT_PROP" => $arParams["OFFER_ADD_PICT_PROP"],
				"OFFER_TREE_PROPS" => $arParams["OFFER_TREE_PROPS"],
				"PRODUCT_SUBSCRIPTION" => $arParams["PRODUCT_SUBSCRIPTION"],
				"SHOW_DISCOUNT_PERCENT" => "Y",
				"SHOW_OLD_PRICE" => "Y",
				"MESS_BTN_BUY" => $arParams["MESS_BTN_BUY"],
				"MESS_BTN_ADD_TO_BASKET" => $arParams["MESS_BTN_ADD_TO_BASKET"],
				"MESS_BTN_SUBSCRIBE" => $arParams["MESS_BTN_SUBSCRIBE"],
				"MESS_BTN_DETAIL" => $arParams["MESS_BTN_DETAIL"],
				"MESS_NOT_AVAILABLE" => $arParams["MESS_NOT_AVAILABLE"],
				"ADD_TO_BASKET_ACTION" => $basketAction,
				"SHOW_CLOSE_POPUP" => isset($arParams["COMMON_SHOW_CLOSE_POPUP"])?$arParams["COMMON_SHOW_CLOSE_POPUP"]:"",
				"COMPARE_PATH" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
				"COMPONENT_TEMPLATE" => "products_slider_viewed",
				"SEF_MODE" => "N"
			),
			false
		);?>
	</div>
<?}?>