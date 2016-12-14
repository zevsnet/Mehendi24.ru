<?
	@define("MODULE_ID", basename(dirname(dirname(THIS_FILE))));
	use Bitrix\Main\Localization\Loc;
	Loc::loadMessages(THIS_FILE);
	$aButtons = 'no';
	$aButtonsedit = 'no';
	$aButtonsadd = 'yes';
	$title = $ID>0? Loc::getMessage(MODULE_ID.'_EDIT') : Loc::getMessage(MODULE_ID.'_ADD');
	$aTabs = array(
		array("DIV" => "edit1", "TAB" => ($ID>0? Loc::getMessage(MODULE_ID.'_EDIT') : Loc::getMessage(MODULE_ID.'_ADD')), "ICON"=>"main_user_edit", "TITLE"=> ($ID>0? Loc::getMessage(MODULE_ID.'_EDIT') : Loc::getMessage(MODULE_ID.'_ADD'))),
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
						1 =>array(
							  'VARIABLE' => 'COUNTSETURL',
							  'NAME' => Loc::getMessage(MODULE_ID.'_ADDEDIT_COUNTSETURL'),
							  'TYPE' => 'INPUT',
							  'NOTEMPTY' => 'Y',
							  'SIZE'=>'B',
							  'DEFAULT'=>'',							  
							  'SHOWLISTDEFAULT' => 'Y', //Показывать в списке по умолчанию
							  'LISTFILTER' => 'N',	//Добавить в фильтр
							),	
						2 =>array(
							  'VARIABLE' => 'SITEID',
							  'NAME' => Loc::getMessage(MODULE_ID.'_ADDEDIT_SITEID'),
							  'TYPE' => 'SELECT',
							  'NOTEMPTY' => 'Y',
							  'LISTSTYLEMODIFICATIONTYPE'=> 'SELECT',
							  'SHOWLISTDEFAULT' => 'Y', //Показывать в списке по умолчанию
							  'LISTFILTER' => 'Y',	//Добавить в фильтр									  
							  'VALUE' => array( "REFERENCE" => $MARKETTYPE,
												"REFERENCE_ID" => $MARKETTYPE_ID
											   ) 
							),	
						3 =>array(
							  'VARIABLE' => 'REFERALURL',
							  'NAME' => Loc::getMessage(MODULE_ID.'_ADDEDIT_REFERALURL'),
							  'TYPE' => 'INPUT',
							  'NOTEMPTY' => 'N',
							  'SIZE'=>'B',
							  'DEFAULT'=>'',							  
							  'SHOWLISTDEFAULT' => 'Y', //Показывать в списке по умолчанию
							  'LISTFILTER' => 'Y',	//Добавить в фильтр
							),
	   )
	);
?>	