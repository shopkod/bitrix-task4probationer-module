<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class namer_task4probationer extends CModule
{
	var $MODULE_ID = "namer.task4probationer";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	function namer_task4probationer()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}

		$this->MODULE_NAME = GetMessage("NAMER_MODULE_TASK4PROBATIONER_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("NAMER_MODULE_TASK4PROBATIONER_INSTALL_DESCRIPTION");
	}

	function InstallDB()
	{
		RegisterModule($this->MODULE_ID);
		return true;
	}

	function UnInstallDB()
	{
		CAgent::RemoveModuleAgents($this->MODULE_ID);
		COption::RemoveOption($this->MODULE_ID);
		UnRegisterModule($this->MODULE_ID);
		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles()
	{
		return true;
	}

	function UnInstallFiles()
	{
		return true;
	}

	function DoInstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;

		if (!IsModuleInstalled($this->MODULE_ID))
		{
			$this->InstallDB();
			$this->InstallEvents();
			$this->InstallFiles();
			$APPLICATION->IncludeAdminFile(GetMessage("NAMER_MODULE_TASK4PROBATIONER_INSTALL_TITLE"), dirname(__FILE__)."/step.php");
		}
	}

	function DoUninstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;

		$this->UnInstallDB();
		$this->UnInstallEvents();
		$this->UnInstallFiles();
		$APPLICATION->IncludeAdminFile(GetMessage("NAMER_MODULE_TASK4PROBATIONER_UNINSTALL_TITLE"), dirname(__FILE__)."/unstep.php");
	}
}?>