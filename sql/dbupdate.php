<#1>
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
?>
<#2>
<?php

/** @var ilDB $ilDB */
if(!$ilDB->tableExists('wfe_clearance'))
{
	$fields = array (
		'id' => array(
			'type' => 'integer',
			'length' => 4,
			'notnull' => true,
			'default' => 0),
		'wf_file' => array(
			'type' => 'text',
			'length' => 255),
		'ref_id' => array(
			'type' => 'integer',
			'length' => 4),
	);
	$ilDB->createTable('wfe_clearance', $fields);
	$ilDB->addPrimaryKey('wfe_clearance', array('id'));
	$ilDB->createSequence('wfe_clearance');
}

?>