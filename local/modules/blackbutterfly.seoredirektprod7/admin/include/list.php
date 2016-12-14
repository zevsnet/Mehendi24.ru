<?php
/**
 * @define THIS_FILE - константа определенная в файле родителе 
 */
@define("MODULE_ID", basename(dirname(dirname(THIS_FILE))));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
// Проверка прав
$RIGHT = $APPLICATION->GetGroupRight(MODULE_ID);
if ($RIGHT <= "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
// Название ORM сущности, строим по имени файла
$sEntity = 'Blackbutterfly\\Seoredirektprod7\\';
foreach (explode('_', str_replace('__list.php', '', basename(THIS_FILE))) as $k)
{
    $sEntity.= ucfirst($k);
}
$sEntity .= 'Table';

// Подключение языковых файлов
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

// Названия страниц для списка и редактирования
$pageList = MODULE_ID . '_' . basename(THIS_FILE);
$pageListsmall = str_replace('.php', '', basename(THIS_FILE));
$pageEdit = str_replace('__list', '__edit', $pageList);

// Подключение модулей
Bitrix\Main\Loader::includeModule(MODULE_ID);

//параметры 	
	$folders = str_replace(array('__list','__edit'),array('','') , THIS_FILE);
	$fileinclude = str_replace(array('__list','__edit'),array('','') , basename(THIS_FILE));
	$incfiles = str_replace($fileinclude,'params/'.$fileinclude,$folders);
	include($incfiles);
	
// Подключение css/js
CJSCore::Init(array("jquery"));
CUtil::InitJSCore(array('window'));

// Сущность ORM
$entity = Bitrix\Main\Entity\Base::getInstance($sEntity);


// Инициализация списка
$sTableID = join('', explode('\\', $sEntity));
$oSort = new CAdminSorting($sTableID, "id", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);


if($lAdmin->EditAction())
{
	foreach($FIELDS as $ID => $arUpdate)
	{
		if(!$lAdmin->IsUpdated($ID))
			continue;

		$ID = intval($ID);
/*		
		echo $ID;
		$rs = $sEntity::getById($ID);
		$ar = $rs->fetch();
		print_r($ar);
*/		
		$res = $sEntity::update($ID, $arUpdate);

		
/*		
		if (!$cData->Update($ID, $arUpdate))
		{
			$lAdmin->AddUpdateError(GetMessage("CONS_G_L_SAVE_ERR")." #".$ID.": ".$cData->LAST_ERROR, $ID);
			$DB->Rollback();
		}
		
		$DB->Commit();
*/		
	}
	
}

// Выполнение действий
if($arID = $lAdmin->GroupAction())
{
	
	if ( $_REQUEST['action_target'] == 'selected' ){
		$rs = $sEntity::getList();
			while ($ar = $rs->fetch()) {
				\Blackbutterfly\Seoredirektprod7\Functions::deleteByIdWithEntityName($ar['ID'] , $sEntity);
			}	
	} else {
			foreach($arID as $ID)
			{
				if(strlen($ID)<=0)
					continue;
				$ID = IntVal($ID);
				switch($_REQUEST['action'])
				{
					case "delete":
						\Blackbutterfly\Seoredirektprod7\Functions::deleteByIdWithEntityName($ID, $sEntity);
					break;
				}
			}
	}
}
$APPLICATION->SetTitle(GetMessage(MODULE_ID."_".ToUpper((str_replace('__list.php', '', basename(THIS_FILE))))));

// Фильтр
/*
$arFilterRows = array();
foreach($entity->getFields() as $field)
{
    $arFilterRows[] = $field->getTitle();
}
*/
foreach ( $arPole as $tabs ) {	
		foreach ( $tabs as $pole ) {
			if (  $pole['LISTFILTER'] == 'Y' ) {
				$arFilterRows[] = $pole['NAME'];
			}
		}
}				
$filter = new CAdminFilter($sTableID."_filter_id", $arFilterRows);  // Создаём объект фильтра

$arFilterFields = array();
foreach($entity->getFields() as $field)
{
    $arFilterFields[] = $field->getName();
}
$lAdmin->InitFilter($arFilterFields);

$arFilter = array();
if (empty($request['del_filter']))
foreach($entity->getFields() as $field)
{
    $key = $field->getName();      
    if (!empty($$key))
    {        
        switch($field->getDataType())
        {
            case 'integer': 
                $arFilter[$key] = ${$key};
                break;
            default: 
                $arFilter[$key] = '%'.${$key}.'%';
        }
    }
}

// Столбцы таблицы
$arHeaders = array();
    $arHeaders[] = array(
		"id" => 'ID',
		"content" => 'ID',
		"sort" => 'ID',
        "default" => true, //in_array($code, array('ID', 'NAME', 'SORT', 'CODE', 'ART', 'ACTIVE'))
	);
	foreach ( $arPole as $tabs ) {	
		foreach ( $tabs as $pole ) {
			if (  $pole['SHOWLISTDEFAULT'] == 'Y' ) {
				$default = true;
			} else {
				$default = false;
			}
			$arHeaders[] = array(
				"id" => $pole['VARIABLE'],
				"content" => $pole['NAME'],
				"sort" => $pole['VARIABLE'],
				"default" => $default, //in_array($code, array('ID', 'NAME', 'SORT', 'CODE', 'ART', 'ACTIVE'))
			);		
		}
	}
$lAdmin->AddHeaders($arHeaders);
// Получение данных
$query = new Bitrix\Main\Entity\Query($entity); // Конструктор sql запросов
$rsQuery = $query
    ->setSelect(array('*'))
    ->setOrder(array(strtoupper($by)?:'ID'=>$order?:'desc'))
    ->setFilter($arFilter)
    ->exec();


// Преобразование данных
$rsData = new CAdminResult($rsQuery, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage('NAV'))); //название постранички


