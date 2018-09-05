<?php
/**
 *
 * PHP version 5.3
 *
 * @category Plugin
 * @package WorkflowClearance
 * @author Maximilian Becker <mbecker@databay.de>
 * @copyright 2015-2016 Maximilian Becker / Databay AG
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version SVN: $Id$
 * @link http://www.databay.de Databay AG
 */

// @codingStandardsIgnoreStart
require_once './Services/Object/classes/class.ilObject2GUI.php';
// @codingStandardsIgnoreEnd

/**
 * Class ilWorkflowClearanceAdminGUI
 *
 * @ilCtrl_isCalledBy ilWorkflowClearanceAdminGUI: ilObjPluginDispatchGUI
 */
class ilWorkflowClearanceAdminGUI extends ilObject2GUI
{
	/**
	 * @return null
	 */
	public function getType()
	{
		return null;
	}

	function executeCommand()
	{
		$next_class = $this->ctrl->getNextClass($this);
		$cmd = $this->ctrl->getCmd();

		$cmd_parts = explode('.', $cmd);
		if($cmd_parts[0] != 'wfecadmin' && $cmd_parts[0] != 'wfecuser' )
		{
			throw new Exception('Illegal request to ' . $cmd_parts[0] . ' routed to ilWorkflowClearanceAdmin');
		}

		global $lng;
		$lng->loadLanguageModule('wfe');

		$content = '';
		if($cmd_parts[0] == 'wfecadmin')
		{
			switch($cmd_parts[1])
			{
				case 'cformsave':
					require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/WorkflowClearance/classes/class.ilWorkflowClearanceForm.php';
					$form = new ilWorkflowClearanceForm(stripslashes($_POST['wf_id']),$this, $cmd_parts[0]);
					$form->handleSaveRequest();
					ilUtil::sendSuccess($this->lng->txt('saved'));
				case 'cformcancel':
				case 'table':
					require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/WorkflowClearance/classes/class.ilWorkflowClearanceTableController.php';
					$table_controller = new ilWorkflowClearanceTableController('admin');
					$table_controller->setRoutingObject($this);
					$table_controller->setBaseURL($this->ctrl->getLinkTarget($this));
					$content = $table_controller->execute($cmd_parts[2]);
					break;

				default:
			}
		} else {

			switch($cmd_parts[1])
			{
				case 'table':
					require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/WorkflowClearance/classes/class.ilWorkflowSelectionTableController.php';
					$table_controller = new ilWorkflowSelectionTableController('admin');
					$table_controller->setRoutingObject($this);
					$table_controller->setBaseURL($this->ctrl->getLinkTarget($this));
					$content = $table_controller->execute($cmd_parts[2]);
					break;
			}
		}


		/** @var ilTabsGUI $ilTabs*/
		global $ilTabs;
		$ilTabs->setBackTarget($lng->txt('back'), $this->ctrl->getLinkTargetByClass(
			array('iladministrationgui','ilobjworkflowenginegui')) . '&ref_id=' . (int)$_GET['ref_id']
		);

		/** @var ilTemplate $tpl */
		global $tpl;
		$tpl->getStandardTemplate();
		$this->setTitleAndDescription();
		$tpl->setContent($content);
		$tpl->show();
	}
}