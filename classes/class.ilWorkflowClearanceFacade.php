<?php

class ilWorkflowClearanceFacade
{
	public static function isWorkflowFileClearedForRef($wf_file, $target_ref)
	{
		require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/WorkflowClearance/classes/class.ilWorkflowClearanceRepository.php';

		global $ilDB, $tree;

		$repository = new ilWorkflowClearanceRepository($ilDB);
		$instance = $repository->loadByWfFile($wf_file);

		if( $instance->getClearedRefs() == array() )
		{
			return false;
		}

		foreach($instance->getClearedRefs() as $clearedRef)
		{
			if(in_array($target_ref,$tree->getChildIds($clearedRef)))
			{
				return true;
			}
			if($target_ref == $clearedRef || $tree->isGrandChild($clearedRef, $target_ref))
			{
				return true;
			}
		}
		return false;
	}

}