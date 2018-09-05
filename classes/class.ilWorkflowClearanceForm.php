<?php

require_once './Services/Form/classes/class.ilPropertyFormGUI.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/WorkflowClearance/classes/class.ilCategoryCourseAssignmentsRepositoryInputGUI.php';

class ilWorkflowClearanceForm
{
	protected $wf_file;

	/** @var \ilLanguage $lng */
	protected $lng;

	/** @var ilCtrl $ilCtrl */
	protected $ilCtrl;

	protected $routing_object;
	protected $routing_cmd;

	/**
	 * ilWorkflowClearanceForm constructor.
	 *
	 * @param $wf_file
	 * @param $routing_object
	 * @param $routing_cmd
	 */
	public function __construct($wf_file, $routing_object, $routing_cmd)
	{
		global $lng, $ilCtrl;

		$this->wf_file 			= $wf_file;
		$this->routing_object 	= $routing_object;
		$this->routing_cmd 		= $routing_cmd;

		$this->lng				= $lng;
		$this->ilCtrl			= $ilCtrl;

	}

	/**
	 * @return string
	 */
	public function getHTML()
	{
		$form = $this->getForm();
		$form = $this->populateForm($form);
		return $form->getHTML();
	}

	/**
	 * @return \ilPropertyFormGUI
	 */
	public function getForm()
	{
		$form = new ilPropertyFormGUI();

		$form->setFormAction( $this->ilCtrl->getFormAction( $this->routing_object ) );
		$form->setTitle( $this->lng->txt( 'ui_uihk_wfeclearance_clearance' ) );
		$form->addCommandButton('wfecadmin.cformsave', $this->lng->txt('save'));

		$wf_file = new ilHiddenInputGUI('wf_id');
		$wf_file->setValue($this->wf_file);
		$form->addItem($wf_file);

		$catsel = new ilCategoryCourseAssignmentsRepositoryInputGUI(
			$this->lng->txt('ui_uihk_wfeclearance_clearance_loc'),
			'repository'
		);
		$form->addItem($catsel);

		return $form;
	}

	public function populateForm(ilPropertyFormGUI $form)
	{
		global $ilDB;
		require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/WorkflowClearance/classes/class.ilWorkflowClearanceRepository.php';
		$repository = new ilWorkflowClearanceRepository($ilDB);
		$entity = $repository->loadByWfFile($this->wf_file);
		$element = $form->getItemByPostVar('repository');
		$element->setValue($entity->getClearedRefs());
		return $form;
	}

	public function handleSaveRequest()
	{
		$form = $this->getForm();
		$form->setValuesByPost();
		if(!$form->checkInput())
		{
			exit("Form Error - Handle me!");
		}

		global $ilDB;
		require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/WorkflowClearance/classes/class.ilWorkflowClearanceRepository.php';
		$repository = new ilWorkflowClearanceRepository($ilDB);
		$entity = $repository->loadByWfFile($this->wf_file);
		$entity->flushClearedRefs();
		foreach($_POST['repository_sel'] as $ref_id)
		{
			$entity->addClearedRef( (int)$ref_id );
		}
		$repository->store($entity);
	}
}