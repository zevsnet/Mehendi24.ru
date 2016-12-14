<?php
/**
 * @define THIS_FILE - ��������� ������������ � ����� �������� 
 */
@define("MODULE_ID", basename(dirname(dirname(THIS_FILE))));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
// �������� ����
$RIGHT = $APPLICATION->GetGroupRight(MODULE_ID);
if ($RIGHT <= "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
	
// �������� ORM ��������, ������ �� ����� �����
$sEntity = 'Blackbutterfly\\Seoredirektprod7\\';
foreach (explode('_', str_replace('__edit.php', '', basename(THIS_FILE))) as $k)
{
    $sEntity.= ucfirst($k);
}
$sEntity .= 'Table';	

// �������� ������� ��� ������ � ��������������
$pageEdit = MODULE_ID . '_' . basename(THIS_FILE);
$pageList = str_replace('__edit', '__list', $pageEdit);

// ����������� �������� ������
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

// ����������� �������
Bitrix\Main\Loader::includeModule(MODULE_ID);

//��������� 	
	$folders = str_replace(array('__list','__edit'),array('','') , THIS_FILE);
	$fileinclude = str_replace(array('__list','__edit'),array('','') , basename(THIS_FILE));
	$incfiles = str_replace($fileinclude,'params/'.$fileinclude,$folders);
	include($incfiles);
// ����������� css/js
CJSCore::Init(array("jquery"));
CUtil::InitJSCore(array('window'));
//$APPLICATION->addHeadScript('/bitrix/js/blackbutterfly/'.MODULE_ID.'/main.js');

// ��������
$ID = intval($ID);		// ������������� ������������� ������
$message = null;		// ��������� �� ������

//���������� ����
$tabControl = new \CAdminTabControl("tabControl", $aTabs);

if ( $REQUEST_METHOD == "POST" && ($save!="" || $apply!="") && check_bitrix_sessid() ) {
	$errorsmessage = array();
	foreach ( $arPole as $tabs ) {	
		foreach ( $tabs as $pole ) {
			if ( $pole['NOTEMPTY'] == 'Y' and $_POST['pole_'.$pole['VARIABLE']] == '' and $pole['TYPE'] != 'FILE' ) {
					$errorsmessage[] = Loc::getMessage("NOTEMPTY_ERRORS")." ".$pole['NAME'];
			} else {
				if ( $pole['NOTEMPTY'] == 'Y' and $pole['TYPE'] == 'FILE' and $_FILES['pole_'.$pole['VARIABLE']]['name'] == '' ) {
					$errorsmessage[] = Loc::getMessage("NOTEMPTY_ERRORS")." ".$pole['NAME'];
				}			
			}
			if ( $pole['TYPE'] != 'FILE' ) {

				if ($pole['MULTY'] == "Y") {
					$arFields[$pole['VARIABLE']] = serialize($_POST['pole_'.$pole['VARIABLE']]);
				} else {
					if ( $pole['TYPE'] == 'CHECKBOX' and  !isset($_POST['pole_'.$pole['VARIABLE']])) {
						$arFields[$pole['VARIABLE']] = 'N';
					} else {
						$arFields[$pole['VARIABLE']] = trim($_POST['pole_'.$pole['VARIABLE']]);
					}
			    } 
			}else {
			if ( $_POST['pole_'.$pole['VARIABLE']."_del"] == '' ) {
			   if ( $_FILES['pole_'.$pole['VARIABLE']]['name'] != '' ) {
					$arr_file=Array(
						"name" => $_FILES['pole_'.$pole['VARIABLE']]['name'],
						"size" => $_FILES['pole_'.$pole['VARIABLE']]['size'],
						"tmp_name" => $_FILES['pole_'.$pole['VARIABLE']]['tmp_name'],
						"type" => "",
						"old_file" => "",
						"del" => "Y",
						"MODULE_ID" => MODULE_ID
					);
					$fid = CFile::SaveFile($arr_file, MODULE_ID);
					$arFields[$pole['VARIABLE']] = $fid;
				}
				} else {
					$rs = $sEntity::getById($ID);
					$ar = $rs->fetch();	
					CFile::Delete($ar[$pole['VARIABLE']]);
					$arFields[$pole['VARIABLE']] = '';			  
			  }				
			}
		
			$ar[$pole['VARIABLE']] = $_POST['pole_'.$pole['VARIABLE']];
		}
	}
   if (!isset($errorsmessage[0])) {
		if ($ID > 0) {
			if ($take == 'yes') {
				\Blackbutterfly\Seoredirektprod7\Functions::takeElement($_POST['pole_FIRSTURL'], $_POST['pole_LASTURL'], $_POST['pole_TYPEBLOCK'], $sEntity,$ID);
			}		
			$res = $sEntity::update($ID, $arFields);
		} else {
			if ($take == 'yes') {
				\Blackbutterfly\Seoredirektprod7\Functions::takeElement($_POST['pole_FIRSTURL'], $_POST['pole_LASTURL'], $_POST['pole_TYPEBLOCK'], $sEntity,'');
			}
			$ID = $sEntity::add($arFields)->getId();
			$res = ($ID > 0);
		}
	}
	if($res) {
		if ($apply != "")
			LocalRedirect("/bitrix/admin/".$pageEdit."?ID=".$ID."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
		else
			LocalRedirect("/bitrix/admin/".$pageList."?lang=".LANG);
	}
}

// ������
if($ID>0 and !isset($errorsmessage[0]))
{
	// ��� �������������� �������� ������
	$rs = $sEntity::getById($ID);
	$ar = $rs->fetch();
}

//����� �����

// ��������� ��������� ��������
$APPLICATION->SetTitle($title);

// �� ������� ��������� ���������� ������ � �����
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

// ������������ ����������������� ����
$aMenu = array(
  array(
    "TEXT"=>Loc::getMessage(MODULE_ID.'_PREW'),
    "TITLE"=>Loc::getMessage(MODULE_ID.'_PREW'),
    "LINK"=>"/bitrix/admin/".$pageList."?lang=".LANG,
    "ICON"=>"btn_list",
  )
);
$context = new CAdminContextMenu($aMenu);
$context->Show();

// ���� ���� ��������� �� ������� ��� �� �������� ���������� - ������� ��.
if($_REQUEST["mess"] == "ok" && $ID>0)
  CAdminMessage::ShowMessage(array("MESSAGE"=>Loc::getMessage(MODULE_ID."_SAVED_SUCCESS"), "TYPE"=>"OK"));

if(isset($errorsmessage[0])) {
  CAdminMessage::ShowMessage(array("MESSAGE"=>Loc::getMessage(MODULE_ID."_SAVED_ERRORS")."<br />".implode("<br />", $errorsmessage), "TYPE"=>"ERROR"));
}
?>
	<form method="POST" Action="<?echo $APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form">  
	<?// �������� �������������� ������ ?>
		<?echo bitrix_sessid_post();?>
	<?
	// ��������� ��������� ��������
		$tabControl->Begin();
		foreach ( $aTabs as $tab ) {
			$tabControl->BeginNextTab();
			foreach ( $arPole[$tab['DIV']] as $pole ) {
				?>
					  <tr>
						<td class="<? if ( ($pole['TYPE'] == 'CHECKBOX' or $pole['TYPE'] == 'RADIO') and is_array($pole['VALUE']) ) {?>adm-detail-valign-top<?} ?>" width="28%"  ><?if($pole['NOTEMPTY'] == 'Y' ){ ?><strong><span class="required">*</span><? } ?> <?=$pole['NAME']?><?if($pole['NOTEMPTY'] == 'Y' ){ ?></strong><? } ?></td>
						<td width="70%">
									<?
										switch($pole['TYPE'])
										{
										  case 'INPUT':
												if ( $pole['SIZE'] == 'S' ) {
													$params = 'maxlength="10" size="7"';
													if ( $ar[$pole['VARIABLE']] == '' ) {
														if ( isset($pole['DEFAULT']) ) {
															$ar[$pole['VARIABLE']] = $pole['DEFAULT'];
														} else {
															$ar[$pole['VARIABLE']] = '';
														}
													}
												} elseif ( $pole['SIZE'] == 'M' ) {
														$params = 'maxlength="20" size="20"';
														if ( $ar[$pole['VARIABLE']] == '' ) {
															if ( isset($pole['DEFAULT']) ) {
																$ar[$pole['VARIABLE']] = $pole['DEFAULT'];
															} else {
																$ar[$pole['VARIABLE']] = '';
															}
														}
												} else {
													$params = 'style="width: 100%;"';
												}
												echo "<div style='width: 80%' >";
													echo '<input type="text" '.$params.' name="pole_'.$pole['VARIABLE'].'" id="'.$pole['VARIABLE'].'" value="'.$ar[$pole['VARIABLE']].'" />';
												echo "</div>";
												break;
										  case 'INPUTREADONLY':
													if ( $pole['SIZE'] == 'S' ) {
														$params = 'maxlength="10" size="7"';
														if ( $ar[$pole['VARIABLE']] == '' ) {
															if ( isset($pole['DEFAULT']) ) {
																$ar[$pole['VARIABLE']] = $pole['DEFAULT'];
															} else {
																$ar[$pole['VARIABLE']] = '';
															}
														}
													} elseif ( $pole['SIZE'] == 'M' ) {
														$params = 'maxlength="20" size="20"';
														if ( $ar[$pole['VARIABLE']] == '' ) {
															if ( isset($pole['DEFAULT']) ) {
																$ar[$pole['VARIABLE']] = $pole['DEFAULT'];
															} else {
																$ar[$pole['VARIABLE']] = '';
															}
														}
													} else {
														$params = 'style="width: 100%;"';
													}
													if ( $ar[$pole['VARIABLE']] != '' ) {
														$val = $ar[$pole['VARIABLE']];
													} else {
														$val = date('U');
													}
													echo "<div style='width: 80%' >";
														echo '<input type="text" '.$params.' readonly="readonly" value="'.date('d.m.Y H:i:s',$val).'" />
															  <input type="hidden" '.$params.'  name="pole_'.$pole['VARIABLE'].'" id="'.$pole['VARIABLE'].'" value="'.$val.'" />
														';
													echo "</div>";										  
												break;
										  case 'USERREADONLY':
													if ( $ar[$pole['VARIABLE']] != '' ) {
														$val = $ar[$pole['VARIABLE']];
														$rsUser = CUser::GetByID($val);
														$arUser = $rsUser->Fetch();
														$valtext = $arUser['LAST_NAME'].' '.$arUser['NAME'];
													} else {
														$val = $USER->GetID();
														$valtext = $USER->GetFullName();
													}
													$params = 'style="width: 100%;"';
													echo "<div style='width: 80%' >";
														echo '<input type="text" '.$params.' readonly="readonly" value="['.$val.'] '.$valtext.'" />
															  <input type="hidden" '.$params.'  name="pole_'.$pole['VARIABLE'].'" id="'.$pole['VARIABLE'].'" value="'.$USER->GetID().'" />
														';
													echo "</div>";										  
												break;											
										  case 'CHECKBOX':
												  if ($pole['MULTY'] == "Y") {
													foreach ($pole['VALUE']['REFERENCE'] as $index=>$val) {
														echo InputType("checkbox", "pole_".$pole['VARIABLE']."[".$pole['VALUE']['REFERENCE_ID'][$index]."]", "Y", $ar[$pole['VARIABLE']][$pole['VALUE']['REFERENCE_ID'][$index]])." ".$val."<br />";
													}
												  } else {
													 if ( $pole['DEFAULTCHECK'] == 'Y' and $ar[$pole['VARIABLE']] == '' ) {
														$ar[$pole['VARIABLE']] = 'Y';
													 }
														echo InputType("checkbox", "pole_".$pole['VARIABLE'], "Y", $ar[$pole['VARIABLE']]);
												  }
												break;
										  case 'RADIO':
												  if (is_array($pole['VALUE'])) {
													foreach ($pole['VALUE'] as $index=>$val) {
														echo InputType("radio", "pole_".$pole['VARIABLE'], $val, $ar[$pole['VARIABLE']])." ".$val."<br />";
													}
												  }
												break;												
										  case 'TEXT':
										        echo "<div style='width: 80%' >";
												CFileMan::AddHTMLEditorFrame(
														"pole_".$pole['VARIABLE'],
														$ar[$pole['VARIABLE']],
														"PREVIEW_TEXT_TYPE",
														'html',
														array(
															'height' => 450,
															'width' => '100%'
														),
														"Y",
														0,
														"",
														"",
														"",
														true,
														false,
														array(
															'toolbarConfig' => CFileman::GetEditorToolbarConfig("iblock_".(defined('BX_PUBLIC_MODE') && BX_PUBLIC_MODE == 1 ? 'public' : 'admin')),
														)
												);
												echo "</div>";
												break;
										  case 'SELECT':
												echo SelectBoxFromArray("pole_".$pole['VARIABLE'], $pole['VALUE'], $ar[$pole['VARIABLE']], '', '',false,'');
												break;
										  case 'MSELECT':
												echo SelectBoxMFromArray("pole_".$pole['VARIABLE'].'[]', $pole['VALUE'], $ar[$pole['VARIABLE']]);
												break;
										   case 'FILE':
													echo CFileInput::Show("pole_".$pole['VARIABLE'], $ar[$pole['VARIABLE']], array(
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
																));
												break; 										  case 'TEMPLATE':
												echo '
														<div style="float: left; margin-right: 20px;" >
															<textarea class="typearea" name="pole_'.$pole['VARIABLE'].'" id="pole_'.$pole['VARIABLE'].'" cols="65" rows="15" wrap="VIRTUAL">'.$ar[$pole['VARIABLE']].'</textarea>
														</div>
														<div>';
														foreach ($pole['VALUE']['REFERENCE_ID'] as $index=>$val) {														
																echo '<a onclick="changemyblockcode(\'pole_'.$pole['VARIABLE'].'\',\'#'.$val.'#\'); return false;" href="#">#'.$val.'#</a> - '.$pole['VALUE']['REFERENCE'][$index].'<br/>';
														}
												echo '</div>
													';
												break;
										}
									?>
						</td>
					  </tr>   
				<?
			}
		}
	?>	
	
	  
<?
// ���������� ����� - ����� ������ ���������� ���������
$tabControl->Buttons(
  array(
  //    "disabled"=>($POST_RIGHT<"W"),
    "back_url"=>$pageList."?lang=".LANG,
  )
);
?>
<input type="hidden" name="lang" value="<?=LANG?>">
<?if($ID>0 && !$bCopy):?>
  <input type="hidden" name="ID" value="<?=$ID?>">
<?endif;?>
<?
// ��������� ��������� ��������
$tabControl->End();
?>
<?
// �������������� ����������� �� ������� - ����� ������ ����� ����, � ������� �������� ������
$tabControl->ShowWarnings("post_form", $message);
?>
<?
// �������������: ������������ ���������� ��������, ���� ���������.
?>
<?
// �������������� ���������
echo BeginNote();?>
<span class="required">*</span><?=GetMessage('REQUIRED_FIELDS')?>
<?echo EndNote();?>

<?
// ���������� ��������
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>