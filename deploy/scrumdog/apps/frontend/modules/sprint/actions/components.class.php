<?
class sprintComponents extends sfComponents
{

  public function executeBacklogBody(sfWebRequest $request)
  {
    if(is_null($this->project))
	{
		$this->project_id = $request->getParameter('project_id');
		$this->project = Doctrine::getTable('SdProject')->find($this->project_id);
	}

    if(is_null($this->sprint))
	{
		$this->sprint_id = $request->getParameter('sprint_id');
		$this->sprint = Doctrine::getTable('SdSprint')->find($this->sprint_id);
	}

    if(is_null($this->projectUserArray))
	{
		$this->projectUserArray = SdProjectTable::getProjectUserArray($this->project_id);
	}

	//prepare the filters
	if(is_null($this->filters))
	{
		//this means we're getting the filters from an ajax request
		$this->filters = (array) json_decode($request->getParameter('filters'));

		//We should store filters in the session now
		$this->getUser()->setAttribute('sprintFilters-'.$this->sprint->getId(), $this->filters);
	}

	$this->filters['sprint_id'] = $this->sprint->getId();
	$this->filters['is_archived'] = 0;
	
	//prepare the sort
	if(is_null($this->sort))
	{
		//this means we're getting the sort from an ajax request
		$this->sort = (array) json_decode($request->getParameter('sort'));
		
		//We should store filters in the session now
		$this->getUser()->setAttribute('sprintSort-'.$this->sprint->getId(), $this->sort);
	}

	$this->tasks = SdTaskTable::getTasks($this->filters, $this->sort);
  }
}