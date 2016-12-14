<?php
use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
CModule::AddAutoloadClasses(
	'blackbutterfly.seoredirektprod7',
	array(
		'BLACKSeoredirekt' => 'classes/general/seoredirekt.php',
	)
);
Class Blackbutterflyseoredirektprod7
{
	function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
	{
		$MODULE_ID = basename(dirname(__FILE__));
		$RIGHT = $GLOBALS['APPLICATION']->GetGroupRight($MODULE_ID);
		if ($RIGHT > "D") {
			$bmenu = array(
				"parent_menu" => "global_menu_blackbutterfly",
				"section" => $MODULE_ID,
				"sort" => 2,
				"text" => Loc::getMessage($MODULE_ID.'_MODULES'),
				"title" => Loc::getMessage($MODULE_ID.'_MODULES'),
				"icon" => "seoredirectpro_menu_icon",
				"page_icon" => "seoredirectpro_menu_icon",
				"items_id" => $MODULE_ID."_items",
				"more_url" => array(),
				"items" => array()
			);
				$arTitle = array(
					"seoredirekt__list.php",
					"seoredirekterror__list.php",
					"seoredirektignore__list.php",
					"generator__list.php",
					"import__list.php",
				);
				foreach($arTitle as $item) {
					 if ( substr_count($item,"__list.php") > 0 ) {
					    $itemss = str_replace('__list','__edit',$item);
						$itemssname = explode('__list',$item);
						$bmenu['items'][] = array(
							'text' => Loc::getMessage($MODULE_ID."_".strtoupper($itemssname[0])),
							'url' => $MODULE_ID.'_'.$item,
							'module_id' => $MODULE_ID,
							"icon" => "iblock_menu_icon_iblocks",
							"title" => "",
							'more_url'    => array(
									$MODULE_ID.'_'.$itemss
							),
						);
					 }
				}
			if ( $GLOBALS['APPLICATION']->GetGroupRight('main') > "P") {
				$bmenu['items'][] = array(
							'text' => Loc::getMessage($MODULE_ID."_OPTIOBS"),
							"url" => "settings.php?lang=ru&mid=".$MODULE_ID,
							"icon" => "iblock_menu_icon_settings",
							'module_id' => $MODULE_ID,
							"title" => "",
						);				
			}
			$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/js/blackbutterfly/'.$MODULE_ID.'/seoredirectpro.css');
		if(!isset($aGlobalMenu["global_menu_blackbutterfly"])) {
			$aGlobalMenu["global_menu_blackbutterfly"] = array(
				"menu_id" => "global_menu_blackbutterfly",
				"page_icon" => "statistics_title_icon",
				"index_icon" => "statistics_page_icon",
				"text" => Loc::getMessage($MODULE_ID.'_COMPANY'),
				"title" => Loc::getMessage($MODULE_ID.'_COMPANY'),
				"icon" => "blackbutterfly_menu_icon",
				"sort" => 120,
				"items_id" => "global_menu_blackbutterfly",
				"help_section" => "global_menu_blackbutterfly",
				"items" => $aMenu,
			);
			$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/js/blackbutterfly/'.$MODULE_ID.'/menugl.css');
		}
		$aModuleMenu[] = $bmenu;
	  }
	}
}