<?php
class defaultComponents extends sfComponents
{
  public function executeMetaNavLeft()
  {
    $this->nav_array = array();
    $this->nav_array[] = array('text' => 'Home', 'link' => '@homepage');
  }

  public function executeMetaNavRight()
  {
    $this->nav_array = array();
    if($this->getUser()->isAuthenticated())
    {
      $logOutNavItem = array();
      $logOutNavItem['text'] = 'logout';
      $logOutNavItem['link'] = '@user_signout';
      $this->nav_array[] = $logOutNavItem;

      $ProfileNavItem = array();
      $ProfileNavItem['text'] = 'profile';
      $ProfileNavItem['link'] = '@member_profile?username='.$this->getUser()->getUsername();
      $this->nav_array[] = $ProfileNavItem;
    }
    else
    {
      $RegisterNavItem = array();
      $RegisterNavItem['text'] = 'register';
      $RegisterNavItem['link'] = '@user_register';
      $this->nav_array[] = $RegisterNavItem;
    }
  }

  public function executeMainNav()
  {
    $this->nav_array = array();   
    $this->nav_array[] = array('text' => 'Home', 'link' => '@homepage');
	$this->nav_array[] = array('text' => 'How It Works', 'link' => '@how_it_works');
    $this->nav_array[] = array('text' => 'About', 'link' => '@about');

   
    $this->nav_array = $this->checkNavActive($this->nav_array);
  }

  public function executeMemberNav()
  {
    $this->nav_array = array();

    $DashboardNavItem = array();
    $DashboardNavItem['text'] = 'Account';
    $DashboardNavItem['link'] = '@member_dashboard';
    $this->nav_array[] = $DashboardNavItem;

    $this->nav_array = $this->checkNavParent($this->nav_array);
  }

  public function executeProjectNav()
  {
    $this->nav_array = array();
    $projectId = $this->getRequestParameter('project_id');
	$project = Doctrine_Query::create()->from('SdProject s')->where('s.id = '.$projectId)->fetchone();	
    $this->nav_array[] = array('text' => 'Project', 'link' => '@project_dashboard?project_id='.$projectId);
	$activeSprints = $project->getActiveSprints();

	if(count($activeSprints)>0)
	{
		if($this->getUser()->isProjectMember($projectId))
		{
			foreach($activeSprints as $sprint)
			{
				$navItem = array('text' => $sprint->getName(), 'link' => '@sprint_dashboard?sprint_id='.$sprint->getId());
				if($sprint->getCurrent())
					$navItem['current'] = true;
				$this->nav_array[] = $navItem;
			}
		}
	}
	else
	{
		$this->nav_array[] = array('text' => 'Create Sprint', 'link' => '@project_createsprint?project_id='.$projectId);
	}

    $this->nav_array = $this->checkNavParent($this->nav_array);
  }

  public function executeSubNav()
  {
    $this->nav_array = array();
	$navScope = $this->getRequestParameter('nav_scope');
      switch($navScope)
      {
        case 'main':
          break;
        case 'member':
          if($this->getUser()->isAuthenticated())
          {
            $userId = $this->getUser()->getId();
            $this->nav_array[] = array('text' => 'Dashboard', 'link' => '@member_dashboard');
            $this->nav_array[] = array('text' => 'Create Project', 'link' => '@member_createproject');
            $this->nav_array[] = array('text' => 'Edit Profile', 'link' => '@member_editprofile');
            $this->nav_array[] = array('text' => 'My Hours', 'link' => '@member_hours');
          }
          break;
        case 'project':
          $projectId = $this->getRequestParameter('project_id');
			if($this->getUser()->isProjectMember($projectId))
			{
				$sprintId = $this->getRequestParameter('sprint_id');
		
				if(isset($sprintId))
				{
					$this->nav_array[] = array('text' => 'Dashboard', 'link' => '@sprint_dashboard?sprint_id='.$sprintId);
					$this->nav_array[] = array('text' => 'Burndown Chart', 'link' => '@sprint_burndown?sprint_id='.$sprintId);
					$this->nav_array[] = array('text' => 'Create Task', 'link' => '@sprint_createtask?sprint_id='.$sprintId);
					$this->nav_array[] = array('text' => 'Manage Sprint', 'link' => '@sprint_manage?sprint_id='.$sprintId);
				}
				else
				{
					$this->nav_array[] = array('text' => 'Dashboard', 'link' => '@project_dashboard?project_id='.$projectId);
					$this->nav_array[] = array('text' => 'Team Members', 'link' => '@project_members?project_id='.$projectId);
					$this->nav_array[] = array('text' => 'Messages/Files', 'link' => '@project_messages?project_id='.$projectId);
					$this->nav_array[] = array('text' => 'Create Sprint', 'link' => '@project_createsprint?project_id='.$projectId);
					$this->nav_array[] = array('text' => 'Create Task', 'link' => '@project_createtask?project_id='.$projectId);
					$this->nav_array[] = array('text' => 'Manage Project', 'link' => '@project_manage?project_id='.$projectId);
					$this->nav_array[] = array('text' => 'Archive', 'link' => '@project_archive?project_id='.$projectId);
					$this->nav_array[] = array('text' => 'Questions', 'link' => '@project_questions?project_id='.$projectId.'&username='.$this->getUser()->getUsername().'&date=today');
					$this->nav_array[] = array('text' => 'Work Summary', 'link' => '@project_work?project_id='.$projectId);
				}
			}
		break;
      }
    $this->nav_array = $this->checkNavActive($this->nav_array);
  }
  public function executeFooter()
  {
    
  }
  
  private function checkNavActive($navArray)
  {
    $newArray = array();
    foreach($navArray as $nav_item)
    {
      if(Fluide_Symfony_Util::checkRouteEquality($nav_item['link'], sfContext::getInstance()->getRouting()->getCurrentInternalUri(true)))
      {
         $nav_item['active'] = true;
      }
      $newArray[] = $nav_item;
    }
    return $newArray;
  }

  private function checkNavParent($navArray)
  {
    $newArray = array();
    $currentRoute = sfContext::getInstance()->getRouting()->getCurrentInternalUri(true);
    $currentRouteArray = explode('_', $currentRoute);

	$nav_scope = $this->getRequestParameter('nav_scope');
	$sprint_id = $this->getRequestParameter('sprint_id');
	$foundMatch = false;
    foreach($navArray as $nav_item)
    {
      $nav_item_array = explode('_', $nav_item['link']);
		if(!$foundMatch)
		{
			if($nav_scope=='project' && isset($sprint_id))
			{
				$navItemTempArray = array_reverse(explode('?', $nav_item['link']));
				parse_str($navItemTempArray[0], $navItemQSArray);
				if(isset($navItemQSArray['sprint_id']) && $navItemQSArray['sprint_id']==$sprint_id)
				{
					$nav_item['active'] = true;
					$foundMatch = true;
				}
			}
			elseif($nav_item_array[0]==$currentRouteArray[0])
			{
				$nav_item['active'] = true;
				$foundMatch = true;
			}
		}
      $newArray[] = $nav_item;
    }
//die();
    return $newArray;
  }

  public function executeInviteMembers()
  {
    $invitemembers['current_route'] = sfContext::getInstance()->getRouting()->getCurrentInternalUri(true);
    $this->form = new SdInviteMemberForm($invitemembers);
  }
}