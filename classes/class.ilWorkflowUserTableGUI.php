<?php

/** @noinspection PhpIncludeInspection */
require_once './Services/Table/classes/class.ilTable2GUI.php';
/** @noinspection PhpIncludeInspection */
require_once './Services/Form/classes/class.ilTextInputGUI.php';
/** @noinspection PhpIncludeInspection */
require_once './Services/Form/classes/class.ilCheckboxInputGUI.php';
/** @noinspection PhpIncludeInspection */
require_once './Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php';
/** @noinspection PhpIncludeInspection */
require_once  './Services/WorkflowEngine/classes/class.ilObjWorkflowEngine.php';
/** @noinspection PhpIncludeInspection */
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/WorkflowClearance/classes/class.ilCategoryCourseAssignmentsRepositoryInputGUI.php';
/** @noinspection PhpIncludeInspection */
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/WorkflowClearance/classes/class.ilWorkflowClearanceFacade.php';

/**
 * Class ilWorkflowUserTableGUI
 */
class ilWorkflowUserTableGUI extends ilTable2GUI
{
	/** @var ilCtrl $ilCtrl */
	protected $ilCtrl;

	/** @var ilLanguage $lng */
	protected $lng;

	public function __construct($parent_obj, $parent_cmd, $template_context = '')
	{
		$this->setId('wfedef');
		parent::__construct($parent_obj, $parent_cmd, $template_context, $template_context);

		global $ilCtrl, $lng;
		$this->ilCtrl = $ilCtrl;
		$this->lng = $lng;

		$this->setRowTemplate('tpl.wfe_usr_row.html',
							  'Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/WorkflowClearance');

		$this->initColumns();
		$this->setEnableHeader(true);

		$this->setFormAction($this->ilCtrl->getFormAction($parent_obj));

		$this->initFilter();

		$this->getProcessesForDisplay();

		$this->setTitle($this->lng->txt("ui_uihk_wfeclearance_clearance"));
	}

	/**
	 * @return void
	 */
	public function getProcessesForDisplay()
	{
		/** @var ilTree $tree */
		global $tree;
		$path = $tree->getNodePath($_GET['ref_id']);
		$nodes = array();
		foreach($path as $element)
		{
			$nodes[] = $element['child'];
		}

		$entries = array();

		if(is_dir(ilObjWorkflowEngine::getRepositoryDir().'/'))
		{
			$entries = scandir(ilObjWorkflowEngine::getRepositoryDir().'/');
		}

		$base_list = array();
		foreach($entries as $entry)
		{
			if( $entry == '.' || $entry == '..' )
			{
				continue;
			}

			if(substr($entry, strlen($entry)-6) == '.bpmn2')
			{
				$file_entry = array();
				$file_entry['file'] = $entry;
				$file_entry['id'] = substr($entry, 0, strlen($entry)-6);
				$parts = explode('_', substr($entry, 6, strlen($entry)-12));

				$file_entry['status'] = 'OK';
				if(!file_exists(ilObjWorkflowEngine::getRepositoryDir() . '/' . $file_entry['id']. '.php'))
				{
					$file_entry['status'] = $this->lng->txt('missing_parsed_class');
				}

				$file_entry['version'] = substr(array_pop($parts),1);
				$file_entry['title'] = implode(' ', $parts);

				if(!$this->isFiltered($file_entry))
				{
					$base_list[] = $file_entry;
				}
			}
		}

		$this->setDefaultOrderField("nr");
		$this->setDefaultOrderDirection("asc");
		$this->setData($base_list);
	}

	/**
	 * @param array $row
	 *
	 * @return bool
	 */
	public function isFiltered($row)
	{
		if(!ilWorkflowClearanceFacade::isWorkflowFileClearedForRef($row['file'], (int)$_GET['ref_id']))
		{
			return true;
		}
		// Title filter
		$title_filter = $this->getFilterItemByPostVar('title');
		if($title_filter->getValue() != null)
		{
			if(strpos(strtolower($row['title']),strtolower($title_filter->getValue())) === false)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * @param array $set
	 */
	protected function fillRow($set)
	{

		$this->tpl->setVariable('VAL_TITLE', $set['title']);

		$selected_columns = $this->getSelectedColumns();

		if(in_array('file', $selected_columns))
		{
			$this->tpl->setVariable('VAL_FILE', $set['file']);
		}

		if(in_array('version', $selected_columns))
		{
			$this->tpl->setVariable('VAL_VERSION', $set['version']);
		}

		$button = ilLinkButton::getInstance();
		$button->setCaption('ui_uihk_wfeclearance_startlistening');
		$this->ilCtrl->setParameterByClass(get_class($this->parent_obj),'wfid', $set['file']);
		$button->setUrl(
			$this->ilCtrl->getLinkTarget(
				$this->parent_obj,
				'wfecuser.table.formshow'
			)
		);

		$this->tpl->setVariable('HTML_ASL', $button->getToolbarHTML());
	}

	/**
	 * @return void
	 */
	public function initFilter()
	{
		$title_filter_input = new ilTextInputGUI($this->lng->txt("title"), "title");
		$title_filter_input->setMaxLength(64);
		$title_filter_input->setSize(20);
		$this->addFilterItem($title_filter_input);
		$title_filter_input->readFromSession();
		$this->filter["title"] = $title_filter_input->getValue();
	}

	/**
	 * @return void
	 */
	public function initColumns()
	{
		$this->addColumn($this->lng->txt("title"), "title", "20%");

		$selected_columns = $this->getSelectedColumns();

		if(in_array('file', $selected_columns))
		{
			$this->addColumn($this->lng->txt("file"), "file", "30%");
		}

		if(in_array('version', $selected_columns))
		{
			$this->addColumn($this->lng->txt("version"), "version", "10%");
		}

		$this->addColumn($this->lng->txt("actions"), "", "10%");

	}

	/**
	 * @return array
	 */
	public function getSelectableColumns()
	{
		$cols["file"] = array(
			"txt" => $this->lng->txt("file"),
			"default" => true);
		$cols["version"] = array(
			"txt" => $this->lng->txt("version"),
			"default" => true);
		return $cols;
	}

}