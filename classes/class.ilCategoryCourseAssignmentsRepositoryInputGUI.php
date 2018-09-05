<?php
/**
 *
 * PHP version 5.3
 *
 * @category CategoryCourseAssignments
 * @package BusinessAddOnSuite
 * @author Maximilian Becker <mbecker@databay.de>
 * @copyright 2015-2016 Maximilian Becker / Databay AG
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version SVN: $Id$
 * @link http://www.databay.de Databay AG
 */

// @codingStandardsIgnoreStart
require_once './Services/UIComponent/Explorer2/classes/class.ilExplorerSelectInputGUI.php';
require_once './Services/Repository/classes/class.ilRepositoryExplorerGUI.php';
// @codingStandardsIgnoreEnd

/**
 * Select repository nodes input GUI
 * @ilCtrl_IsCalledBy ilCategoryCourseAssignmentsRepositoryInputGUI: ilObjPluginDispatchGUI
 */
class ilCategoryCourseAssignmentsRepositoryInputGUI extends ilExplorerSelectInputGUI
{
	/** @var \ilRepositoryExplorerGUI $explorer_gui */
	protected $explorer_gui;

	/**
	 * Constructor
	 * @param string $a_title   Title
	 * @param string $a_postvar Post Variable
	 */
	public function __construct($a_title = '', $a_postvar = '')
	{
		/** @var $ilCtrl ilCtrl */
		global $ilCtrl;

		$multi = true;

		$ilCtrl->setParameterByClass("ilobjplugindispatchgui", "postvar", $a_postvar);
		$this->explorer_gui = new ilRepositoryExplorerGUI(
			array('ilObjPluginDispatchGUI', 'ilCategoryCourseAssignmentsRepositoryInputGUI'),
			$this->getExplHandleCmd()
		);
		$this->explorer_gui->setSelectMode($a_postvar . '_sel', $multi);
		$this->explorer_gui->setTypeWhiteList(array("cat"));
		$this->explorer_gui->setSkipRootNode(true);

		parent::__construct($a_title, $a_postvar, $this->explorer_gui, $multi);
		$this->setType("repository_select");
	}

	/**
	 * @param int $a_id Node ID
	 *
	 * @return string
	 */
	public function getTitleForNodeId($a_id)
	{
		$retval = ilObject::_lookupTitle(ilObject::_lookupObjId($a_id));
		return $retval;
	}
}
