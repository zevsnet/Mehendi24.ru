<?
	@define("MODULE_ID", basename(dirname(dirname(THIS_FILE))));
	use Bitrix\Main\Localization\Loc;
	Loc::loadMessages(THIS_FILE);
	$aButtons = 'yes';
	$aButtonsedit = 'yes';
	$title = $ID>0? Loc::getMessage(MODULE_ID.'_SEOREDIREKTIGNORE_EDIT') : Loc::getMessage(MODULE_ID.'_SEOREDIREKTIGNORE_ADD');
	$aTabs = array(
		array("DIV" => "edit1", "TAB" => ($ID>0? Loc::getMessage(MODULE_ID.'_SEOREDIREKTIGNORE_EDIT') : Loc::getMessage(MODULE_ID.'_SEOREDIREKTIGNORE_ADD')), "ICON"=>"main_user_edit", "TITLE"=> ($ID>0? Loc::getMessage(MODULE_ID.'_SEOREDIREKTIGNORE_EDIT') : Loc::getMessage(MODULE_ID.'_SEOREDIREKTIGNORE_ADD'))),
	);	
		$rsSites = CSite::GetList($bys="LID", $orders="asc", Array());
		while ($arSite = $rsSites->Fetch())
		{
			$MARKETTYPE_ID[] = $arSite['LID'];
			$MARKETTYPE[] = "[".$arSite['ID']."] ".$arSite['NAME'];
		}
	$arPole = array(
		'edit1' => array(	
						0 =>array(
							  'VARIABLE' => 'URL',
							  'NAME' => Loc::getMessage(MODULE_ID.'_ADDEDIT_URL'),
							  'TYPE' => 'INPUT',
							  'NOTEMPTY' => 'Y',
							  'SIZE'=>'B',
							  'DEFAULT'=>'',							  
							  'SHOWLISTDEFAULT' => 'Y', //Показывать в списке по умолчанию
							  'LISTFILTER' => 'Y',	//Добавить в фильтр
							),
	   )
	);
?>	