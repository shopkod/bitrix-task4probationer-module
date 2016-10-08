<?
if(!$USER->IsAdmin()) return;

$MODULE_ID = "namer.task4probationer";
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

$error = '';
if (($REQUEST_METHOD == "POST") && (strlen($Update.$RestoreDefaults) > 0) && check_bitrix_sessid()
)
{
	if (strlen($RestoreDefaults) > 0)
	{
		COption::RemoveOption($MODULE_ID);
	}
	else
	{
		if ($_POST['CREATED_BY'] > 0 && $_POST['RESPONSIBLE_ID'] > 0 && strlen(trim($_POST['TITLE'])) > 0
		)
		{
			COption::SetOptionInt($MODULE_ID, 'CREATED_BY', intval($_POST['CREATED_BY']));
			COption::SetOptionInt($MODULE_ID, 'RESPONSIBLE_ID', intval($_POST['RESPONSIBLE_ID']));
			COption::SetOptionString($MODULE_ID, 'TITLE', trim($_POST['TITLE']));

			if(isset($_POST['ACCOMPLICES']) && count($_POST['ACCOMPLICES']))
				COption::SetOptionString($MODULE_ID, 'ACCOMPLICES', serialize($_POST['ACCOMPLICES']));

			if(isset($_POST['AUDITORS']) && count($_POST['AUDITORS']))
				COption::SetOptionString($MODULE_ID, 'AUDITORS', serialize($_POST['AUDITORS']));

			if(isset($_POST['ACCOMPLICES_STRING']) && strlen(trim($_POST['ACCOMPLICES_STRING'])) > 0)
				COption::SetOptionString($MODULE_ID, 'ACCOMPLICES_STRING', preg_replace("|]\s+|", "];", trim($_POST['ACCOMPLICES_STRING'])));

			if(isset($_POST['AUDITORS_STRING']) && strlen(trim($_POST['AUDITORS_STRING'])) > 0)
				COption::SetOptionString($MODULE_ID, 'AUDITORS_STRING', preg_replace("|]\s+|", "];", trim($_POST['AUDITORS_STRING'])));

			// Создаем агент для ежедневной проверки и создания задач сотрудникам на испытательном сроке
			CAgent::RemoveModuleAgents($MODULE_ID);
			CAgent::AddAgent("CTask4Probationer::CreateTask4Probationer();", $MODULE_ID, "Y", 86400);

			LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($MODULE_ID)."&lang=".urlencode(LANGUAGE_ID));
		}
		else
			$error .= GetMessage("NAMER_MODULE_TASK4PROBATIONER_OPTIONS_VALIDATE_ERROR");
	}
}

if (!empty($error))
	CAdminMessage::ShowMessage($error);

echo BeginNote();
echo GetMessage("NAMER_MODULE_TASK4PROBATIONER_DESCRIPTION");
echo EndNote();

$aTabs = array();
$aTabs[] = array(
	"DIV" => "edit1",
	"TAB" => GetMessage("NAMER_MODULE_TASK4PROBATIONER_OPTIONS_TAB_1"),
	"ICON" => "settings",
	"TITLE" => GetMessage("NAMER_MODULE_TASK4PROBATIONER_OPTIONS_TAB_1_TITLE")
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();?>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?echo LANGUAGE_ID?>" name="f_task_settings">
	<?$tabControl->BeginNextTab();?>
		<tr class="adm-detail-required-field">
			<td valign="top" class="field-name">
				<label for="CREATED_BY"><?=GetMessage('NAMER_MODULE_TASK4PROBATIONER_OPTIONS_CREATED_BY');?>:</label>
			</td>
			<td>
				<?echo FindUserID("CREATED_BY", COption::GetOptionInt($MODULE_ID, "CREATED_BY"), "", "f_task_settings", 4)?>
			</td>
		</tr>
		<tr class="adm-detail-required-field">
			<td valign="top" class="field-name">
				<label for="RESPONSIBLE_ID"><?=GetMessage('NAMER_MODULE_TASK4PROBATIONER_OPTIONS_RESPONSIBLE_ID');?>:</label>
			</td>
			<td>
				<?echo FindUserID("RESPONSIBLE_ID", COption::GetOptionInt($MODULE_ID, "RESPONSIBLE_ID"), "", "f_task_settings", 4)?>
			</td>
		</tr>
		<tr class="adm-detail-required-field">
			<td valign="top" class="field-name">
				<label for="TITLE"><?=GetMessage('NAMER_MODULE_TASK4PROBATIONER_OPTIONS_TITLE');?>:</label>
			</td>
			<td>
				<input type="text" id="TITLE" name="TITLE" size="40" value="<?=COption::GetOptionString($MODULE_ID, "TITLE")?>">
			</td>
		</tr>
		<tr>
			<td valign="top" class="field-name">
				<label for="TITLE"><?=GetMessage('NAMER_MODULE_TASK4PROBATIONER_OPTIONS_ACCOMPLICES');?>:</label>
			</td>
			<td>
				<?$GLOBALS["APPLICATION"]->IncludeComponent(
					'bitrix:intranet.user.selector',
					'',
					array(
						'INPUT_NAME' => "ACCOMPLICES",
						'INPUT_NAME_STRING' => "ACCOMPLICES_STRING",
						'INPUT_NAME_SUSPICIOUS' => "ACCOMPLICES_SUSPICIOUS",
						'INPUT_VALUE_STRING' => COption::GetOptionString($MODULE_ID, "ACCOMPLICES_STRING"),
						//'EXTERNAL' => 'A',
						'MULTIPLE' => 'Y',
					),
					false,
					array("HIDE_ICONS" => "Y")
				); ?>
			</td>
		</tr>
		<tr>
			<td valign="top" class="field-name">
				<label for="TITLE"><?=GetMessage('NAMER_MODULE_TASK4PROBATIONER_OPTIONS_AUDITORS');?>:</label>
			</td>
			<td>
				<?$GLOBALS["APPLICATION"]->IncludeComponent(
					'bitrix:intranet.user.selector',
					'',
					array(
						'INPUT_NAME' => "AUDITORS",
						'INPUT_NAME_STRING' => "AUDITORS_STRING",
						'INPUT_NAME_SUSPICIOUS' => "AUDITORS_SUSPICIOUS",
						'INPUT_VALUE_STRING' => COption::GetOptionString($MODULE_ID, "AUDITORS_STRING"),
						//'EXTERNAL' => 'A',
						'MULTIPLE' => 'Y',
					),
					false,
					array("HIDE_ICONS" => "Y")
				); ?>
			</td>
		</tr>

	<?$tabControl->Buttons();?>
	<input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>" class="adm-btn-save">
	<?if(strlen($_REQUEST["back_url_settings"])>0):?>
		<input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
		<input type="hidden" name="back_url_settings" value="<?=htmlspecialcharsbx($_REQUEST["back_url_settings"])?>">
	<?endif?>
	<input type="submit" name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="return confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
	<?=bitrix_sessid_post();?>
	<?$tabControl->End();?>
</form>