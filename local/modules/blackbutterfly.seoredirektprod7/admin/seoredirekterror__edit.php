<?php
/**
 * @define THIS_FILE - константа определенная в файле родителе 
 */
@define('MODULE_ID', basename(dirname(dirname(__FILE__))));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); // первый общий пролог
// Проверка прав
$RIGHT = $APPLICATION->GetGroupRight(MODULE_ID);
if ($RIGHT <= "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));;

$sEntity = 'Blackbutterfly\\Seoredirektprod7\\';
foreach (explode('_', str_replace('__edit.php', '', basename(__FILE__))) as $k)
{
    $sEntity.= ucfirst($k);
}
$sEntity .= 'Table';

// Подключение языковых файлов
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

// Подключение модулей
\Bitrix\Main\Loader::includeModule(MODULE_ID);

// Файл
  $files =  str_replace('__edit.php', '', basename(__FILE__));
  $listing = MODULE_ID.'_'.$files.'__list.php';
  $edting = MODULE_ID.'_'.$files.'__edit.php';

// Подключение css/js
CJSCore::Init(array("jquery"));
CUtil::InitJSCore(array('window'));

// сформируем список закладок
$aTabs = array(
  array("DIV" => "edit1", "TAB" => Loc::getMessage(MODULE_ID.'_ADD'), "ICON"=>"main_user_edit", "TITLE"=> Loc::getMessage(MODULE_ID.'_ADD'))
);
$tabControl = new \CAdminTabControl("tabControl", $aTabs);

// Действия
$ID = intval($ID);		// идентификатор редактируемой записи
$message = null;		// сообщение об ошибке
$bVarsFromForm = false; // флаг "Данные получены с формы", обозначающий, что выводимые данные получены с формы, а не из БД.

if ( $REQUEST_METHOD == "POST" && ($save!="" || $apply!="") && check_bitrix_sessid() ) {
		$massarray = array();
		$errorsmessage = array();
		foreach ( $_POST as $index=>$val ) {
			if ( substr_count($index,"pole_") > 0 ) {
				$idpole = str_replace('pole_','',$index);
				$massarray[$idpole] = $val;
				if ( $val == '' ) {
					$errorsmessage[] = '1';
				}
			}
		}
		$massarray['ADDURLDATE'] = date('U');
		$massarray['USERID'] = $USER->GetID();
		if (!isset($errorsmessage[0])) {
			$res = \Blackbutterfly\Seoredirektprod7\SeoredirektTable::add($massarray);				
			\Blackbutterfly\Seoredirektprod7\Functions::deleteByIdWithEntityName($ID, $sEntity);		
		}

	if($res) {
    if ($apply != "")
      // если была нажата кнопка "Применить" - отправляем обратно на форму.
      LocalRedirect("/bitrix/admin/".$listing."?lang=".LANG);
    else
      // если была нажата кнопка "Сохранить" - отправляем к списку элементов.
      LocalRedirect("/bitrix/admin/".$listing."?lang=".LANG);
  } else  {
    // если в процессе сохранения возникли ошибки - получаем текст ошибки и меняем вышеопределённые переменные
    if($e = $APPLICATION->GetException())
      $message = new CAdminMessage(GetMessage("rub_save_error"), $e);
	  $bVarsFromForm = true;
  }
}

// Данные
if($ID>0)
{
	// Это редактирование получаем данные
	$rs = $sEntity::getById($ID);
	$ar = $rs->fetch();
}

//Вывод формы

// установим заголовок страницы
$APPLICATION->SetTitle(Loc::getMessage(MODULE_ID.'_ADD'));

// не забудем разделить подготовку данных и вывод
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

// конфигурация административного меню
$aMenu = array(
  array(
    "TEXT"=>Loc::getMessage(MODULE_ID.'_PREW'),
    "TITLE"=>Loc::getMessage(MODULE_ID.'_PREW'),
    "LINK"=>"/bitrix/admin/".$listing."?lang=".LANG,
    "ICON"=>"btn_list",
  )
);
$context = new CAdminContextMenu($aMenu);
$context->Show();
// если есть сообщения об ошибках или об успешном сохранении - выведем их.
if($_REQUEST["mess"] == "ok" && $ID>0)
  CAdminMessage::ShowMessage(array("MESSAGE"=>Loc::getMessage(MODULE_ID."_SAVED_SUCCESS"), "TYPE"=>"OK"));

if(isset($errorsmessage[0])) {
  CAdminMessage::ShowMessage(array("MESSAGE"=>Loc::getMessage(MODULE_ID."_SAVED_ERRORS")."<br />".Loc::getMessage(MODULE_ID."_NOTEMPTY_ERRORS"), "TYPE"=>"ERROR"));
}


//Форма
?>
	<form method="POST" Action="<?echo $APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form">  
	<?// проверка идентификатора сессии ?>
		<?echo bitrix_sessid_post();?>
	<?
	// отобразим заголовки закладок
		$tabControl->Begin();
		$tabControl->BeginNextTab();
	?>	
	  <tr>
		<td><span class="required">*</span><?=Loc::getMessage(MODULE_ID.'_ADDEDIT_FIRSTURL')?></td>
		<td><input type="text" name="pole_FIRSTURL" value="<?=$ar['URL'];?>" size="50" maxlength="100"></td>
	  </tr>      
	  <tr>
		<td><span class="required">*</span><?=Loc::getMessage(MODULE_ID.'_ADDEDIT_LASTURL')?></td>
		<td><input type="text" name="pole_LASTURL" value="<?=$ar['LASTURL'];?>" size="50"></td>
	  </tr>	 
	  <tr>
        <td width="40%"><span class="required">*</span><?=Loc::getMessage(MODULE_ID.'_ADDEDIT_TYPEBLOCK')?></td>
        <td width="60%">
<?
				$arr = array(
					"REFERENCE" => array(Loc::getMessage(MODULE_ID.'_TYPE1'),Loc::getMessage(MODULE_ID.'_TYPE2')),
					"REFERENCE_ID" => array('element','folders')
					); 
				echo SelectBoxFromArray("pole_TYPEBLOCK", $arr, $ar['TYPEBLOCK'], "", "", false, "post_form")
?>		
		</td>
      </tr>	 	  
	  <tr>
        <td width="40%"><span class="required">*</span><?=Loc::getMessage(MODULE_ID.'_ADDEDIT_SITEID')?></td>
        <td width="60%">
<?
		$rsSites = CSite::GetList($bys="LID", $orders="asc", Array());
		while ($arSite = $rsSites->Fetch())
		{
			$MARKETTYPE_ID[] = $arSite['LID'];
			$MARKETTYPE[] = "[".$arSite['ID']."] ".$arSite['NAME'];
		}
				$arr = array(
					"REFERENCE" => $MARKETTYPE,
					"REFERENCE_ID" => $MARKETTYPE_ID
					); 
				echo SelectBoxFromArray("pole_SITEID", $arr, $ar['SITEID'], "", "", false, "post_form")
?>		
		</td>
      </tr>  
<?
// завершение формы - вывод кнопок сохранения изменений
$tabControl->Buttons(
  array(
  //    "disabled"=>($POST_RIGHT<"W"),
    "back_url"=>$listing."?lang=".LANG,
  )
);
?>
<input type="hidden" name="lang" value="<?=LANG?>">
<?if($ID>0 && !$bCopy):?>
  <input type="hidden" name="ID" value="<?=$ID?>">
<?endif;?>
<?
// завершаем интерфейс закладок
$tabControl->End();
?>
<?
// дополнительное уведомление об ошибках - вывод иконки около поля, в котором возникла ошибка
$tabControl->ShowWarnings("post_form", $message);
?>
<?
// дополнительно: динамическая блокировка закладки, если требуется.
?>
<?
// информационная подсказка
echo BeginNote();?>
<span class="required">*</span><?echo GetMessage("REQUIRED_FIELDS")?>
<?echo EndNote();?>

<?
// завершение страницы
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>	  