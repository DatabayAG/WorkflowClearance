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
require_once './Services/UIComponent/classes/class.ilUIHookPluginGUI.php';
// @codingStandardsIgnoreEnd

/**
 * WorkflowClearance User Interface Hook Class
 *
 * @author Maximilian Becker <mbecker@databay.de>
 * @version $Id$
 * @ingroup ServicesUIComponent
 */
class ilWorkflowClearanceUIHookGUI extends ilUIHookPluginGUI
{
	/** @var array */
	protected $whitelist = array('crs');

	/** @var int */
	protected $ref_id;

	/** @var int */
	protected $obj_id;

	/** @var int */
	protected $folder_ref_id;

	/** @var string */
	protected $type;

	/** @var ilTemplate $template */
	protected $template;

	/**
	 * Modify HTML output of GUI elements. Modifications modes are:
	 * - ilUIHookPluginGUI::KEEP (No modification)
	 * - ilUIHookPluginGUI::REPLACE (Replace default HTML with your HTML)
	 * - ilUIHookPluginGUI::APPEND (Append your HTML to the default HTML)
	 * - ilUIHookPluginGUI::PREPEND (Prepend your HTML to the default HTML)
	 *
	 * @param string $a_comp component
	 * @param string $a_part string that identifies the part of the UI that is handled
	 * @param string $a_par array of parameters (depend on $a_comp and $a_part)
	 *
	 * @return array array with entries "mode" => modification mode, "html" => your html
	 */
	public function getHTML($a_comp, $a_part, $a_par = array())
	{
		/**
		 * @var $ilTabs ilTabsGUI
		 * @var $ilObjDataCache ilObjectDataCache
		 * @var $ilAccess ilAccessHandler
		 * @var $ilCtrl ilCtrl
		 * @var $ilToolbar ilToolbarGUI
		 * @var $ilSetting ilSetting
		 */
		global $ilTabs, $tpl, $ilObjDataCache, $ilAccess, $tree;
		if(
			!isset($_GET['isClearancePluginCmd']) ||
			!(int)$_GET['isClearancePluginCmd'] ||
			!isset($_GET['ref_id']) ||
			!(int)$_GET['ref_id'] ||
			!in_array($a_part, array('right_column', 'left_column', 'center_column')) ||
			!$ilAccess->checkAccess('read', '', $_GET['ref_id']) ||
			!$ilObjDataCache->lookupType($ilObjDataCache->lookupObjId($_GET['ref_id'])) == 'cat'
		)
		{
			return array("mode" => ilUIHookPluginGUI::KEEP, "html" => "");
		}

		/** @var ilLanguage $lng */
		global $lng;

		// add things to the personal desktop overview
		if($a_comp == "Services/PersonalDesktop" && $a_part == "center_column")
		{
			$this->ref_id = (int)$_GET['ref_id'];

			$tpl->setTitle($ilObjDataCache->lookupTitle($ilObjDataCache->lookupObjId($this->ref_id)));
			$tpl->setDescription($ilObjDataCache->lookupDescription($ilObjDataCache->lookupObjId($this->ref_id)));

			$ilTabs->setBackTarget($lng->txt('back'), 'goto.php?target=crs_' . $this->ref_id);

			$this->template = new ilTemplate("tpl.clearancelauncher_form.html", true, true, "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/WorkflowClearance");
			$this->template->setVariable('FORM_ACTION', 'ilias.php?baseClass=ilPersonalDesktopGUI&amp;isPrintPluginCmd=1&amp;printPluginCmd=createExport&amp;ref_id=' . (int)$_GET['ref_id']);

			$this->template->setVariable('CANCEL_URL', 'goto.php?target=crs_' . $this->ref_id);
			$this->template->setVariable('CANCEL', $lng->txt('cancel'));
			$this->template->setVariable('PDF_EXPORT', $lng->txt('launch'));

			return array('mode' => ilUIHookPluginGUI::REPLACE, 'html' => $this->template->get());
		}
		return array("mode" => ilUIHookPluginGUI::KEEP, "html" => "");
	}

	/**
	 * Modify GUI objects, before they generate ouput
	 *
	 * @param string $a_comp component
	 * @param string $a_part string that identifies the part of the UI that is handled
	 * @param array  $a_par array of parameters (depend on $a_comp and $a_part)
	 */
	public function modifyGUI($a_comp, $a_part, $a_par = array())
	{
		/** @var ilCtrl $ilCtrl */
		global $ilCtrl;
		if($a_part == 'tabs' && $ilCtrl->getCmdClass() == 'ilobjworkflowenginegui')
		{
			global $lng;

			$link_target = $ilCtrl->getLinkTargetByClass(
					array('ilobjplugindispatchgui', 'ilworkflowclearanceadmingui'),
					'wfecadmin.table.show'
				) . '&ref_id=' . (int)$_GET['ref_id'];

			/** @var ilTabsGUI $tabs */
			$tabs = $a_par['tabs'];
			$tabs->addTab('clearance', $lng->txt('ui_uihk_wfeclearance_clearance'), $link_target);
		}

		if($a_part == 'tabs' && $ilCtrl->getCmdClass() == 'ilobjcoursegui')
		{
			global $lng;

			$link_target = $ilCtrl->getLinkTargetByClass(
					array('ilobjplugindispatchgui', 'ilworkflowclearanceadmingui'),
					'wfecuser.table.show'
				) . '&ref_id=' . (int)$_GET['ref_id'];

			/** @var ilTabsGUI $tabs */
			$tabs = $a_par['tabs'];
			$tabs->addTab('wfselect', $lng->txt('ui_uihk_wfeclearance_workflows'), $link_target);
		}

	}

	/**
	 * @return string
	 */
	protected function getMenuHref()
	{
		return 'ilias.php?baseClass=ilPersonalDesktopGUI&amp;isClearancePluginCmd=1&amp;clearancePluginCmd=selector&amp;ref_id=' . (int)$_GET['ref_id'];
	}

	/**
	 * @return bool|string
	 */
	public function isThisTheCorrectLocationForTabDisplay()
	{
		if(strtolower($_GET['baseClass']) != 'iladministrationgui')
		{
			if(in_array($this->getTypeByRefId(), $this->whitelist))
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * @return string
	 */
	public function getTypeByRefId()
	{
		/** @var $ilObjDataCache ilObjectDataCache */
		global $ilObjDataCache;

		if(is_array($_GET) && array_key_exists('ref_id', $_GET))
		{
			$this->ref_id 	= (int)$_GET['ref_id'];
			$this->obj_id 	= $ilObjDataCache->lookupObjId($this->ref_id);
			$this->type   	= $ilObjDataCache->lookupType($this->obj_id);
			return $this->type;
		}
		return '';
	}

}