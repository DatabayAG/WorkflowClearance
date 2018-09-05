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
require_once './Services/Component/classes/class.ilPluginConfigGUI.php';
require_once './Services/Form/classes/class.ilPropertyFormGUI.php';
require_once './Services/Form/classes/class.ilNonEditableValueGUI.php';
// @codingStandardsIgnoreEnd

/**
 * WorkflowClearance Plugin Config GUI
 */
class ilWorkflowClearanceConfigGUI extends ilPluginConfigGUI
{
	/** @var ilLanguage $lng */
	public $lng;

	/** @var ilTemplate $tpl */
	public $tpl;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		global $tpl, $lng;
		$this->tpl = $tpl;
		$this->lng = $lng;
	}

	/**
	 * @param string $cmd Command
	 */
	public function performCommand($cmd)
	{
		// TODO: Implement performCommand() method.
	}
}
