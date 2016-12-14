<?
IncludeModuleLangFile(__FILE__);
Class blackbutterfly_seoredirektprod7 extends CModule {

	const MODULE_ID = 'blackbutterfly.seoredirektprod7';
	var $MODULE_ID = 'blackbutterfly.seoredirektprod7';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';
	var $MODULE_GROUP_RIGHTS = "Y";

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("blackbutterfly.seoredirektprod7_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("blackbutterfly.seoredirektprod7_MODULE_DESC");
		$this->PARTNER_NAME = GetMessage("blackbutterfly.seoredirektprod7_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("blackbutterfly.seoredirektprod7_PARTNER_URI");
	}
		
	function InstallDB($arParams = array())
	{
        global $DB, $DBType;        
        if (file_exists($file = dirname(__FILE__).'/'.$DBType.'/install.sql'))
        {
            $this->strError = $DB->RunSQLBatch($file);
        }
		return true;
	}

	function UnInstallDB($arParams = array())
	{
        global $DB, $DBType;        
        if (file_exists($file = dirname(__FILE__).'/'.$DBType.'/uninstall.sql'))
        {
            $this->strError = $DB->RunSQLBatch($file);
        }
		return true;
	}	

	function InstallEvents()
	{	
		RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'Blackbutterflyseoredirektprod7', 'OnBuildGlobalMenu');
		RegisterModuleDependences("iblock", "OnBeforeIBlockElementUpdate",self::MODULE_ID, "BLACKSeoredirekt", "OnBeforeIBlockElementUpdateHandler");
		RegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate",self::MODULE_ID, "BLACKSeoredirekt", "OnAfterIBlockElementUpdateHandler");
		RegisterModuleDependences("iblock", "OnBeforeIBlockSectionUpdate",self::MODULE_ID, "BLACKSeoredirekt", "OnBeforeIBlockSectionUpdateHandler");
		RegisterModuleDependences("iblock", "OnAfterIBlockSectionUpdate",self::MODULE_ID, "BLACKSeoredirekt", "OnAfterIBlockSectionUpdateHandler");		
		RegisterModuleDependences("main", "OnPageStart", self::MODULE_ID, "BLACKSeoredirekt", "Redirektlist", "100");
		RegisterModuleDependences("main", "OnEpilog", self::MODULE_ID, "BLACKSeoredirekt", "Checknew404Error", "100");		
		return true;
	}	
	
	function UnInstallEvents()
	{
		UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'Blackbutterflyseoredirektprod7', 'OnBuildGlobalMenu');
		UnRegisterModuleDependences("iblock", "OnBeforeIBlockElementUpdate",self::MODULE_ID, "BLACKSeoredirekt", "OnBeforeIBlockElementUpdateHandler");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate",self::MODULE_ID, "BLACKSeoredirekt", "OnAfterIBlockElementUpdateHandler");
		UnRegisterModuleDependences("iblock", "OnBeforeIBlockSectionUpdate",self::MODULE_ID, "BLACKSeoredirekt", "OnBeforeIBlockSectionUpdateHandler");
		UnRegisterModuleDependences("iblock", "OnAfterIBlockSectionUpdate",self::MODULE_ID, "BLACKSeoredirekt", "OnAfterIBlockSectionUpdateHandler");		
		UnRegisterModuleDependences("main", "OnPageStart", self::MODULE_ID, "BLACKSeoredirekt", "Redirektlist", "100");
		UnRegisterModuleDependences("main", "OnEpilog", self::MODULE_ID, "BLACKSeoredirekt", "Checknew404Error", "100");		
		return true;
	}	
	
	function InstallFiles($arParams = array())
	{
		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/style",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/js/blackbutterfly/".self::MODULE_ID,
			true, true
		);	
		CopyDirFiles(
			$_SERVER["DOCUMENT_ROOT"]."/local/modules/".self::MODULE_ID."/style",
			$_SERVER["DOCUMENT_ROOT"]."/bitrix/js/blackbutterfly/".self::MODULE_ID,
			true, true
		);		
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || $item == 'menu.php')
						continue;
					file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item,
					'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.self::MODULE_ID.'/admin/'.$item.'");?'.'>');
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					CopyDirFiles($p.'/'.$item, $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/'.$item, $ReWrite = True, $Recursive = True);
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/local/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || $item == 'menu.php')
						continue;
					file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item,
					'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/local/modules/'.self::MODULE_ID.'/admin/'.$item.'");?'.'>');
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/local/modules/'.self::MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					CopyDirFiles($p.'/'.$item, $_SERVER['DOCUMENT_ROOT'].'/bitrix/components/'.$item, $ReWrite = True, $Recursive = True);
				}
				closedir($dir);
			}
		}		
		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx("/bitrix/js/blackbutterfly/".self::MODULE_ID);
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item);
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || !is_dir($p0 = $p.'/'.$item))
						continue;

					$dir0 = opendir($p0);
					while (false !== $item0 = readdir($dir0))
					{
						if ($item0 == '..' || $item0 == '.')
							continue;
						DeleteDirFilesEx('/bitrix/components/'.$item.'/'.$item0);
					}
					closedir($dir0);
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/local/modules/'.self::MODULE_ID.'/admin'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.')
						continue;
					unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.self::MODULE_ID.'_'.$item);
				}
				closedir($dir);
			}
		}
		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/local/modules/'.self::MODULE_ID.'/install/components'))
		{
			if ($dir = opendir($p))
			{
				while (false !== $item = readdir($dir))
				{
					if ($item == '..' || $item == '.' || !is_dir($p0 = $p.'/'.$item))
						continue;

					$dir0 = opendir($p0);
					while (false !== $item0 = readdir($dir0))
					{
						if ($item0 == '..' || $item0 == '.')
							continue;
						DeleteDirFilesEx('/bitrix/components/'.$item.'/'.$item0);
					}
					closedir($dir0);
				}
				closedir($dir);
			}
		}		
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;
		$this->InstallDB();
		$this->InstallEvents();
		$this->InstallFiles();
		RegisterModule(self::MODULE_ID);
	}

	function DoUninstall()
	{
		global $APPLICATION;
		UnRegisterModule(self::MODULE_ID);
		$this->UnInstallDB();
		$this->UnInstallEvents();
		$this->UnInstallFiles();
	}	
	function GetModuleRightList()
    {
        $arr = array(
            "reference_id" => array("D","R","W"),
            "reference" => array(
                GetMessage(self::MODULE_ID."_DENIED"),
                GetMessage(self::MODULE_ID."_OPENED"),
                GetMessage(self::MODULE_ID."_FULL")
            ));
        return $arr;
    }
}
?>