// инициализация строк таблицы
while($arRes = $rsData->NavNext(true, "f_"))
{    
    $row =& $lAdmin->AddRow($f_ID, $arRes);
	foreach ( $arPole as $tabs ) {	
		foreach ( $tabs as $pole ) {
				if ( isset($pole['LISTSTYLEMODIFICATIONTYPE']) and $pole['LISTSTYLEMODIFICATIONTYPE'] == 'CHEKBOX' ) {
					$row->AddViewField($pole['VARIABLE'], ($arRes[$pole['VARIABLE']]=='Y'? Loc::getMessage('CHECKBOX_Y') : Loc::getMessage('CHECKBOX_N')));
				} elseif ( isset($pole['LISTSTYLEMODIFICATIONTYPE']) and $pole['LISTSTYLEMODIFICATIONTYPE'] == 'IMAGES' ) {
					if ( $arRes[$pole['VARIABLE']] != '' and $arRes[$pole['VARIABLE']] != 0 ) {
						$file = CFile::ResizeImageGet($arRes[$pole['VARIABLE']], array('width'=>100, 'height'=>100), BX_RESIZE_IMAGE_PROPORTIONAL, true);                
						$row->AddViewField($pole['VARIABLE'], '<img src="'.$file['src'].'" width="'.$file['width'].'" height="'.$file['height'].'" />');
					} else {
						$row->AddViewField($pole['IMAGES'], '');
					}
				} elseif ( isset($pole['LISTSTYLEMODIFICATIONTYPE']) and $pole['LISTSTYLEMODIFICATIONTYPE'] == 'SELECT' ) {
					$key = array_search($arRes[$pole['VARIABLE']], $pole['VALUE']['REFERENCE_ID']);
					$row->AddViewField($pole['VARIABLE'], $pole['VALUE']['REFERENCE'][$key]);
					$paramsselect = array();
					foreach ($pole['VALUE']['REFERENCE_ID'] as $index=>$value ) {
						$paramsselect[$value] = $pole['VALUE']['REFERENCE'][$index];
					}
					$row->AddSelectField($pole['VARIABLE'], $paramsselect);
				} elseif ( isset($pole['LISTSTYLEMODIFICATIONTYPE']) and $pole['LISTSTYLEMODIFICATIONTYPE'] == 'UNITTIME' ) {
					$row->AddViewField($pole['VARIABLE'], date('d.m.Y H:i:s',$arRes[$pole['VARIABLE']]));
				} elseif ( isset($pole['LISTSTYLEMODIFICATIONTYPE']) and $pole['LISTSTYLEMODIFICATIONTYPE'] == 'USERREADONLY' ) {
					$rsUser = CUser::GetByID($arRes[$pole['VARIABLE']]);
					$arUser = $rsUser->Fetch();
					$row->AddViewField($pole['VARIABLE'], '['.$arRes[$pole['VARIABLE']].'] '.$arUser['LAST_NAME'].' '.$arUser['NAME']);
				} else {
					$row->AddInputField ($pole['VARIABLE'], Array("size"=>"15"));
				}
		}
	}
    // контекстное меню для строки
    $arActions = Array();
if ( $aButtonsedit != 'no' ) {
    $arActions[] = array(
		"ICON" => "edit",
		"TEXT" => GetMessage(MODULE_ID."_ACTION_EDIT"),
		"DEFAULT" => "Y",
		"ACTION" => $lAdmin->ActionRedirect(
            $pageEdit."?ID=".$arRes['ID']."&lang=".LANG
        )
	);
}
if ( $aButtonsadd == 'yes' ) {
    $arActions[] = array(
		"ICON" => "edit",
		"TEXT" => GetMessage(MODULE_ID."_ACTION_ADD_SMALL"),
		"DEFAULT" => "Y",
		"ACTION" => $lAdmin->ActionRedirect(
            $pageEdit."?ID=".$arRes['ID']."&lang=".LANG
        )
	);
}
    $arActions[] = array(
		"ICON" => "delete",
		"TEXT" => GetMessage(MODULE_ID."_ACTION_DELETE"),
		"ACTION"=>"if(confirm('".GetMessage(MODULE_ID."_ACTION_DELETE_PODTV")."')) ".$lAdmin->ActionDoGroup($arRes['ID'], "delete"),
	);	
	$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
		array("title"=>'123', "value"=>$rsData->SelectedRowsCount()),
		array("counter"=>true, "title"=> '234', "value"=>"0"),
	)
);

