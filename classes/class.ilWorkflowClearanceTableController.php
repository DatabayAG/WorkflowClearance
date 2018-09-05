<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/WorkflowClearance/classes/class.ilIWorkflowClearanceController.php';

class ilWorkflowClearanceTableController implements ilIWorkflowClearanceController
{
	/** @var string $base_url */
	protected $base_url;

	/** @var  ilObject2GUI $routing_object */
	protected $routing_object;

	/** @var string $routing_cmd */
	protected $routing_cmd;

	/**
	 * @param string $base_url
	 *
	 * @return void
	 */
	public function setBaseURL($base_url)
	{
		$this->base_url = $base_url;
	}

	/**
	 * @param ilObject2GUI $routing_object
	 */
	public function setRoutingObject($routing_object)
	{
		$this->routing_object = $routing_object;
	}

	/**
	 * @param string $routing_cmd
	 */
	public function setRoutingCmd($routing_cmd)
	{
		$this->routing_cmd = $routing_cmd;
	}

	/**
	 * @param string $command
	 */
	public function execute($command)
	{
		switch($command)
		{

			case 'formshow':
				require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/WorkflowClearance/classes/class.ilWorkflowClearanceForm.php';
				$form = new ilWorkflowClearanceForm($_GET['wfid'], $this->routing_object, $this->routing_cmd);
				return $form->getHTML();

			case 'show':
			default:
				require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/WorkflowClearance/classes/class.ilWorkflowClearanceTableGUI.php';
				$table = new ilWorkflowClearanceTableGUI($this->routing_object, $this->routing_cmd);
				return $table->getHTML();
		}
	}


}