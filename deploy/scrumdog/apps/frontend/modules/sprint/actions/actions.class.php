<?php

/**
 * sprint actions.
 *
 * @package    scrumdog
 * @subpackage sprint
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class sprintActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  
  public function executeCreate(sfWebRequest $request)
  {
	$this->project_id = $this->getRequestParameter('project_id');
	$this->project = Doctrine::getTable('SdProject')->find($this->project_id);
	if(!$this->project)
	{
		$request->setParameter('nav_scope', 'main');
		$this->forward('error', 'index');
	}
	if(!$this->getUser()->isProjectMember($this->project_id))
	{
		
      $this->getUser()->setFlash('error', 'You are not a member of this project.');
      $this->redirect('@project_requestjoin?project_id='.$this->project_id);
    }
	$this->form = new SdSprintForm();

    if ($request->getMethod() == sfRequest::POST)
    {
      $sprint = $request->getParameter('sprint', array());
      $this->form->bind($sprint);

      if ($this->form->isValid())
      {	
        try
        {	
			$StartTime = date('h:i:s', strtotime($sprint['scrum_start_time']));
			$sprintObject = new SdSprint();
			$sprintObject->setName($sprint['name']);
			$sprintObject->setDescription($sprint['description']);
			$sprintObject->setStartDate($sprint['start_date']);
			$sprintObject->setEndDate($sprint['end_date']);
			$sprintObject->setScrumStartTime($StartTime);
			$sprintObject->setScrumTimeZoneName($sprint['scrum_time_zone_name']);
			$sprintObject->setScrumDays(implode($sprint['scrum_days'], ','));
			$sprintObject->setProjectId($this->project_id); 
			$sprintObject->save(); 

		    $this->getUser()->setFlash('success', 'Your sprint has been created.', true);
		    $this->redirect('@sprint_dashboard?sprint_id='.$sprintObject->getId());
        }
        catch (sfStopException $e)
        {
          throw $e; 
        }
        catch (Exception $e)
        {	
          $this->getUser()->setFlash('error', $e, false);
          return sfView::SUCCESS;
        }
      }
      else
      {
        $this->getUser()->setFlash('error', 'There is a problem with the data you entered into the form.', false);
      }
    }
  }

  public function executeDashboard(sfWebRequest $request)
  {
    $this->project_id = $request->getParameter('project_id');
	$this->project = Doctrine::getTable('SdProject')->find($this->project_id);	
	if(!$this->project)
	{
		$request->setParameter('nav_scope', 'main');
		$this->forward('error', 'index');
	}
	if(!$this->getUser()->isProjectMember($this->project_id))
	{
		
	  $this->getUser()->setFlash('error', 'You are not a member of this project.');
	  $this->redirect('@project_requestjoin?project_id='.$this->project_id);
	}

	$this->sprint_id = $request->getParameter('sprint_id');
	$this->sprint = Doctrine::getTable('SdSprint')->find($this->sprint_id);
	$this->filters = $this->getUser()->getSessionData('sprintFilters-'.$this->sprint_id);
	$this->sort = $this->getUser()->getSessionData('sprintSort-'.$this->sprint_id);
	$this->projectUserArray = SdProjectTable::getProjectUserArray($this->project_id);
	$this->activeSprints = $this->project->getActiveSprints();
	$this->hasActiveSprints = count($this->activeSprints)>0;
	$request->setParameter('dialogmode', 'sprint');
  }

  public function executeBacklogBody(sfWebRequest $request)
  {
	$this->project_id = $request->getParameter('project_id');
	$this->project = Doctrine::getTable('SdProject')->find($this->project_id);
	if(!$this->project)
	{
		$request->setParameter('nav_scope', 'main');
		$this->forward('error', 'index');
	}
	if(!$this->getUser()->isProjectMember($this->project_id))
	{
		
	  $this->getUser()->setFlash('error', 'You are not a member of this project.');
	  $this->redirect('@project_requestjoin?project_id='.$this->project_id);
	}
  }
  
  public function executeManage(sfWebRequest $request)
  {
  	$context = $this->getContext();
	$currentRoute = $this->getContext()->getRouting()->getCurrentRouteName();
	if($request->getParameter('redirect'))
	{
		$this->redirectUrl = $request->getParameter('redirect');
	}
	else
	{
		$this->redirectUrl = $request->getReferer();
	}
	$this->project_id = $this->getRequestParameter('project_id');
	$this->project = Doctrine::getTable('SdProject')->find($this->project_id);

	if(!$this->project)
	{
		$request->setParameter('nav_scope', 'main');
		$this->forward('error', 'index');
	}
	if(!$this->getUser()->isProjectMember($this->project_id))
	{
	  $this->getUser()->setFlash('error', 'You are not a member of this project.');
	  $this->redirect('@project_requestjoin?project_id='.$this->project_id);
	}

  	$this->sprint_id = $this->getRequestParameter('sprint_id');
	$sprintObject = Doctrine::getTable('SdSprint')->find($this->sprint_id);
	$sprintObject->setScrumDays(explode(',', $sprintObject->getScrumDays()));
	$StartTime = date('G:i', strtotime($sprintObject->getScrumStartTime()));
	$sprintObject->setScrumStartTime($StartTime);
	$this->form = new SdSprintForm($sprintObject);
	if ($request->getMethod() == sfRequest::POST)
    {
		$sprint = $request->getParameter('sprint', array());
		$this->form->bind($sprint);
		if ($this->form->isValid())
    	{
			try 
			{
				$StartTime = date('h:i:s', strtotime($sprint['scrum_start_time']));
				$sprintObject->setName($sprint['name']);
				$sprintObject->setDescription($sprint['description']);
				$sprintObject->setStartDate($sprint['start_date']);
				$sprintObject->setEndDate($sprint['end_date']);
				$sprintObject->setScrumStartTime($StartTime);
				$sprintObject->setScrumTimeZoneName($sprint['scrum_time_zone_name']);
				$sprintObject->setScrumDays(implode($sprint['scrum_days'], ','));
				$sprintObject->setProjectId($this->project_id); 
				$sprintObject->save(); 
				
				$this->getUser()->setFlash('success', 'Your sprint has been Updated.', true);
				if(eregi('project', $this->getRequestParameter('redirect')))
					$this->redirect($this->getRequestParameter('redirect'));
				else
					$this->redirect('@sprint_dashboard?sprint_id='.$sprintObject->getId());
					
			}
			catch (sfStopException $e)
			{
			  throw $e; 
			}
			catch (Exception $e)
			{	
			  $this->getUser()->setFlash('error', $e, false);
			  return sfView::SUCCESS;
			}
		}
	}
	
  }

	public function executeBurndown(sfWebRequest $request)
	{
		$this->project_id = $this->getRequestParameter('project_id');
		$this->project = Doctrine::getTable('SdProject')->find($this->project_id);
		if(!$this->project)
		{
			$request->setParameter('nav_scope', 'main');
			$this->forward('error', 'index');
		}
		if(!$this->getUser()->isProjectMember($this->project_id))
		{
			
		  $this->getUser()->setFlash('error', 'You are not a member of this project.');
		  $this->redirect('@project_requestjoin?project_id='.$this->project_id);
		}
		$this->sprint_id = $this->getRequestParameter('sprint_id');
		//$this->sprint = Doctrine::getTable('SdSprint')->find($this->sprint_id);
	}

	public function executeBurndownData(sfWebRequest $request)
	{
		$this->project_id = $this->getRequestParameter('project_id');
		$this->project = Doctrine::getTable('SdProject')->find($this->project_id);
		if(!$this->project)
		{
			$request->setParameter('nav_scope', 'main');
			$this->forward('error', 'index');
		}
		if(!$this->getUser()->isProjectMember($this->project_id))
		{
			
		  $this->getUser()->setFlash('error', 'You are not a member of this project.');
		  $this->redirect('@project_requestjoin?project_id='.$this->project_id);
		}
		$this->sprint_id = $this->getRequestParameter('sprint_id');
		$this->sprint = Doctrine::getTable('SdSprint')->find($this->sprint_id);
		$sprintStartTime = strtotime($this->sprint->getStartDate());
		$sprintEndTime = strtotime($this->sprint->getEndDate());

		$sprintDayCount = ($sprintEndTime-$sprintStartTime)/(60*60*24);

		//get all sprint tasks
		$filters = array('sprint_id' => $this->sprint_id);
		$taskCollection = SdTaskTable::getTasks($filters);

		//loop through the tasks and prepare them
		$confirmedIndex = array();
		$this->totalSprintHours = 0;
		$this->preAcceptedTaskHours = 0;
		$this->postAcceptedTaskHours = 0;
		foreach($taskCollection as $task)
		{
			$this->totalSprintHours += $task->getEstimatedHours();
			$confirmedIndex[$task->getDateConfirmed()][] = $task;
			$dateConfirmed = $task->getDateConfirmed();
			$confirmedTime = strtotime($dateConfirmed);
			if(!is_null($dateConfirmed))
			{
				if($confirmedTime < $sprintStartTime)
					$this->preAcceptedTaskHours += $task->getEstimatedHours();
				elseif($confirmedTime > $sprintEndTime)
					$this->postAcceptedTaskHours += $task->getEstimatedHours();
			}
		}

		$this->idealDailyHours = $this->totalSprintHours / $sprintDayCount;
		
		$pointerTime = $sprintStartTime;
		$this->sprintDays = array();
		$sprintWorkDays = explode(',', $this->sprint->getScrumDays());
		$i=0;
		$isFutureDay = false;
		
		$totalAcceptedHours = $this->preAcceptedTaskHours;
		
		while($pointerTime <= $sprintEndTime)
		{
			$day = new SprintDay();
			$day->date = date('n-j', $pointerTime);
	
			if(!in_array(date('w'), $sprintWorkDays))
				$day->isWorkDay = false;

			if($pointerTime > time())
				$day->isFutureDay = true;

			if(is_array($confirmedIndex[date('Y-m-d', $pointerTime)]))
			{
				$day->confirmedTasks = $confirmedIndex[date('Y-m-d', $pointerTime)];
	
				foreach($day->confirmedTasks as $task)
				{
					$day->confirmedHours += $task->getEstimatedHours();
				}
			}

			if($i==0)
				$day->confirmedHours += $this->preAcceptedTaskHours;

			$pointerTime = $pointerTime + 60*60*24;
			$this->sprintDays[] = $day;
			$i++;
		}
		
		//fix the postAcceptedTaskHours
		$day->confirmedHours += $this->postAcceptedTaskHours;
		
		$this->getResponse()->setContentType('text/xml');
	}
}

