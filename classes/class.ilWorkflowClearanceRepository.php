<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/WorkflowClearance/classes/class.ilWorkflowClearanceEntity.php';

class ilWorkflowClearanceRepository
{
	/** @var \ilDB $ilDB */
	protected $ilDB;

	/**
	 * ilWorkflowClearanceRepository constructor.
	 *
	 * @param \ilDB $ilDB
	 */
	public function __construct($ilDB)
	{
		$this->ilDB = $ilDB;
	}

	/**
	 * @param string $wf_file
	 *
	 * @return \ilWorkflowClearanceEntity
	 */
	public function loadByWfFile($wf_file)
	{
		$entity = new ilWorkflowClearanceEntity();
		$entity->setWfFile($wf_file);

		$result = $this->ilDB->queryF("SELECT ref_id FROM wfe_clearance WHERE wf_file = %s",
								array("text"),
								array($wf_file));

		while($row = $this->ilDB->fetchAssoc($result))
		{
			$entity->addClearedRef($row['ref_id']);
		}

		return $entity;
	}

	/**
	 * @param \ilWorkflowClearanceEntity $entity
	 */
	public function store(ilWorkflowClearanceEntity $entity)
	{
		$this->ilDB->manipulate("DELETE FROM wfe_clearance WHERE wf_file = "
								.$this->ilDB->quote($entity->getWfFile(), "text")
		);

		foreach($entity->getClearedRefs() as $ref_id)
		{
			$this->ilDB->insert(
				'wfe_clearance',
				array(
					'id' 		=> array('integer', $this->ilDB->nextId('wfe_clearance')),
					'wf_file' 	=> array('text', 	$entity->getWfFile()),
					'ref_id' 	=> array('integer', $ref_id)
				)
			);
		}
	}

	public function getEntityInstance($wf_file = '', $cleared_refs = array())
	{
		$entity = new ilWorkflowClearanceEntity();
		$entity->setWfFile($wf_file);
		$entity->setClearedRefs($cleared_refs);

		return $entity;
	}
}