<?
class projectComponents extends sfComponents
{
  public function executeAddMembers()
  {
    $this->project_id = $this->getRequestParameter('project_id');
    $addmembers = array('project_id' => $this->project_id);
    $addmembers['current_route'] = sfContext::getInstance()->getRouting()->getCurrentInternalUri(true);
    $this->form = new SdProjectAddMemberForm($addmembers);
  }

  public function executeBacklogBody(sfWebRequest $request)
  {
	if(is_null($this->project))
	{
		$this->project_id = $request->getParameter('project_id');
		$this->project = Doctrine::getTable('SdProject')->find($this->project_id);
	}

	//prepare the filters
	if(is_null($this->filters))
	{
		//this means we're getting the filters from an ajax request
		$this->filters = (array) json_decode($request->getParameter('filters'));

		//We should store filters in the session now
		$this->getUser()->setAttribute('backlogFilters-'.$this->project_id, $this->filters);
	}

	$this->filters['project_id'] = $this->project->getId();
	$this->filters['sprint_id'] = 'null';
	$this->filters['is_archived'] = 0;
	
	//prepare the sort
	if(is_null($this->sort))
	{
		//this means we're getting the sort from an ajax request
		$this->sort = (array) json_decode($request->getParameter('sort'));
		
		//We should store filters in the session now
		$this->getUser()->setAttribute('backlogSort-'.$this->project_id, $this->sort);
	}

	$this->tasks = SdTaskTable::getTasks($this->filters, $this->sort);
  }

  public function executeArchiveBody(sfWebRequest $request)
  {
    if(is_null($this->project))
	{
		$this->project_id = $request->getParameter('project_id');
		$this->project = Doctrine::getTable('SdProject')->find($this->project_id);
	}

	//prepare the filters
	if(is_null($this->filters))
	{
		//this means we're getting the filters from an ajax request
		$this->filters = (array) json_decode($request->getParameter('filters'));

		//We should store filters in the session now
		$this->getUser()->setAttribute('archiveFilters-'.$this->project_id, $this->filters);
	}

	$this->filters['project_id'] = $this->project->getId();
	$this->filters['is_archived'] = 1;
	
	//prepare the sort
	if(is_null($this->sort))
	{
		//this means we're getting the sort from an ajax request
		$this->sort = (array) json_decode($request->getParameter('sort'));
		
		//We should store filters in the session now
		$this->getUser()->setAttribute('archiveSort-'.$this->project->getId(), $this->sort);
	}

	$this->tasks = SdTaskTable::getTasks($this->filters, $this->sort);
  }

  public function executeSprintTableBody(sfWebRequest $request)
  {
	if(!isset($this->project))
	{
		$this->project = Doctrine::getTable('SdProject')->find($request->getParameter('project_id'));
	}
    $this->sprints = $this->project->getSprints(); 
  }
}