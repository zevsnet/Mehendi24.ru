<?
use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
@define("MODULE_ID", basename(dirname(__FILE__)));
// подключение модулей
Bitrix\Main\Loader::includeModule(MODULE_ID);
require_once(str_replace("options.php","CModuleOptions.php",__FILE__));

		$showRightsTab = true;
		$arSel = array('REFERENCE_ID' => array(0, 1, 2), 'REFERENCE' => array(GetMessage("blackbutterfly.seoredirektprod7_SETTINGS_404_WWWNOTWWW_VAR1"), GetMessage("blackbutterfly.seoredirektprod7_SETTINGS_404_WWWNOTWWW_VAR2"), GetMessage("blackbutterfly.seoredirektprod7_SETTINGS_404_WWWNOTWWW_VAR3")));
		$arTabs = array(
		   array(
			  'DIV' => 'edit1',
			  'TAB' => GetMessage("blackbutterfly.seoredirektprod7_SETTINGS_NAME"),
			  'ICON' => '',
			  'TITLE' => GetMessage("blackbutterfly.seoredirektprod7_SETTINGS_NAME")
		   )
		);
		$arGroups = array(
		   'SEOREDIREKTPROD7' => array('TITLE' => GetMessage("blackbutterfly.seoredirektprod7_SETTINGS"), 'TAB' => 0),
		);
		$arOptions = array(
		 'SEOPRO_ELEMENT_GET' => array(
			  'GROUP' => 'SEOREDIREKTPROD7',
			  'TITLE' => GetMessage("blackbutterfly.seoredirektprod7_SETTINGS_ELEMENT_GET"),
			  'TYPE' => 'CHECKBOX',
			  'REFRESH' => 'N',
			  'SORT' => '1'
		   ),   
		'SEOPRO_404_GET' => array(
			  'GROUP' => 'SEOREDIREKTPROD7',
			  'TITLE' => GetMessage("blackbutterfly.seoredirektprod7_SETTINGS_404_GET"),
			  'TYPE' => 'CHECKBOX',
			  'REFRESH' => 'N',
			  'SORT' => '2'
		   ),      
		'SEOPRO_404_PAGEURL' => array(
			  'GROUP' => 'SEOREDIREKTPROD7',
			  'TITLE' => GetMessage("blackbutterfly.seoredirektprod7_SETTINGS_404_PAGEURL"),
			  'TYPE' => 'STRING',
			  'SORT' => '3',
			  'NOTES' => GetMessage("blackbutterfly.seoredirektprod7_SETTINGS_404_PAGEURL_NOTES")
		   ),
		'SEOPRO_404_WWWNOTWWW' => array(
			  'GROUP' => 'SEOREDIREKTPROD7',
			  'TITLE' => GetMessage("blackbutterfly.seoredirektprod7_SETTINGS_404_WWWNOTWWW"),
			  'TYPE' => 'SELECT',
			  'SORT' => '4',
			  'VALUES' => $arSel
		   ), 
/*		   
		'SEOPRO_NOTINDEX' => array(
			  'GROUP' => 'SEOREDIREKTPROD7',
			  'TITLE' => GetMessage("blackbutterfly.seoredirektprod7_SETTINGS_NOTINDEX"),
			  'TYPE' => 'CHECKBOX',
			  'REFRESH' => 'N',
			  'SORT' => '5'
		   ),
		'SEOPRO_NOSLESH' => array(
			  'GROUP' => 'SEOREDIREKTPROD7',
			  'TITLE' => GetMessage("blackbutterfly.seoredirektprod7_SETTINGS_NOSLESH"),
			  'TYPE' => 'CHECKBOX',
			  'REFRESH' => 'N',
			  'SORT' => '6'
		   ),	
*/		   
		);		
		
		
/*
онструктор класса CModuleOptions
$module_id - ID модуля
$arTabs - массив вкладок с параметрами
$arGroups - массив групп параметров
$arOptions - собственно сам массив, содержащий параметры
$showRightsTab - определяет надо ли показывать вкладку с настройками прав доступа к модулю ( true / false )
*/
$opt = new CModuleOptions(MODULE_ID, $arTabs, $arGroups, $arOptions, $showRightsTab);
$opt->ShowHTML();
?>