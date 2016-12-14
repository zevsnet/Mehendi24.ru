<?php
// Ид модуля = папка модуля
@define('MODULE_ID', basename(dirname(dirname(__FILE__))));

// подключим все необходимые файлы:
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); // первый общий пролог

// Проверка прав
$RIGHT = $APPLICATION->GetGroupRight(MODULE_ID);
if ($RIGHT <= "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
	
// Файл
  $files =  str_replace('__list.php', '', basename(__FILE__));
  $listing = MODULE_ID.'_'.$files.'__list.php';
  
// Подключение языковых файлов
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

// Подключение модулей
\Bitrix\Main\Loader::includeModule(MODULE_ID);

// сформируем список закладок
$aTabs = array(
  array("DIV" => "edit1", "TAB" => ($ID>0? Loc::getMessage(MODULE_ID.'_IMPORT') : Loc::getMessage(MODULE_ID.'_IMPORT')), "ICON"=>"main_user_edit", "TITLE"=> Loc::getMessage(MODULE_ID.'_IMPORT'))
);
$tabControl = new \CAdminTabControl("tabControl", $aTabs);

if ( $REQUEST_METHOD == "POST" && ($save!="" || $apply!="") && check_bitrix_sessid() ) {
		if ( $_FILES['pole_IMPORT']['name'] != '' ) {
			$arr_file=Array(
				"name" => $_FILES['pole_IMPORT']['name'],
				"size" => $_FILES['pole_IMPORT']['size'],
				"tmp_name" => $_FILES['pole_IMPORT']['tmp_name'],
				"type" => "",
				"old_file" => "",
				"del" => "Y",
				"MODULE_ID" => MODULE_ID
			);
			$count = 0;
			$fid = CFile::SaveFile($arr_file, MODULE_ID);
			if (($handle = fopen($_SERVER['DOCUMENT_ROOT']."".CFile::GetPath($fid), "r")) !== FALSE) {			
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
						$newpars = explode(';',$data[0]);
						$massarray = array(
							'FIRSTURL'=>$newpars[0],
							'LASTURL'=>$newpars[1],
							'TYPEBLOCK'=>$newpars[2],
							'SITEID'=>$newpars[3],
							'ADDURLDATE'=>date('U'),
							'USERID'=>$USER->GetID(),
						);
						\Blackbutterfly\Seoredirektprod7\Functions::takeElement($newpars[0], $newpars[1], $newpars[2], '\Blackbutterfly\Seoredirektprod7\SeoredirektTable','');
						$ID = \Blackbutterfly\Seoredirektprod7\SeoredirektTable::add($massarray)->getId();
						if ( $ID > 0 ) {
							$count = $count + 1;
						}
				}
				fclose($handle);
				LocalRedirect("/bitrix/admin/".$listing."?count=".$count."&lang=".LANG);
			} else {
				$noread = 1;
				LocalRedirect("/bitrix/admin/".$listing."?noread=1&lang=".LANG);
			}
			CFile::Delete($fid);
		}	
}

//Вывод формы

// установим заголовок страницы
$APPLICATION->SetTitle(GetMessage(MODULE_ID."_".ToUpper((str_replace('__list.php', '', basename(__FILE__))))));

// не забудем разделить подготовку данных и вывод
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<? if ( $_REQUEST["noread"] == 1 ) { ?>
	<?CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage(MODULE_ID."_UPLOAD_ERRORS"), "TYPE"=>"ERROR"));?>
<? }elseif ( $_REQUEST["count"] != '' and $_REQUEST["count"] >= 0 ) { ?>
	<?CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage(MODULE_ID."_UPLOAD_SUCCESS")."".$count, "TYPE"=>"OK"));?>
<? } ?>
	<form method="POST" Action="<?echo $APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form">  
	<?// проверка идентификатора сессии ?>
		<?echo bitrix_sessid_post();?>
	<?
	// отобразим заголовки закладок
		$tabControl->Begin();
		$tabControl->BeginNextTab();
	?>	
	  <tr>
		 <td width="40%"><?=Loc::getMessage(MODULE_ID.'_IMPORT_FILE')?></td>
		 <td width="60%">
			<?echo CFileInput::Show('pole_IMPORT', $ar['IMPORT'], array(
				"IMAGE" => "Y",
				"PATH" => "Y",
				"FILE_SIZE" => "Y",
				"DIMENSIONS" => "Y",
				"IMAGE_POPUP" => "Y",
			), array(
				'upload' => true,
				'medialib' => true,
				'file_dialog' => true,
				'cloud' => true,
				'del' => true,
				'description' => false,
			));?>
		 </td>
		</tr>
		<tr>
		 <td width="40%" style="vertical-align: top;" ></td>
		 <td width="60%">
								<div class="notes">
                                    <table cellspacing="0" cellpadding="0" border="0" class="notes">
                                        <tbody>
                                            <tr class="top">
                                                <td class="left"><div class="empty"></div></td>
                                                <td><div class="empty"></div></td>
                                                <td class="right"><div class="empty"></div></td>
                                            </tr>
                                            <tr>
                                                <td class="left"><div class="empty"></div></td>
                                                <td class="content">
                                                    <?=Loc::getMessage(MODULE_ID.'_IMPORT_PRIMER')?>
                                                </td>
                                                <td class="right"><div class="empty"></div></td>
                                            </tr>
                                            <tr class="bottom">
                                                <td class="left"><div class="empty"></div></td>
                                                <td><div class="empty"></div></td>
                                                <td class="right"><div class="empty"></div></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>			
		 </td>
	  </tr>      

<?
// завершение формы - вывод кнопок сохранения изменений
?>
	<? $tabControl->Buttons(); ?>
			<input type="submit" name="save" value="<?=Loc::getMessage(MODULE_ID.'_IMPORT_START')?>" title="<?=Loc::getMessage(MODULE_ID.'_IMPORT_START')?>" class="adm-btn-save">
	<? $tabControl->End(); ?>			
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