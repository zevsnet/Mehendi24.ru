<?
	@define("MODULE_ID", basename(dirname(dirname(THIS_FILE))));
	use Bitrix\Main\Localization\Loc;
	Loc::loadMessages(THIS_FILE);
	$aButtons = 'yes';
	$aButtonsedit = 'yes';
	$take = 'yes';
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
							  'VARIABLE' => 'FIRSTURL',
							  'NAME' => Loc::getMessage(MODULE_ID.'_ADDEDIT_FIRSTURL'),
							  'TYPE' => 'INPUT',
							  'NOTEMPTY' => 'Y',
							  'SIZE'=>'B',
							  'DEFAULT'=>'',							  
							  'SHOWLISTDEFAULT' => 'Y', //Показывать в списке по умолчанию
							  'LISTFILTER' => 'Y',	//Добавить в фильтр
							),
						1 =>array(
							  'VARIABLE' => 'LASTURL',
							  'NAME' => Loc::getMessage(MODULE_ID.'_ADDEDIT_LASTURL'),
							  'TYPE' => 'INPUT',
							  'NOTEMPTY' => 'Y',
							  'SIZE'=>'B',
							  'DEFAULT'=>'',							  
							  'SHOWLISTDEFAULT' => 'Y', //Показывать в списке по умолчанию
							  'LISTFILTER' => 'Y',	//Добавить в фильтр
							),	
						2 =>array(
							  'VARIABLE' => 'TYPEBLOCK',
							  'NAME' => Loc::getMessage(MODULE_ID.'_ADDEDIT_TYPEBLOCK'),
							  'TYPE' => 'SELECT',
							  'NOTEMPTY' => 'Y',
							  'LISTSTYLEMODIFICATIONTYPE'=> 'SELECT',
							  'SHOWLISTDEFAULT' => 'Y', //Показывать в списке по умолчанию
							  'LISTFILTER' => 'Y',	//Добавить в фильтр									  
							  'VALUE' => array( "REFERENCE" => array(Loc::getMessage(MODULE_ID.'_TYPE1'),Loc::getMessage(MODULE_ID.'_TYPE2')),
												"REFERENCE_ID" => array('element','folders')
								) 
							),	
						3 =>array(
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
						4 =>array(
							  'VARIABLE' => 'ADDURLDATE',
							  'NAME' => Loc::getMessage(MODULE_ID.'_ADDEDIT_ADDURLDATE'),
							  'TYPE' => 'INPUTREADONLY',
							  'NOTEMPTY' => 'N',
							  'SIZE'=>'M',
							  'LISTSTYLEMODIFICATIONTYPE'=> 'UNITTIME',
							  'SHOWLISTDEFAULT' => 'Y', //Показывать в списке по умолчанию
							  'LISTFILTER' => 'N',	//Добавить в фильтр									  
							),
						5 =>array(
							  'VARIABLE' => 'USERID',
							  'NAME' => Loc::getMessage(MODULE_ID.'_ADDEDIT_USERID'),
							  'TYPE' => 'USERREADONLY',
							  'NOTEMPTY' => 'N',
							  'LISTSTYLEMODIFICATIONTYPE'=> 'USERREADONLY',
							  'SHOWLISTDEFAULT' => 'Y', //Показывать в списке по умолчанию
							  'LISTFILTER' => 'N',	//Добавить в фильтр									  

							),
	   )
	);
?>	