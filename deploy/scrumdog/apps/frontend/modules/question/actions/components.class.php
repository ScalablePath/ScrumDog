<?php
class questionComponents extends sfComponents
{
	public function executeCalendar(sfWebRequest $request)
	{
		if(is_null($this->project_id))
		{
			$this->project_id = $request->getParameter('project_id');
		}
		if(is_null($this->questionUser))
		{
			$this->userName = $request->getParameter('username');
			$this->questionUser = SdUserTable::retrieveByUsername($this->userName);
		}
		if(is_null($this->dateString))
		{
			$this->dateString = $request->getParameter('date');
		}
	
		//figure out what month we should be showing
		if(is_null($this->dateString))
		{
			//ajax request
			$monthString = $request->getParameter('month');
			$this->selected_day = $request->getParameter('selected_day');
		}
		else
		{
			//initial question page load
			$monthString = $this->dateString;
			$this->selected_day = $this->dateString;
		}

		//Start doing the interesting stuff
		$this->calendar = new QuestionCalendar($this->questionUser, $monthString, $this->selected_day);
		
	}

	public function executeSearchTableBody(sfWebRequest $request)
	{
		if(is_null($this->project))
		{
			$this->project_id = $request->getParameter('project_id');
			$this->project = Doctrine::getTable('SdProject')->find($this->project_id);
		}

		if(is_null($this->projectUserArray))
		{
			$this->projectUserArray = SdProjectTable::getProjectUserArray($this->project_id);
		}

		$questionUserId = $request->getParameter('question-user-id');
	
		//prepare the filters
		if(is_null($this->filters))
		{
			//this means we're getting the filters from an ajax request
			$this->filters = (array) json_decode($request->getParameter('filters'));
			//We should store filters in the session now
			$this->getUser()->setAttribute('searchFilters-'.$this->project['id'].'-'.$questionUserId, $this->filters);
		}

		//Filters that get applied in all cases
		$this->filters['is_archived'] = 0;
		$this->filters['sprint_id'] = 'not null';
		$this->filters['project_id'] = $this->project['id'];
		$this->filters['s.active'] = 1;
		
		//prepare the sort
		if(is_null($this->sort))
		{
			//this means we're getting the sort from an ajax request
			$this->sort = (array) json_decode($request->getParameter('sort'));
			
			//We should store filters in the session now
			$this->getUser()->setAttribute('searchSort-'.$this->project['id'].'-'.$questionUserId, $this->sort);
		}

		$this->options = array();
		$this->options['joinSprints'] = true;

		$this->tasks = SdTaskTable::getTasks($this->filters, $this->sort, $this->options);
	}
}