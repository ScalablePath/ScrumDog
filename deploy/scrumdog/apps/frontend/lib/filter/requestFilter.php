<?
class requestFilter extends sfFilter
{
  public function execute ($filterChain)
  {
    // Code to execute before the action execution
	if ($this->isFirstCall())
	{
		$context = $this->getContext();
		
		//clear the projectRoleArray in the session
		//$context->getUser()->setAttribute('projectRoleArray', NULL);
		
		$currentRoute = $context->getRouting()->getCurrentRouteName();
		//var_dump($currentRoute); die();

		$taskRoutes = array('project_task', 'task_ajax_comment_save', 'task_history', 'task_file_list', 'task_subtasks', 'task_create_dialog_task', 'task_ajax_file_delete');
		if(in_array($currentRoute, $taskRoutes))
		{
			$request = $context->getRequest();
	
			$task_id = $request->getParameter('task_id');
			$task = Doctrine::getTable('SdTask')->find($task_id);
			if($task)
			{
				$project_id = $task->getProjectId();
				$request->setParameter('project_id', $project_id);
				$sprint_id = $task->getSprintId();
				$request->setParameter('sprint_id', $sprint_id);
			}
			else
			{
				$request->setParameter('nav_scope', 'main');
				$context->getController()->forward('error', 'index'); //this appears to cause infinite redirect
			}
		}
		
		$messageRoutes = array('project_message_view', 'message_ajax_comment_save', 'message_history', 'message_file_list', 'message_ajax_save', 'message_ajax_file_delete');
		if(in_array($currentRoute, $messageRoutes))
		{
			$request = $context->getRequest();
	
			$message_id = $request->getParameter('message_id');
			$message = Doctrine::getTable('SdMessage')->find($message_id);
			if($message)
			{
				$project_id = $message->getProjectId();
				$request->setParameter('project_id', $project_id);
				//$sprint_id = $task->getSprintId();
				//$request->setParameter('sprint_id', $sprint_id);
			}
			else
			{
				$request->setParameter('nav_scope', 'main');
				$context->getController()->forward('error', 'index'); //this appears to cause infinite redirect
			}
		}
	
		$sprintRoutes = array('sprint_createtask', 'sprint_dashboard', 'sprint_backlogbody', 'sprint_manage', 'sprint_burndown', 'sprint_burndown-data', 'task_create_dialog_sprint');
	
		if(in_array($currentRoute, $sprintRoutes))
		{
			$request = $context->getRequest();
	
			$sprint_id = $request->getParameter('sprint_id');
			$sprint = Doctrine::getTable('SdSprint')->find($sprint_id);
			if($sprint)
			{
				$project_id = $sprint->getProjectId();
				$request->setParameter('project_id', $project_id);
			}
			else
			{
				$request->setParameter('nav_scope', 'main');
				//$context->getController()->forward('error', 'index');
			}
		}
	
		if($currentRoute=='member_profile')
		{
			$request = $context->getRequest();
			$requestedUsername = $request->getParameter('username');
			$myUsername = $this->getContext()->getUser()->getUsername();
			if($requestedUsername!=$myUsername)
				$request->setParameter('nav_scope', 'main');
		}
	}

    // Execute next filter in the chain
    $filterChain->execute();
 
    // Code to execute after the action execution, before the rendering
  }
}
