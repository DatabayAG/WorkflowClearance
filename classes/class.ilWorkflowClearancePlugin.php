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
require_once './Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php';
// @codingStandardsIgnoreEnd

/**
 * WorkflowClearance Plugin
 *
 * @author Maximilian Becker <mbecker@databay.de>
 */
class ilWorkflowClearancePlugin extends ilUserInterfaceHookPlugin
{
	/**
	 * @return string
	 */
	public function getPluginName()
	{
		return 'WorkflowClearance';
	}
}