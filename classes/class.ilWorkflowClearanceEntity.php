<?php

class ilWorkflowClearanceEntity
{
	/** @var string $wf_file */
	protected $wf_file;

	/** @var integer[] $cleared_refs */
	protected $cleared_refs;

	/**
	 * ilWorkflowClearanceEntity constructor.
	 */
	public function __construct()
	{
		$this->cleared_refs = array();
	}

	/**
	 * @return string
	 */
	public function getWfFile()
	{
		return $this->wf_file;
	}

	/**
	 * @param string $wf_file
	 */
	public function setWfFile($wf_file)
	{
		$this->wf_file = $wf_file;
	}

	/**
	 * @return integer[]
	 */
	public function getClearedRefs()
	{
		return $this->cleared_refs;
	}

	/**
	 * @param integer[] $cleared_refs
	 */
	public function setClearedRefs($cleared_refs)
	{
		$this->cleared_refs = $cleared_refs;
	}

	/**
	 * @param integer $cleared_ref
	 */
	public function addClearedRef($cleared_ref)
	{
		$this->cleared_refs[] = $cleared_ref;
	}

	/**
	 *
	 */
	public function flushClearedRefs()
	{
		$this->cleared_refs = array();
	}
}