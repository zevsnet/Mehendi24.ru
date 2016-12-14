<?
class CASDOrderPrintUtil {
	public static function AddAction(&$list, $name, $html) {
		$list->arActions['asd_orderprint'] = $name;
		$list->arActions['asd_orderprint_html'] = array('type' => 'html', 'value' => $html);
		$list->arActionsParams['select_onchange'] .= "BX('asd_orderprint_form').style.display = (this.value == 'asd_orderprint'? 'block':'none');";
	}
	public static function GetPrintReports() {
		$arSysLangs = array();
		$db_lang = CLangAdmin::GetList(($b='sort'), ($o='asc'), array('ACTIVE' => 'Y'));
		while ($arLang = $db_lang->Fetch()) {
			$arSysLangs[] = $arLang['LID'];
		}
		$arReports = array();
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/reports/')) {
			if ($handle = opendir($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/reports/')) {
				while (($file = readdir($handle)) !== false) {
					if ($file == '.' || $file == '..') {
						continue;
					}
					if (is_file($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/reports/'.$file) && strtoupper(substr($file, strlen($file)-4))=='.PHP') {
						$rep_title = $file;
						$file_contents = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/reports/'.$file);

						$rep_langs = '';
						$arMatches = array();
						if (preg_match("#<title([\s]+langs[\s]*=[\s]*\"([^\"]*)\"|)[\s]*>([^<]*)</title[\s]*>#i", $file_contents, $arMatches)) {
							$arMatches[3] = Trim($arMatches[3]);
							if (strlen($arMatches[3])>0) {
								$rep_title = $arMatches[3];
							}
							$arMatches[2] = Trim($arMatches[2]);
							if (strlen($arMatches[2])>0) {
								$rep_langs = $arMatches[2];
							}
						}

						if (strlen($rep_langs)>0) {
							$bContinue = True;
							for ($ic = 0; $ic < count($arSysLangs); $ic++) {
								if (strpos($rep_langs, $arSysLangs[$ic])!==false) {
									$bContinue = False;
									break;
								}
							}
							if ($bContinue) {
								continue;
							}
						}

						$file = substr($file, 0, -4);
						$arReports[$file] = array(
								'FILE' => $file,
								'TITLE' => $rep_title
							);
					}
				}
			}
			closedir($handle);
		}

		if ($handle = opendir($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/reports/')) {
			while (($file = readdir($handle)) !== false) {
				if ($file == '.' || $file == '..') {
					continue;
				}
				if (is_file($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/reports/'.$file)
					&& !in_array($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/reports/'.$file, $arReports)
					&& strtoupper(substr($file, strlen($file)-4))=='.PHP'
					) {
					$rep_title = $file;
					if (is_file($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/ru/reports/'.$file)
						&& strtoupper(substr($file, strlen($file)-4))=='.PHP'
						) {
						$file_contents = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/ru/reports/'.$file);
					} else {
						$file_contents = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/sale/reports/'.$file);
					}

					$rep_langs = '';
					$arMatches = array();
					if (preg_match("#<title([\s]+langs[\s]*=[\s]*\"([^\"]*)\"|)[\s]*>([^<]*)</title[\s]*>#i", $file_contents, $arMatches)) {
						$arMatches[3] = Trim($arMatches[3]);
						if (strlen($arMatches[3])>0) {
							$rep_title = $arMatches[3];
						}
						$arMatches[2] = Trim($arMatches[2]);
						if (strlen($arMatches[2])>0) {
							$rep_langs = $arMatches[2];
						}
					}

					if (strlen($rep_langs)>0) {
						$bContinue = True;
						for ($ic = 0; $ic < count($arSysLangs); $ic++) {
							if (strpos($rep_langs, $arSysLangs[$ic])!==false) {
								$bContinue = False;
								break;
							}
						}
						if ($bContinue) {
							continue;
						}
					}

					$file = substr($file, 0, -4);
					if (!isset($arReports[$file])) {
						$arReports[$file] = array(
								'FILE' => $file,
								'TITLE' => $rep_title
							);
					}
				}
			}
		}
		closedir($handle);
		return $arReports;
	}
}
?>