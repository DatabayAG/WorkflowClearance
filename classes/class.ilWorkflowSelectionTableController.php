<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/WorkflowClearance/classes/class.ilIWorkflowClearanceController.php';

class ilWorkflowSelectionTableController implements ilIWorkflowClearanceController
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
				require_once './Services/WorkflowEngine/classes/class.ilObjWorkflowEngine.php';
				require_once './Services/WorkflowEngine/classes/administration/class.ilWorkflowLauncherGUI.php';
				$identifier = substr($_GET['wfid'],0,-6);

				require_once ilObjWorkflowEngine::getRepositoryDir() . $identifier . '.php';
				$class = substr($identifier,4);
				/** @var ilBaseWorkflow $workflow_instance */
				$workflow_instance = new $class;

				$workflow_instance->setWorkflowClass('wfd.'.$class.'.php');
				$workflow_instance->setWorkflowLocation(ilObjWorkflowEngine::getRepositoryDir());

				if(count($workflow_instance->getInputVars()))
				{
					$show_launcher_form = false;
					foreach ($workflow_instance->getInputVars() as $input_var)
					{
						if (!isset($_POST[ $input_var['name'] ]))
						{
							$show_launcher_form = true;
						}
						else
						{
							$workflow_instance->setInstanceVarById($input_var['name'], $_POST[ $input_var['name'] ]);
						}
					}

				$launcher = new ilWorkflowLauncherGUI('formsubmit', $identifier);
				$form = $launcher->getForm($workflow_instance->getInputVars());

				if ($show_launcher_form || $form->checkInput() == false)
				{
					$form->setValuesByPost();

					return $form->getHTML();
				}
			}
			require_once './Services/WorkflowEngine/classes/utils/class.ilWorkflowDbHelper.php';
			ilWorkflowDbHelper::writeWorkflow( $workflow_instance );

			$workflow_instance->startWorkflow();
			$workflow_instance->handleEvent(
				array(
					'time_passed',
					'time_passed',
					'none',
					0,
					'none',
					0
				)
			);

			ilWorkflowDbHelper::writeWorkflow( $workflow_instance );
			global $lng;
			ilUtil::sendSuccess($lng->txt('process_started'));

			case 'show':
			default:
				require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/WorkflowClearance/classes/class.ilWorkflowUserTableGUI.php';
				$table = new ilWorkflowUserTableGUI($this->routing_object, $this->routing_cmd);
				return $table->getHTML();
		}
	}


}