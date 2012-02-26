<?
class userComponents extends sfComponents
{
  public function executeHeaderSignin()
  {
    $this->renderForm = false;
    if(!$this->getUser()->isAuthenticated())
    {
      $routingURI = sfContext::getInstance()->getRouting()->getCurrentInternalUri();
      if(strpos($routingURI,'auth/signin')===false)
      {
        $this->renderForm = true;
        $this->form = new SdLoginForm();
      }
    }
  }

  public function executeProjects()
  {
    $this->current_user = $this->getUser()->getSdUser();
    $this->current_username = $this->current_user->getUsername();
    
    if($this->getRequestParameter('username'))
    {
      $this->view_username = $this->getRequestParameter('username');
      $this->view_user = SdUserTable::retrieveByUsername($this->view_username, true);
    }
    else
    {
      $this->view_user = $this->current_user;
      $this->view_username = $this->current_username;
    }
	
    $this->projects = SdProjectTable::getProjectsByUser($this->view_user);
    $this->for_current_user = $this->view_username==$this->current_username;
  }

  public function executeBacklogBody(sfWebRequest $request)
  {
	//prepare the filters
	if(is_null($this->filters))
	{
		//this means we're getting the filters from an ajax request
		$this->filters = (array) json_decode($request->getParameter('filters'));

		//We should store filters in the session now
		$this->getUser()->setAttribute('dashboardFilters', $this->filters);
	}

	$this->filters['user_id'] = $this->getUser()->getId();
	$this->filters['is_archived'] = 0;
	$this->filters['sprint_id'] = 'not null';
	$this->filters['s.active'] = 1;
	
	//prepare the sort
	if(is_null($this->sort))
	{
		//this means we're getting the sort from an ajax request
		$this->sort = (array) json_decode($request->getParameter('sort'));
		
		//We should store filters in the session now
		$this->getUser()->setAttribute('dashboardSort', $this->sort);
	}

	$this->tasks = SdTaskTable::getTasks($this->filters, $this->sort, array('joinProjects' => true, 'joinSprints' => true));
  }
}