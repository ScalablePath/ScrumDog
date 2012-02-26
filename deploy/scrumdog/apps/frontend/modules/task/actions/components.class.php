<?php
class taskComponents extends sfComponents
{
  public function executeComment($request)
  {
		$this->taskId = $this->getRequestParameter('task_id');
		$this->comments = SdTaskCommentTable::getTaskComments($this->taskId);
  }
  
  public function executeHistory()
  {
		$taskId = $this->getRequestParameter('task_id');
			
		$query = Doctrine_Query::create()
							->select('u.full_name, th.created_at, th.change_type, th.previous_value, th.new_value')
							->from('SdTaskHistory th')
							->innerJoin('th.User u')
							->where('th.task_id ='.$taskId)
							->orderby('th.created_at DESC');
		
		$this->histories = $query->execute();
		$this->historyTotal = count($this->histories->getData());
  }

  public function executeFileList($request)
  {
  		if(!isset($this->files))
		{
	  		if($this->mode=='task')
	  		{
				$id = isset($this->id) ? $this->id : $this->getRequestParameter('task_id');
				$this->files = SdTaskFileTable::getTaskFiles($id);
			}
			elseif($this->mode=='message' || $this->mode=='message_index')
			{
				$id = isset($this->id) ? $this->id : $this->getRequestParameter('message_id');
				$this->files = SdMessageFileTable::getMessageFiles($id);
			}
		}
  }
 
  public function executeCreateDialog($request)
  {
  	$this->project_id = $request->getParameter('project_id');
    $this->sprint_id = $request->getParameter('sprint_id');
    $this->task_id = $request->getParameter('task_id');
    $this->dialogmode = $request->getParameter('dialogmode');

    $this->projectUserArray = SdProjectTable::getProjectUserArray($this->project_id);
	
	if(is_null($this->parentTasks))
	{	
		$filters = array();
		$filters['project_id'] = $this->project_id;
		$filters['status'] = 'not-completed';
		
		if($this->dialogmode=='task')
		{
			$this->task = Doctrine::getTable('SdTask')->find($this->task_id);
			$filters['is_archived'] = $this->task->getIsArchived();
			$this->selectedTaskId = $this->task->getId();
		}
		else
		{
			$filters['is_archived'] = 0;
		}
		
		if(!is_null($this->sprint_id))
			$filters['sprint_id'] = $this->sprint_id;
		else
			$filters['sprint_id'] = 'null';
			
		$sort = array('task_id' => 'ASC');
		
		$this->parentTasks = SdTaskTable::getTasks($filters, $sort);
	}
  }
  
  public function executeSubtaskBody(sfWebRequest $request)
  {
  	$this->project_id = $request->getParameter('project_id');
  	if(is_null($this->project))
	{
		$this->project = Doctrine::getTable('SdProject')->find($this->project_id);
	}
	
	$this->task_id = $request->getParameter('task_id');
    if(is_null($this->task))
	{
		$this->task = Doctrine::getTable('SdTask')->find($this->task_id);
	}

    if(is_null($this->projectUserArray))
	{
		$this->projectUserArray = SdProjectTable::getProjectUserArray($this->project_id);
	}
	
	if(is_null($this->subTasks))
	{
		//prepare the filters
		$filters = array('parent_id' => $this->task->getId());
		//$filters['is_archived'] = 0;
		
		//prepare the sort
		$sort = array('task_id' => 'ASC');
	
		$this->subTasks = SdTaskTable::getTasks($filters, $sort);
	}
  }
}