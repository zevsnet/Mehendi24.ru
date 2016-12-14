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
CJSCore::Init(array('window'));
if ($_REQUEST['givmeparams'] == 'y') {
	$res = CIBlock::GetByID($_REQUEST['iblock_id']);
	if($ar_res = $res->GetNext()) {
	  if ( $_REQUEST['type_id'] == 1 ) {
			$url = $ar_res['SECTION_PAGE_URL'];
	  } else {
			$url = $ar_res['DETAIL_PAGE_URL'];
	  }
		echo CUtil::PhpToJSObject(array(
				'URL' => $url,
		));
	}
} elseif($_REQUEST['work_start']) {
	if (!$_REQUEST['page']){
		$page = 1;
	} else {
		$page = $_REQUEST['page'];
	}
		$step = 20; //Шаг
		global $USER;
		$rsSites = CSite::GetByID($_REQUEST['site_id']);
		$arSite = $rsSites->Fetch();
		$site_dir = substr($arSite['DIR'],0,-1);	
	if ( $_REQUEST['type_id'] == 1 ) {
				  $arFilter = Array('IBLOCK_ID'=>2);
				  $res = CIBlockSection::GetList(Array($by=>$order), $arFilter, false,false,Array("nPageSize"=>$step,'iNumPage'=>$page));		
				  while($ar_result = $res->GetNext())
					{
							if ( substr_count($_REQUEST['firsturl'], '#SECTION_CODE#') > 0 or substr_count($_REQUEST['lasturl'], '#SECTION_CODE#')> 0 ) {
								$resp = CIBlockSection::GetByID($ar_result['ID']);
								if($ar_res = $resp->GetNext())
								  $section_code =  $ar_res['CODE'];
							}
							if ( substr_count($_REQUEST['firsturl'], '#SECTION_CODE_PATH#')> 0 or substr_count($_REQUEST['lasturl'], '#SECTION_CODE_PATH#')> 0  ) {
								$nav = CIBlockSection::GetNavChain($_REQUEST['iblock_id'],$ar_result['ID']);
								$s_path = array();
								while($arSectionPath = $nav->GetNext()){
								   $s_path[] = $arSectionPath['CODE'];
								}
								$section_code_path = implode("/", $s_path);
							}
							$firsturl = str_replace(array('#SITE_DIR#','#SECTION_ID#','#SECTION_CODE#','#SECTION_CODE_PATH#'),array($site_dir,$ar_result['ID'],$section_code,$section_code_path),$_REQUEST['firsturl']);
							$lasturl = str_replace(array('#SITE_DIR#','#SECTION_ID#','#SECTION_CODE#','#SECTION_CODE_PATH#'),array($site_dir,$ar_result['ID'],$section_code,$section_code_path),$_REQUEST['lasturl']);
							$massarray = array(
								'FIRSTURL'=> $firsturl,
								'LASTURL'=> $lasturl,
								'TYPEBLOCK'=> 'folders',
								'SITEID'=> $_REQUEST['site_id'],
								'ADDURLDATE'=> date('U'),
								'USERID'=> $USER->GetID(),
							);
							$ID = \Blackbutterfly\Seoredirektprod7\SeoredirektTable::add($massarray)->getId();						
					}
	} else if ( $_REQUEST['type_id'] == 2 ) {
					$arFilter = Array("IBLOCK_ID"=>$_REQUEST['iblock_id']);
					$res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>$step,'iNumPage'=>$page),array('ID','CODE','IBLOCK_SECTION_ID'));
					while($ob = $res->GetNextElement()){
							$arFields = $ob->GetFields();
							if ( substr_count($_REQUEST['firsturl'], '#SECTION_CODE#') > 0 or substr_count($_REQUEST['lasturl'], '#SECTION_CODE#')> 0 ) {
								$resp = CIBlockSection::GetByID($arFields['IBLOCK_SECTION_ID']);
								if($ar_res = $resp->GetNext())
								  $section_code =  $ar_res['CODE'];
							}
							if ( substr_count($_REQUEST['firsturl'], '#SECTION_CODE_PATH#')> 0 or substr_count($_REQUEST['lasturl'], '#SECTION_CODE_PATH#')> 0  ) {
								$nav = CIBlockSection::GetNavChain($_REQUEST['iblock_id'],$arFields['IBLOCK_SECTION_ID']);
								$s_path = array();
								while($arSectionPath = $nav->GetNext()){
								   $s_path[] = $arSectionPath['CODE'];
								}
								$section_code_path = implode("/", $s_path);
							}
							$firsturl = str_replace(array('#SITE_DIR#','#SECTION_ID#','#SECTION_CODE#','#SECTION_CODE_PATH#','#ELEMENT_ID#','#ELEMENT_CODE#'),array($site_dir,$arFields['IBLOCK_SECTION_ID'],$section_code,$section_code_path,$arFields['ID'],$arFields['CODE']),$_REQUEST['firsturl']);
							$lasturl = str_replace(array('#SITE_DIR#','#SECTION_ID#','#SECTION_CODE#','#SECTION_CODE_PATH#','#ELEMENT_ID#','#ELEMENT_CODE#'),array($site_dir,$arFields['IBLOCK_SECTION_ID'],$section_code,$section_code_path,$arFields['ID'],$arFields['CODE']),$_REQUEST['lasturl']);
							$massarray = array(
								'FIRSTURL'=> $firsturl,
								'LASTURL'=> $lasturl,
								'TYPEBLOCK'=> 'element',
								'SITEID'=> $_REQUEST['site_id'],
								'ADDURLDATE'=> date('U'),
								'USERID'=> $USER->GetID(),
							);
							$ID = \Blackbutterfly\Seoredirektprod7\SeoredirektTable::add($massarray)->getId();
					}
	}
					$posution_proc = round((100*$page)/ $res->NavPageCount);
					$html =  '<div class="adm-info-message-wrap adm-info-message-gray">
						<div class="adm-info-message">
							<div class="adm-info-message-title">'.Loc::getMessage(MODULE_ID.'_PROCESS').'</div>
							<div class="adm-progress-bar-outer" style="width: 500px;"><div class="adm-progress-bar-inner" style="width: '.$posution_proc.'%;"><div class="adm-progress-bar-inner-text" style="width: 500px;">'.$posution_proc.'%</div></div>'.$posution_proc.'%</div>
							<div class="adm-info-message-buttons"></div>
						</div>
					</div>';					
					echo CUtil::PhpToJSObject(array(
							'PAGE' => $page,
							'ALLPAGE' => $res->NavPageCount,
							'HTML' => $html
				   ));	
} else {
// сформируем список закладок
$aTabs = array(
  array("DIV" => "edit1", "TAB" => ($ID>0? Loc::getMessage(MODULE_ID.'_GENERATOR') : Loc::getMessage(MODULE_ID.'_GENERATOR')), "ICON"=>"main_user_edit", "TITLE"=> Loc::getMessage(MODULE_ID.'_GENERATOR'))
);
$tabControl = new \CAdminTabControl("tabControl", $aTabs);

//Вывод формы

// установим заголовок страницы
$APPLICATION->SetTitle(GetMessage(MODULE_ID."_".ToUpper((str_replace('__list.php', '', basename(__FILE__))))));

// не забудем разделить подготовку данных и вывод
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<div id="progress_procent" style="display: none;" >
</div>
<? if ( $_REQUEST["noread"] == 1 ) { ?>
	<?CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage(MODULE_ID."_UPLOAD_ERRORS"), "TYPE"=>"ERROR"));?>