//Групповые операции
$arB = Array();
	$arB["delete"]=GetMessage("MAIN_ADMIN_LIST_DELETE");
$lAdmin->AddGroupActionTable($arB);
//Групповые операции END
if ( $aButtons != 'no' ) {
// контекстное меню для всей таблицы
$aContext = array(
   array(
      "ICON" => "btn_new",
      "TEXT"=> GetMessage(MODULE_ID."_ACTION_ADD"),
      "LINK"=> $pageEdit."?ID=0&lang=".LANG,
      "TITLE"=> GetMessage(MODULE_ID."_ACTION_ADD")
   ),
);
}
if ($pageListsmall == 'seoredirekt__list'){
	$aContext[] = array(
		  "TEXT"=> GetMessage(MODULE_ID."_IMPORT"),
		  "LINK"=> MODULE_ID."_import__list.php",
		  "TITLE"=> GetMessage(MODULE_ID."_IMPORT")
	   );
	$aContext[] = array(
		  "TEXT"=> GetMessage(MODULE_ID."_EXPORT"),
		  "LINK"=> $pageList."?save=Y&lang=".LANG,
		  "TITLE"=> GetMessage(MODULE_ID."_EXPORT")
	   );   
}
if ($aContext) {
$lAdmin->AddAdminContextMenu($aContext);
} else {
$lAdmin->AddAdminContextMenu();
}
// доп. действия
$lAdmin->CheckListMode();
if ($pageListsmall == 'seoredirekt__list' and $_GET['save']=='Y'){
	$file = "404.csv";
	header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
 			$rs = Blackbutterfly\Seoredirektprod7\SeoredirektTable::getList();
				while ($ar = $rs->fetch()) {	
					unset($ar['ID']);
					echo implode(";", $ar)."\r\n";
				}
} else {
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");?>
<form name="form2" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<?$filter->Begin();?>

<? 	foreach ( $arPole as $tabs ) {	
		foreach ( $tabs as $pole ) {
			if (  $pole['LISTFILTER'] == 'Y' ) {
				switch($pole['TYPE']) {	
					case 'INPUT':
							?>
								<tr>
								   <td nowrap><?=$pole['NAME']?>:</td>
								   <td nowrap><input type="text" name="<?=$pole['VARIABLE']?>" value="<?=htmlspecialchars(${$pole['VARIABLE']})?>" size="44"></td>
								</tr>
							<?	
						break;
					case 'CHECKBOX':
							?>
								<tr>
								   <td nowrap><?=$pole['NAME']?>:</td>
								   <td nowrap>
								      <?
									   if ($pole['MULTY'] == "Y") {
											echo SelectBoxMFromArray($pole['VARIABLE'], $pole['VALUE'], ${$pole['VARIABLE']}, '', '',false,'');
									   } else {
											$pole['VALUE'] = array( "REFERENCE" => array('',Loc::getMessage('CHECKBOX_Y'), Loc::getMessage('CHECKBOX_N')),
												"REFERENCE_ID" => array('',$pole['DEFAULTCHECK'], 'N')
											);
											echo SelectBoxFromArray($pole['VARIABLE'], $pole['VALUE'], ${$pole['VARIABLE']}, '', '',false,'');
									   }
									  ?>
									</td>
								</tr>
							<?						
						break;
					case 'SELECT':
							?>
								<tr>
								   <td nowrap><?=$pole['NAME']?>:</td>
								   <td nowrap>
								      <?
									   if ($pole['MULTY'] == "Y") {
											echo SelectBoxMFromArray($pole['VARIABLE'], $pole['VALUE'], ${$pole['VARIABLE']}, '', '',false,'');
									   } else {
										echo SelectBoxFromArray($pole['VARIABLE'], $pole['VALUE'], ${$pole['VARIABLE']}, '', '',false,'');
									   }
									  ?>
									</td>
								</tr>
							<?						
							break;	
					case 'MSELECT':
							?>
								<tr>
								   <td nowrap><?=$pole['NAME']?>:</td>
								   <td nowrap>
								      <?
									   if ($pole['MULTY'] == "Y") {
											echo SelectBoxMFromArray($pole['VARIABLE'], $pole['VALUE'], ${$pole['VARIABLE']}, '', '',false,'');
									   } else {
											$pole['VALUE'] = array( "REFERENCE" => array('',Loc::getMessage('CHECKBOX_Y'), Loc::getMessage('CHECKBOX_N')),
												"REFERENCE_ID" => array('',$pole['DEFAULTCHECK'], 'N')
											);
											echo SelectBoxMFromArray($pole['VARIABLE'].'[]', $pole['VALUE'], ${$pole['VARIABLE']});
									   }
									  ?>
									</td>
								</tr>
							<?						
							break;								
				}
 			}
		}
	}
  ?>
<?$filter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"form2"));?>
<?$filter->End();?>
</form>

<?$lAdmin->DisplayList();?>

<?require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");?>
<? } ?>