<?php

interface ilIWorkflowClearanceController
{
	/**
	 *
	 * @param string $base_url
	 *
	 * @return void
	 */
	public function setBaseURL($base_url);

	/**
	 * @param string $command
	 *
	 * @return mixed
	 */
	public function execute($command);
}