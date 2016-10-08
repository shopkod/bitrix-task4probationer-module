<?php
/**
 * Created by PhpStorm.
 * User: Ponomarev Yuriy
 * Date: 07.10.2016
 * Time: 9:03
 */

class CTask4Probationer {

	protected static $MODULE_ID = "namer.task4probationer";

	/**
	 * Создает задачу с крайним сроком за день до окончания испытательного срока сотрудника
	 *
	 * @return string
	 */
	function CreateTask4Probationer()
	{
		global $APPLICATION;

		if (CModule::IncludeModule("tasks"))
		{
			//get settings
			$created_by = COption::GetOptionInt(self::$MODULE_ID, "CREATED_BY");
			$responsible_id = COption::GetOptionInt(self::$MODULE_ID, "RESPONSIBLE_ID");
			$title = COption::GetOptionString(self::$MODULE_ID, "TITLE");

			$arAccomplices = $arAuditors = array();
			$strAccomplices = COption::GetOptionString(self::$MODULE_ID, 'ACCOMPLICES');
			if(!empty($strAccomplices))
				$arAccomplices = unserialize($strAccomplices);
			$strAuditors = COption::GetOptionString(self::$MODULE_ID, 'AUDITORS');
			if(!empty($strAuditors))
				$arAuditors = unserialize($strAuditors);

			if($created_by > 0 && $responsible_id > 0 && !empty($title))
			{
				//get users who comes to an end probation
				$arFilter = array(
					"ACTIVE" => 'Y',
					"UF_CHECK_DAY" => ConvertTimeStamp(AddToTimeStamp(array("DD" => +14)), "SHORT"),
				);

				$rsUsers = CUser::GetList(($by = "id"), ($order = "asc"), $arFilter);
				while ($arUser = $rsUsers->GetNext())
				{
					$arFields = Array(
						"TITLE" => $title,
						"CREATED_BY" => $created_by,
						//"RESPONSIBLE_ID" => $responsible_id, // ответственный из настроек модуля
						"RESPONSIBLE_ID" => $arUser["ID"], // ответственный из выборки
						"DEADLINE" => ConvertTimeStamp(AddToTimeStamp(array("DD" => +13)), "FULL"),
					);
					if(count($arAccomplices))
						$arFields["ACCOMPLICES"] = $arAccomplices;
					if(count($arAuditors))
						$arFields["AUDITORS"] = $arAuditors;

					$obTask = new CTasks;
					if(!$obTask->Add($arFields))
					{
						if($e = $APPLICATION->GetException())
							AddMessage2Log('CreateTask4Probationer error:\nMessage: '.$e->GetString().'\narFields= '.print_r($arFields, true), "CreateTask4Probationer ".__LINE__);
					}
				}
			}
			else
			{
				AddMessage2Log('Module settings not found!', "CreateTask4Probationer ".__LINE__);
			}
		}
		else
		{
			AddMessage2Log('Tasks module not install!', "CreateTask4Probationer ".__LINE__);
		}

		return "CTask4Probationer::CreateTask4Probationer();";
	}
}