<? }elseif ( $_REQUEST["count"] != '' and $_REQUEST["count"] >= 0 ) { ?>
	<?CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage(MODULE_ID."_UPLOAD_SUCCESS")."".$count, "TYPE"=>"OK"));?>
<? } ?>

	<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form" id="sender_form" >  
	<?// проверка идентификатора сессии ?>
		<?echo bitrix_sessid_post();?>
	<?
	// отобразим заголовки закладок
		$tabControl->Begin();
		$tabControl->BeginNextTab();
	?>	
	  <tr>
		 <td width="40%"><?=Loc::getMessage(MODULE_ID.'_GENERATOR_IBLOCK')?><span class="required">*</span></td>
		 <td width="60%">
			<select id="generator_iblock" >
				<option></option>
				<?
				$res = CIBlock::GetList(
					Array(), 
					Array(
						'ACTIVE'=>'Y', 
					), true
				);
				while($ar_res = $res->Fetch())
				{
					?>
						<option value="<?=$ar_res['ID']?>" ><?=$ar_res['NAME']?></option>
					<?
				}
				?>
			</select>
		 </td>
	  </tr>      
	  <tr>
		 <td width="40%"><?=Loc::getMessage(MODULE_ID.'_GENERATOR_SITE')?><span class="required">*</span></td>
		 <td width="60%">
			<select id="generator_siteid"  >
					<option></option>		 
				<?
				$rsSites = CSite::GetList($by="sort", $order="desc", Array());
				while ($arSite = $rsSites->Fetch())
				{
					?>
						<option value="<?=$arSite['LID']?>" ><?=$arSite['NAME']?></option>
					<?
				}
				?>	
			</select>
		 </td>
	  </tr> 
	  <tr>
		 <td width="40%"><?=Loc::getMessage(MODULE_ID.'_GENERATOR_TYPE')?><span class="required">*</span></td>
		 <td width="60%">
				<select id="generator_typeid"  >
				  <option></option>
				  <option value="1"><?=Loc::getMessage(MODULE_ID.'_GENERATOR_SECTION')?></option>
				  <option value="2"><?=Loc::getMessage(MODULE_ID.'_GENERATOR_ELEMENT')?></option>
				</select>				 
		 </td>
	  </tr>	  
	  	  <tr>
		 <td width="40%"><?=Loc::getMessage(MODULE_ID.'_GENERATOR_FIRSTURL')?><span class="required">*</span></td>
		 <td width="60%">
					<input type="text" name="LIST_FIRSTURL" id="generator_FIRSTURL" size="55" value="<?echo $str_LIST_PAGE_URL?>">
					<input type="button" id="mnu_FIRSTURL" value='...'>
					<input type="button" onclick="givmeparams('generator_FIRSTURL'); return false;" value='<?=Loc::getMessage(MODULE_ID.'_GENERATOR_GIVESETTINGS')?>'>
					<script type="text/javascript">
					BX.ready(function(){
						BX.bind(BX('mnu_FIRSTURL'), 'click', function() {
							BX.adminShowMenu(this, [
								{'TEXT':'<?=Loc::getMessage(MODULE_ID.'_GENERATOR_SITE_DIR')?>','TITLE':'#SITE_DIR# - <?=Loc::getMessage(MODULE_ID.'_GENERATOR_SITE_DIR')?>','ONCLICK':'document.getElementById(\'generator_FIRSTURL\').value+= \'#SECTION_ID#\''},
								{'SEPARATOR':true},							
								{'TEXT':'<?=Loc::getMessage(MODULE_ID.'_GENERATOR_SECTION_ID')?>','TITLE':'#SECTION_ID# - <?=Loc::getMessage(MODULE_ID.'_GENERATOR_SECTION_ID')?>','ONCLICK':'document.getElementById(\'generator_FIRSTURL\').value+= \'#SECTION_ID#\''},
								{'TEXT':'<?=Loc::getMessage(MODULE_ID.'_GENERATOR_SECTION_CODE')?>','TITLE':'#SECTION_CODE# - <?=Loc::getMessage(MODULE_ID.'_GENERATOR_SECTION_CODE')?>','ONCLICK':'document.getElementById(\'generator_FIRSTURL\').value+= \'#SECTION_CODE#\''},
								{'TEXT':'<?=Loc::getMessage(MODULE_ID.'_GENERATOR_SECTION_CODE_PATH')?>','TITLE':'#SECTION_CODE_PATH# - <?=Loc::getMessage(MODULE_ID.'_GENERATOR_SECTION_CODE_PATH')?>','ONCLICK':'document.getElementById(\'generator_FIRSTURL\').value+= \'#SECTION_CODE_PATH#\''},
								{'SEPARATOR':true},
								{'TEXT':'<?=Loc::getMessage(MODULE_ID.'_GENERATOR_ELEMENT_ID')?>','TITLE':'#ELEMENT_ID# - <?=Loc::getMessage(MODULE_ID.'_GENERATOR_ELEMENT_ID')?>','ONCLICK':'document.getElementById(\'generator_FIRSTURL\').value+= \'#ELEMENT_ID#\''},
								{'TEXT':'<?=Loc::getMessage(MODULE_ID.'_GENERATOR_ELEMENT_CODE')?>','TITLE':'#ELEMENT_CODE# - <?=Loc::getMessage(MODULE_ID.'_GENERATOR_ELEMENT_CODE')?>','ONCLICK':'document.getElementById(\'generator_FIRSTURL\').value+= \'#ELEMENT_CODE#\''}
						   ])
						});
					});
					</script>					
		 </td>
	  </tr> 
	  <tr>
		 <td width="40%"><?=Loc::getMessage(MODULE_ID.'_GENERATOR_LASTURL')?><span class="required">*</span></td>
		 <td width="60%">
				 	<input type="text" name="LIST_LASTURL" id="generator_LASTURL" size="55" value="<?echo $str_LIST_PAGE_URL?>">
					<input type="button" id="mnu_LASTURL" value='...'>
					<input type="button" onclick="givmeparams('generator_LASTURL'); return false;" value='<?=Loc::getMessage(MODULE_ID.'_GENERATOR_GIVESETTINGS')?>'>
					<script type="text/javascript">
					BX.ready(function(){
						BX.bind(BX('mnu_LASTURL'), 'click', function() {
							BX.adminShowMenu(this, [
								{'TEXT':'<?=Loc::getMessage(MODULE_ID.'_GENERATOR_SITE_DIR')?>','TITLE':'#SITE_DIR# - <?=Loc::getMessage(MODULE_ID.'_GENERATOR_SITE_DIR')?>','ONCLICK':'document.getElementById(\'generator_LASTURL\').value+= \'#SECTION_ID#\''},
								{'SEPARATOR':true},
								{'TEXT':'<?=Loc::getMessage(MODULE_ID.'_GENERATOR_SECTION_ID')?>','TITLE':'#SECTION_ID# - <?=Loc::getMessage(MODULE_ID.'_GENERATOR_SECTION_ID')?>','ONCLICK':'document.getElementById(\'generator_LASTURL\').value+= \'#SECTION_ID#\''},
								{'TEXT':'<?=Loc::getMessage(MODULE_ID.'_GENERATOR_SECTION_CODE')?>','TITLE':'#SECTION_CODE# - <?=Loc::getMessage(MODULE_ID.'_GENERATOR_SECTION_CODE')?>','ONCLICK':'document.getElementById(\'generator_LASTURL\').value+= \'#SECTION_CODE#\''},
								{'TEXT':'<?=Loc::getMessage(MODULE_ID.'_GENERATOR_SECTION_CODE_PATH')?>','TITLE':'#SECTION_CODE_PATH# - <?=Loc::getMessage(MODULE_ID.'_GENERATOR_SECTION_CODE_PATH')?>','ONCLICK':'document.getElementById(\'generator_LASTURL\').value+= \'#SECTION_CODE_PATH#\''},
								{'SEPARATOR':true},
								{'TEXT':'<?=Loc::getMessage(MODULE_ID.'_GENERATOR_ELEMENT_ID')?>','TITLE':'#ELEMENT_ID# - <?=Loc::getMessage(MODULE_ID.'_GENERATOR_ELEMENT_ID')?>','ONCLICK':'document.getElementById(\'generator_LASTURL\').value+= \'#ELEMENT_ID#\''},
								{'TEXT':'<?=Loc::getMessage(MODULE_ID.'_GENERATOR_ELEMENT_CODE')?>','TITLE':'#ELEMENT_CODE# - <?=Loc::getMessage(MODULE_ID.'_GENERATOR_ELEMENT_CODE')?>','ONCLICK':'document.getElementById(\'generator_LASTURL\').value+= \'#ELEMENT_CODE#\''}
						   ])
						});
					});
					</script>		
		 </td>
	  </tr> 	  
	<? $tabControl->Buttons(); ?>
			<input type="button" name="save" onclick="stertgenerate(this,1); return false;" value="<?=Loc::getMessage(MODULE_ID.'_GENERATOR_START')?>" title="<?=Loc::getMessage(MODULE_ID.'_GENERATOR_START')?>" class="adm-btn-save">
			<script type="text/javascript">
				function givmeparams(id) {
				  if ( BX('generator_iblock').value != '' && BX('generator_typeid').value != '' ) {
					var oPostData = {
							'iblock_id': BX('generator_iblock').value,
							'type_id': BX('generator_typeid').value,
						};					
					BX.ajax.loadJSON('<?=$APPLICATION->GetCurPage()?>?givmeparams=y',oPostData,
						function (data) {
							document.getElementById(id).value = data.URL;
						}
					);							
				  } else {
						var btn_save = {
						   title: '<?=Loc::getMessage(MODULE_ID.'_GENERATOR_OK')?>',
						   id: 'savebtn',
						   name: 'savebtn',
						   className: BX.browser.IsIE() && BX.browser.IsDoctype() && !BX.browser.IsIE10() ? '' : 'adm-btn-save',
						   action: function () {
							  BX.adminPanel.closeWait();
							  this.parentWindow.Close();
						   }
						};					  
						var Dialog = new BX.CDialog({
						   title: "<?=Loc::getMessage(MODULE_ID.'_GENERATOR_ERROR')?>",
						   content: '<?=Loc::getMessage(MODULE_ID.'_GENERATOR_NECESSARILY')?>:<br /><br /> <span style="color: red; font-weight: bold;" ><?=Loc::getMessage(MODULE_ID.'_GENERATOR_IBLOCK')?><br /><?=Loc::getMessage(MODULE_ID.'_GENERATOR_TYPE')?></spam>',
						   icon: 'head-block',
						   resizable: false,
						   draggable: false,
						   height: '168',
						   width: '400',
						   buttons: [btn_save]
						});					  
						Dialog.Show();
				  }
				}
				function stertgenerate(el,page) {
					 if ( BX('generator_iblock').value != '' && BX('generator_typeid').value != '' && BX('generator_siteid').value != '' && BX('generator_FIRSTURL').value != '' && BX('generator_LASTURL').value != '' ) {
							BX.show(BX("progress_procent"));
							var oPostData = {
								'iblock_id': BX('generator_iblock').value,
								'site_id': BX('generator_siteid').value,
								'type_id': BX('generator_typeid').value,
								'firsturl': BX('generator_FIRSTURL').value,
								'lasturl': BX('generator_LASTURL').value,
								'page': page,
							};
							BX.ajax.loadJSON('<?=$APPLICATION->GetCurPage()?>?work_start=y',oPostData,
										function (data) {
											 BX('progress_procent').innerHTML = data.HTML;
											 if ( data.ALLPAGE == data.PAGE ) {
												 BX.adminPanel.closeWait();
											 } else {
												 stertgenerate(el,parseInt(data.PAGE) + 1);
											 }
										}
							);	
					 } else {
						var btn_save = {
						   title: '<?=Loc::getMessage(MODULE_ID.'_GENERATOR_OK')?>',
						   id: 'savebtn',
						   name: 'savebtn',
						   className: BX.browser.IsIE() && BX.browser.IsDoctype() && !BX.browser.IsIE10() ? '' : 'adm-btn-save',
						   action: function () {
							  BX.adminPanel.closeWait();
							  this.parentWindow.Close();
						   }
						};						 
						var Dialog = new BX.CDialog({
						   title: "<?=Loc::getMessage(MODULE_ID.'_GENERATOR_ERROR')?>",
						   content: '<?=Loc::getMessage(MODULE_ID.'_GENERATOR_NECESSARILY')?>:<br /><br /> <span style="color: red; font-weight: bold;" ><?=Loc::getMessage(MODULE_ID.'_GENERATOR_IBLOCK')?><br /><?=Loc::getMessage(MODULE_ID.'_GENERATOR_SITE')?><br /><?=Loc::getMessage(MODULE_ID.'_GENERATOR_TYPE')?><br /><?=Loc::getMessage(MODULE_ID.'_GENERATOR_FIRSTURL')?><br /><?=Loc::getMessage(MODULE_ID.'_GENERATOR_LASTURL')?></spam>',
						   icon: 'head-block',
						   resizable: false,
						   draggable: false,
						   height: '168',
						   width: '400',
						   buttons: [btn_save]
						});					  
							
							Dialog.Show();
					 }
				}
			</script>
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
}
?>