<?
if (!class_exists("BLACKSeoredirekt"))
{
	class BLACKSeoredirekt
	{
/*
	Действие перед обновлением элемента
*/
		function OnBeforeIBlockElementUpdateHandler(&$arFields)
		{
				$res = CIBlockElement::GetByID($arFields["ID"]);
				if($ar_res = $res->GetNext()) {		
					$urlel = $ar_res['DETAIL_PAGE_URL'];
					COption::SetOptionString("blackbutterfly.seoredirektprod7","LOSTURLEL",$urlel);
				}
		}
/*
	Действие перед обновлением раздела
*/		
		function OnBeforeIBlockSectionUpdateHandler(&$arFields)
		{
				$res = CIBlockSection::GetByID($arFields["ID"]);
				if($ar_res = $res->GetNext()) {		
					$urlel = $ar_res['SECTION_PAGE_URL'];
					$urlelcode = $ar_res['CODE'];
					COption::SetOptionString("blackbutterfly.seoredirektprod7","LOSTURLSEC",$urlel);
					COption::SetOptionString("blackbutterfly.seoredirektprod7","LOSTURLSECCODE",$urlelcode);
				}
		}	
/*
	Действие после обновления элемента. Если изменился URL добавляем редирект
*/		
		function OnAfterIBlockElementUpdateHandler(&$arFields)
		{
			if ( $arFields['ID'] != '' ) {
				$resiblock = CIBlock::GetByID($arFields["IBLOCK_ID"]);
				if($ar_resiblock = $resiblock->GetNext()) {
					$ssiteid = $ar_resiblock['LID'];
				}				
				$url = COption::GetOptionString("blackbutterfly.seoredirektprod7", "LOSTURLEL");
				$res = CIBlockElement::GetByID($arFields["ID"]);
				$type = 'element';
					if($ar_res = $res->GetNext()) {
						if ($ar_res['DETAIL_PAGE_URL'] != $url and $url != '' ) {
						 /* Защита от циклических переходов. */
							 $rs = Blackbutterfly\Seoredirektprod7\SeoredirektTable::getList(array('filter' => array('FIRSTURL' => $ar_res['DETAIL_PAGE_URL'], "LASTURL"=> $url)));
								while ($ar = $rs->fetch()) {
									\Blackbutterfly\Seoredirektprod7\Functions::deleteByIdWithEntityName($ar['ID'] , 'Blackbutterfly\Seoredirektprod7\SeoredirektTable');									
								}
						 /* ----------------------- */		
							global $USER;						 
							$massarray = array(
								"FIRSTURL"=> $url,
								"LASTURL"=> $ar_res['DETAIL_PAGE_URL'],
								"TYPEBLOCK"=> $type,
								"SITEID"=> $ssiteid,
								"ADDURLDATE" => date('U'),
								"USERID"=> $USER->GetID()
							);
							Blackbutterfly\Seoredirektprod7\SeoredirektTable::add($massarray);
							COption::SetOptionString("blackbutterfly.seoredirektprod7","LOSTURLEL",'');
						} else {
							COption::SetOptionString("blackbutterfly.seoredirektprod7","LOSTURLEL",'');
						}
					}
			} else {
				COption::SetOptionString("blackbutterfly.seoredirektprod7","LOSTURLEL",'');
			}
		}
/*
	Действие после обновления раздела. Если изменился URL добавляем редирект
*/
		function OnAfterIBlockSectionUpdateHandler(&$arFields)
		{
			if ( $arFields['ID'] != '' ) {
				$resiblock = CIBlock::GetByID($arFields["IBLOCK_ID"]);
				if($ar_resiblock = $resiblock->GetNext()) {
					$ssiteid = $ar_resiblock['LID'];
				}
 
				$url = COption::GetOptionString("blackbutterfly.seoredirektprod7", "LOSTURLSECCODE");
				$res = CIBlockSection::GetByID($arFields["ID"]);
				$type = 'folders';
					if($ar_res = $res->GetNext()) {
						if ($ar_res['CODE'] != $url and $url != '' ) {
							$newurl = $ar_res['SECTION_PAGE_URL'];
							$lasturl = COption::GetOptionString("blackbutterfly.seoredirektprod7", "LOSTURLSEC");
						 /* Защита от циклических переходов. */
							 $rs = Blackbutterfly\Seoredirektprod7\SeoredirektTable::getList(array('filter' => array('FIRSTURL' => $newurl, "LASTURL"=> $lasturl)));
								while ($ar = $rs->fetch()) {
									\Blackbutterfly\Seoredirektprod7\Functions::deleteByIdWithEntityName($ar['ID'] , 'Blackbutterfly\Seoredirektprod7\SeoredirektTable');									
								}
						 /* ----------------------- */
							global $USER;
							$massarray = array(
								"FIRSTURL"=> $lasturl,
								"LASTURL"=> $newurl,
								"TYPEBLOCK"=> $type,
								"SITEID"=> $ssiteid,
								"ADDURLDATE" => date('U'),
								"USERID"=> $USER->GetID()
							);
							Blackbutterfly\Seoredirektprod7\SeoredirektTable::add($massarray);							
							COption::SetOptionString("blackbutterfly.seoredirektprod7","LOSTURLSEC",'');
							COption::SetOptionString("blackbutterfly.seoredirektprod7","LOSTURLSECCODE",'');
						} else {
							COption::SetOptionString("blackbutterfly.seoredirektprod7","LOSTURLSEC",'');
							COption::SetOptionString("blackbutterfly.seoredirektprod7","LOSTURLSECCODE",'');
						}
					}
			} else {
				COption::SetOptionString("blackbutterfly.seoredirektprod7","LOSTURLSEC",'');
				COption::SetOptionString("blackbutterfly.seoredirektprod7","LOSTURLSECCODE",'');
			}
		}	
/*
	Проверка редиректов и переход.
*/		
		function Redirektlist() {
			global $APPLICATION;
			global $DB;
		  if ( COption::GetOptionString("blackbutterfly.seoredirektprod7", "SEOPRO_ELEMENT_GET") != 'Y' ) {
			//$dir = $APPLICATION->GetCurDir();
			$parsurl = parse_url($APPLICATION->GetCurPageParam());
			$dir = $parsurl['path'];
		  } else {
			$dir = $_SERVER['REQUEST_URI'];
		  }
		  $massiblockurl = array();
		/*Начало поиска урла раздела*/
					CModule::IncludeModule("iblock");
					$res = CIBlock::GetList(
						Array(), 
						Array(
							'SITE_ID'=>SITE_ID, 
							'ACTIVE'=>'Y' 
						), false
					);
					while($ar_res = $res->Fetch())
					{
					 if ( $ar_res['SECTION_PAGE_URL'] != '' ) {
						$chast = explode('/',$ar_res['SECTION_PAGE_URL']);
						$start = 1;
						$mass = array();
						
						foreach ( $chast as $url ) {
							if ( substr_count($url, '#') < 1 and $start != 0 ) {
								$mass[] = $url;
								$start = 0;
							}
						}
						$massiblockurl[] = '/'.implode("/", $mass).'/';
					  }
					}
				foreach ( $massiblockurl as $blocks) {
					if ( substr_count($dir, $blocks) == 1 ) {
						$newurl = str_replace($blocks,'',$dir);
						$massnewurl = explode('/',$newurl);
						$actfolders = 1;
						$searchurl = $blocks."".$massnewurl[0]."/";
					}
				}
		/* ---------------------------------- */
		/* Если урл подходит под правило раздела */
				if ( $actfolders == 1 ) {
						$rs = Blackbutterfly\Seoredirektprod7\SeoredirektTable::getList(array('filter' => array('FIRSTURL' => "%".$searchurl."%", "TYPEBLOCK"=>'folders','SITEID'=>SITE_ID)));
						while ($ar = $rs->fetch()) {
							if ( $massnewurl[1] != '' ) {
								$ar['LASTURL'] = $ar['LASTURL']."".$massnewurl[1]."/";
							}
							if ( $_SERVER['QUERY_STRING'] != '' ) {
								$ar['LASTURL'] = $ar['LASTURL']."?".$_SERVER['QUERY_STRING'];
							}
							header("HTTP/1.1 301 Moved Permanently");
							header("Location:".str_replace($dir,$ar['LASTURL'],$dir));
							exit;							
						}
				} else {
					$massdir = explode('/',$dir);
					for ($i = count($massdir)-2; $i > 1; $i--) {
						$tt = array();
						for ($t = 1; $t <= $i; $t++) {
							$tt[] = $massdir[$t];
						}
							$rs = Blackbutterfly\Seoredirektprod7\SeoredirektTable::getList(array('filter' => array('FIRSTURL' => implode('/', $tt), "TYPEBLOCK"=>'folders','SITEID'=>SITE_ID)));
							while ($ar = $rs->fetch()) {
								if ( $ar['LASTURL'] != $dir and "/".implode('/', $tt) != $dir  ) {
									header("HTTP/1.1 301 Moved Permanently");
									header("Location:".str_replace("/".implode('/', $tt)."/",$ar['LASTURL'],$dir));
									exit;	
								}								

							}
					}
					$rs = Blackbutterfly\Seoredirektprod7\SeoredirektTable::getList(array('filter' => array('FIRSTURL' => $dir, "TYPEBLOCK"=>'folders','SITEID'=>SITE_ID)));
							while ($ar = $rs->fetch()) {
									header("HTTP/1.1 301 Moved Permanently");
									header("Location:".$ar['LASTURL']);
									exit;
							}
				}
		/* ---------------------------------- */
		/* Редирект для типа элемент */
		  if ( COption::GetOptionString("blackbutterfly.seoredirektprod7", "SEOPRO_ELEMENT_GET") != 'Y' ) {
			//$dir = $APPLICATION->GetCurDir();
			$parsurl = parse_url($APPLICATION->GetCurPageParam());
			$dir = $parsurl['path'];			
		  } else {
			$dir = $_SERVER['REQUEST_URI'];
		  }
			$rs = Blackbutterfly\Seoredirektprod7\SeoredirektTable::getList(array('filter' => array('FIRSTURL' => $dir, "TYPEBLOCK"=>'element','SITEID'=>SITE_ID)));
			while ($ar = $rs->fetch()) {
						header("HTTP/1.1 301 Moved Permanently");
						header("Location:".$ar['LASTURL']);
						exit;						
			}
		/*--------------------------------*/
		/* WWW и без него */
			if ( COption::GetOptionString("blackbutterfly.seoredirektprod7", "SEOPRO_404_WWWNOTWWW") == 1 ) {
				//на без www
				if (substr_count($_SERVER['HTTP_HOST'],'www.') > 0 ) {
						header("HTTP/1.1 301 Moved Permanently");
						header("Location: http://".str_replace("www.","",$_SERVER['HTTP_HOST'])."".$dir);
						exit;				
				}
			}
			if ( COption::GetOptionString("blackbutterfly.seoredirektprod7", "SEOPRO_404_WWWNOTWWW") == 2 ) {
				//на www
				if (substr_count($_SERVER['HTTP_HOST'],'www.') < 1 ) {
						header("HTTP/1.1 301 Moved Permanently");
						header("Location: http://www.".$_SERVER['HTTP_HOST']."".$dir);
						exit;				
				}				
			}			
		/*--------------------------------*/
		/* Проверяем на index.php */
			if ( COption::GetOptionString("blackbutterfly.seoredirektprod7", "SEOPRO_NOTINDEX") == 'Y' ) {
				if (substr_count($_SERVER['REQUEST_URI'], 'index.php')> 0 ) {
					$dir = str_replace("index.php","",$_SERVER['REQUEST_URI']);
						header("HTTP/1.1 301 Moved Permanently");
						header("Location: http://".$_SERVER['HTTP_HOST']."".$dir);
						exit;					
				}
			}
		/*--------------------------------*/
		/* Проверяем на / в конце */
			if ( COption::GetOptionString("blackbutterfly.seoredirektprod7", "SEOPRO_NOSLESH") == 'Y' ) {
					$parsurl = parse_url($_SERVER['REQUEST_URI']);
					if ( substr($parsurl['path'], -1) != '/' and substr($parsurl['path'], -4) != '.php' and substr($parsurl['path'], -4) != '.css' and substr($parsurl['path'], -3) != '.js' ) {
						$dir = str_replace($parsurl['path'],$parsurl['path']."/",$_SERVER['REQUEST_URI']);
						header("HTTP/1.1 301 Moved Permanently");
						header("Location: http://".$_SERVER['HTTP_HOST']."".$dir);
						exit;							
					}
			}
		/*--------------------------------*/		
		}
/*
	Добавление 404 ошибки в список
*/		
		function Checknew404Error()
		{
		  global $APPLICATION;
		  global $DB;		
		  if ( COption::GetOptionString("blackbutterfly.seoredirektprod7", "SEOPRO_404_GET") != 'Y' ) {
			//$dir = $APPLICATION->GetCurDir();
			$parsurl = parse_url($APPLICATION->GetCurPageParam());
			$dir = $parsurl['path'];			
		  } else {
			$dir = $_SERVER['REQUEST_URI'];
		  }
		   if (defined('ERROR_404') && ERROR_404=='Y' && $dir != '/bitrix/admin/' && $dir != '/' )
		   {
			$yes = 0;
			   $rs = Blackbutterfly\Seoredirektprod7\SeoredirekterrorTable::getList(array('filter' => array('URL' => $dir,'REFERALURL'=>$_SERVER['HTTP_REFERER'])));
				while ($ar = $rs->fetch()) {
					$yes = 1;
					$ar['COUNTSETURL'] = $ar['COUNTSETURL'] + 1;
					$res = Blackbutterfly\Seoredirektprod7\SeoredirekterrorTable::update($ar['ID'],$ar);
				}
				if ( $yes == 0 ) {
					$rs = Blackbutterfly\Seoredirektprod7\SeoredirektignoreTable::getList(array('filter' => array('URL' => $dir)));
					while ($ar = $rs->fetch()) {
						$yes = 1;
					}
					if ( $yes == 0 ) {
						$massarray = array(
							"URL"=> $dir,
							"COUNTSETURL"=>1,
							"SITEID"=> SITE_ID,
							"REFERALURL"=>$_SERVER['HTTP_REFERER']
						);
						Blackbutterfly\Seoredirektprod7\SeoredirekterrorTable::add($massarray);
					}
				}
				if ( COption::GetOptionString("blackbutterfly.seoredirektprod7", "SEOPRO_404_PAGEURL") != '' and $dir != COption::GetOptionString("blackbutterfly.seoredirektprod7", "SEOPRO_404_PAGEURL") ) {
						header("HTTP/1.1 301 Moved Permanently");
						header("Location:".COption::GetOptionString("blackbutterfly.seoredirektprod7", "SEOPRO_404_PAGEURL"));
						exit;				
				}
		   }
		}		
	}
}
?>