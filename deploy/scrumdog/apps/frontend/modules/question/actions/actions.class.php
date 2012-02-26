<?php

/**
 * question actions.
 *
 * @package    scrumdog
 * @subpackage question
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class questionActions extends sfActions
{
	public function executeIndex(sfWebRequest $request)
	{
		$this->userName = $request->getParameter('username');
		$this->questionUser = SdUserTable::retrieveByUsername($this->userName);
		$this->dateString = $request->getParameter('date');

		//deal with today
		$UserDateTimeZone = new DateTimeZone($this->questionUser->getTimeZone());
		$UserDateTime = new DateTime("now", $UserDateTimeZone);
		$UserDateString = $UserDateTime->format('Y-m-d');
			
		if($this->dateString=='today' || $this->dateString=='current')
		{
			$this->isCurrentDay = true;
			$this->dateString = $UserDateString;
		}
		else
		{
			$this->isCurrentDay = $this->dateString==$UserDateString ? true : false;
		}

		$this->questionTime = strtotime($this->dateString);
		$this->prevTime = $this->questionTime - 24*60*60;
		$this->nextTime = $this->questionTime + 24*60*60;
		$this->projectUserArray = SdProjectTable::getProjectUserArray($this->project_id);
		//This and the above line could share the same DB call
		$this->projectUsers = $this->project->getActiveProjectUsers();
		$this->searchFilters = $this->getUser()->getSessionData('searchFilters-'.$this->project_id.'-'.$this->questionUser['id']);
		$this->searchSort = $this->getUser()->getSessionData('searchSort-'.$this->project_id.'-'.$this->questionUser['id']);

		//get existing question task data
		//setup question filters
		$q_filters = array();
		$q_filters['user_id'] = $this->questionUser->getId();
		$q_filters['project_id'] = $this->project_id;

		//previous day
		if($this->isCurrentDay)
		{
			$prevDate = date("Y-m-d", $this->prevTime);
			$q_filters['date'] = $prevDate;
			$questionCollection = Doctrine::getTable('SdQuestion')->getQuestions($q_filters);
			$this->yesterQuestion = isset($questionCollection[0]) ? $questionCollection[0] : NULL;
			$this->yesterHoursCollection = Doctrine::getTable('SdTaskHours')->getTaskHours($q_filters, array(), array('joinTasks' => true));
		}

		//current day
		$q_filters['date'] = $this->dateString;
		$questionCollection = Doctrine::getTable('SdQuestion')->getQuestions($q_filters);
		$this->todayQuestion = isset($questionCollection[0]) ? $questionCollection[0] : NULL;
		$this->todayHoursCollection = Doctrine::getTable('SdTaskHours')->getTaskHours($q_filters, array(), array('joinTasks' => true));
		
		// Is Owner
		$this->isOwner = ( $this->getUser()->getId() == $this->questionUser->getId() || $this->getUser()->isProjectOwner($this->project_id) )? true : false;
	}

	public function executeSave(sfWebRequest $request)
	{
		$this->userName = $request->getParameter('username');
		$this->questionUser = SdUserTable::retrieveByUsername($this->userName);
		$this->dateString = $request->getParameter('date');

		$questionsArray = $request->getParameter('questions');

		//setup filters
		$filters = array();
		$filters['user_id'] = $this->questionUser->getId();
		$filters['project_id'] = $this->project_id;

		//check if we've got yesterdata
		if(isset($questionsArray['yester-hours']))
		{
			$this->questionTime = strtotime($this->dateString);
			$this->prevTime = $this->questionTime - 24*60*60;
			$prevDate = date("Y-m-d", $this->prevTime);
			
			$filters['date'] = $prevDate;

			//get existing Question record (or create it if it doesn't exist yet)
			$questionCollection = Doctrine::getTable('SdQuestion')->getQuestions($filters);
//var_dump(count($questionCollection));

			if(count($questionCollection)<1)
			{
				$question = new SdQuestion();
				$question->setProjectId($this->project_id);
				$question->setUserId($this->questionUser->getId());
				$question->setDate($prevDate);
			}
			else
			{
				$question = $questionCollection[0];
			}

			//get the new Question from the post
			$question->setWork($questionsArray['yester-work']);
			
			//Set to 0 if total hours are NULL
			if($questionsArray['yester-hours']=='')
				$questionsArray['yester-hours'] = 0;
				
			$question->setHours($questionsArray['yester-hours']);

			//save the new Question record
//var_dump($question->toArray()); die();
			$question->save();

			//get existing TaskHour records 
			$taskHoursCollection = Doctrine::getTable('SdTaskHours')->getTaskHours($filters);

			//create an indexed $taskHoursArray
			$existingTaskHoursArray = array();
			foreach($taskHoursCollection as $taskHours)
			{
				$existingTaskHoursArray[$taskHours->getTaskId()] = $taskHours;
			}
			
			//get the new QuestionTask records from the post
			$newTaskHoursArray = array();

			if(is_array($questionsArray['yester']['tasks']))
			{
				foreach($questionsArray['yester']['tasks'] as $key => $taskId)
				{
					//if the task does not already exist add it to the existing array
					if(!isset($existingTaskHoursArray[$taskId]))
					{
						$newHours = $questionsArray['yester']['hours'][$key];
						$taskHours = new SdTaskHours();
						$taskHours->setUserId($this->questionUser->getId());
						$taskHours->setDate($prevDate);
						$taskHours->setTaskId($taskId);
						$taskHours->setProjectId($this->project_id);
						$taskHours->setHours($newHours);
						$newTaskHoursArray[$taskId] = $taskHours;
	
						//add a question history record here for adding a task
						$qh = new SdQuestionHistory();
						$qh->setUserId($this->getUser()->getId());
						$qh->setQuestionId($question->getId());
						$qh->setChangeType('task_hours');
						$qh->setPreviousValue(0);
						$qh->setNewValue($newHours);
						$qh->setPreviousId($taskId);
						$qh->setNewId($taskId);
						$qh->save();
					}
					else //the task already existed so modify it
					{
						$newTaskHoursArray[$taskId] = $existingTaskHoursArray[$taskId];
						//check if hours are different
						if($existingTaskHoursArray[$taskId]->getHours() != $questionsArray['yester']['hours'][$key])
						{
							$prevHours = $existingTaskHoursArray[$taskId]->getHours();
							$newHours = $questionsArray['yester']['hours'][$key];
							$newTaskHoursArray[$taskId]->setHours($newHours);
	
							//add a question history record
							$qh = new SdQuestionHistory();
							$qh->setUserId($this->getUser()->getId());
							$qh->setQuestionId($question->getId());
							$qh->setChangeType('task_hours');
							$qh->setPreviousValue($prevHours);
							$qh->setNewValue($newHours);
							$qh->setPreviousId($taskId);
							$qh->setNewId($taskId);
							$qh->save();
						}
					}
				}
			}

			//check if any tasks have been deleted
			foreach($existingTaskHoursArray as $taskId => $taskHours)
			{
				if(!isset($newTaskHoursArray[$taskId]))
				{				
					//add a question history record for deletion
					$qh = new SdQuestionHistory();
					$qh->setUserId($this->getUser()->getId());
					$qh->setQuestionId($question->getId());
					$qh->setChangeType('task_hours');
					$qh->setPreviousValue($taskHours->getHours());
					$qh->setNewValue(0);
					$qh->setPreviousId($taskId);
					$qh->setNewId($taskId);
					$qh->save();

					$taskHours->delete();
				}
			}
			
			//save the TaskHours records
			foreach($newTaskHoursArray as $taskHours)
			{
				$taskHours->save();
			}
		}

		//NOW START WITH TODAY'S INFORMATION
		//alter filters
		$filters['date'] = $this->dateString;

		//get existing Question record (or create it if it doesn't exist yet)
		$questionCollection = Doctrine::getTable('SdQuestion')->getQuestions($filters);

		if(count($questionCollection)<1)
		{
			$question = new SdQuestion();
			$question->setProjectId($this->project_id);
			$question->setUserId($this->questionUser->getId());
			$question->setDate($this->dateString);
		}
		else
		{
			$question = $questionCollection[0];
		}

		//get the new Question from the post
		$question->setWork($questionsArray['today-work']);
		$question->setObstacles($questionsArray['today-obstacles']);
		
		//Set to 0 if total hours are NULL
		if($questionsArray['today-hours']=='')
			$questionsArray['today-hours'] = 0;
		
		$question->setHours($questionsArray['today-hours']);
//var_dump($question->toArray()); die();
		//save the new Question record
		$question->save();

		//get existing TaskHour records 
		$taskHoursCollection = Doctrine::getTable('SdTaskHours')->getTaskHours($filters);

		//create an indexed $taskHoursArray
		$existingTaskHoursArray = array();
		foreach($taskHoursCollection as $taskHours)
		{
			$existingTaskHoursArray[$taskHours->getTaskId()] = $taskHours;
		}
		
		//get the new QuestionTask records from the post
		$newTaskHoursArray = array();
		if(is_array($questionsArray['today']['tasks']))
		{
			foreach($questionsArray['today']['tasks'] as $key => $taskId)
			{
				//if the task does not already exist add it to the existing array
				if(!isset($existingTaskHoursArray[$taskId]))
				{
					$newHours = $questionsArray['today']['hours'][$key];
					$taskHours = new SdTaskHours();
					$taskHours->setUserId($this->questionUser->getId());
					$taskHours->setDate($this->dateString);
					$taskHours->setTaskId($taskId);
					$taskHours->setProjectId($this->project_id);
					$taskHours->setHours($newHours);
					$newTaskHoursArray[$taskId] = $taskHours;
	
					//add a question history record here for adding a task
					$qh = new SdQuestionHistory();
					$qh->setUserId($this->getUser()->getId());
					$qh->setQuestionId($question->getId());
					$qh->setChangeType('task_hours');
					$qh->setPreviousValue(0);
					$qh->setNewValue($newHours);
					$qh->setPreviousId($taskId);
					$qh->setNewId($taskId);
					$qh->save();
				}
				else //the task already existed so modify it
				{
					$newTaskHoursArray[$taskId] = $existingTaskHoursArray[$taskId];
					//check if hours are different
					if($existingTaskHoursArray[$taskId]->getHours() != $questionsArray['today']['hours'][$key])
					{
						$prevHours = $existingTaskHoursArray[$taskId]->getHours();
						$newHours = $questionsArray['today']['hours'][$key];
						$newTaskHoursArray[$taskId]->setHours($newHours);
	
						//add a question history record
						$qh = new SdQuestionHistory();
						$qh->setUserId($this->getUser()->getId());
						$qh->setQuestionId($question->getId());
						$qh->setChangeType('task_hours');
						$qh->setPreviousValue($prevHours);
						$qh->setNewValue($newHours);
						$qh->setPreviousId($taskId);
						$qh->setNewId($taskId);
						$qh->save();
					}
				}
			}
		}

		//check if any tasks have been deleted
		foreach($existingTaskHoursArray as $taskId => $taskHours)
		{
			if(!isset($newTaskHoursArray[$taskId]))
			{				
				//add a question history record for deletion
				$qh = new SdQuestionHistory();
				$qh->setUserId($this->getUser()->getId());
				$qh->setQuestionId($question->getId());
				$qh->setChangeType('task_hours');
				$qh->setPreviousValue($taskHours->getHours());
				$qh->setNewValue(0);
				$qh->setPreviousId($taskId);
				$qh->setNewId($taskId);
				$qh->save();

				$taskHours->delete();
			}
		}
		
		//save the TaskHours records
		foreach($newTaskHoursArray as $taskHours)
		{
			$taskHours->save();
		}

//var_dump($questionsArray); //die();

		//Set the user flash message
		$this->getUser()->setFlash('success', 'Your answers have been saved.');

		//redirect back to questions page
		$this->redirect('@project_questions?project_id='.$this->project_id.'&username='.$this->userName.'&date='.$this->dateString);
	}

	public function executeSearchTableBody(sfWebRequest $request)
	{
		sfConfig::set('sf_web_debug', false);
	}

	public function executeCalendar(sfWebRequest $request)
	{
		sfConfig::set('sf_web_debug', false);
	}
	
	public function executeWork(sfWebRequest $request)
	{
		$this->startDate = $this->getRequestParameter('start_date');
		$this->endDate = $this->getRequestParameter('end_date');
		$this->usersArray = $this->getRequestParameter('users');
		$this->projectUsers = $this->project->getActiveProjectUsers();
	
		if(is_null($this->startDate))
			$this->csvLink = $_SERVER["REQUEST_URI"].'?csv=true';
		else
			$this->csvLink = $_SERVER["REQUEST_URI"].'&csv=true';
		
		if(is_null($this->usersArray))
		{
			$this->usersArray = array();
			//if this is the first time the user is visiting the page then select all users
			if(is_null($this->startDate))
			{	
				foreach($this->projectUsers as $projectUser)
				{
					$this->usersArray[] = $projectUser->getUserId();
				}
			}
		}
		
		if(is_null($this->startDate))
			$this->startDate = date('Y-m-d', time()-7*24*60*60);
			
		if(is_null($this->endDate))
			$this->endDate = date('Y-m-d');
		
		$filters = array();
		$filters['startDate'] = $this->startDate;
		$filters['endDate'] = $this->endDate;
		$filters['project_id'] = $this->project_id;
		$filters['hours'] = '>0';
		$filters['users'] = $this->usersArray;
		
		$sort = array();
		$sort['date'] = 'asc';
		$sort['user_id'] = 'asc';
		
		$options = array();
		$options['joinUsers'] = true;

		$this->questions = Doctrine::getTable('SdQuestion')->getQuestions($filters, $sort, $options);
		
		$this->rowCount = count($this->questions);
		
		$csv = $this->getRequestParameter('csv');
		
		if($csv=='true')
		{
			$filename = 'project_'.$this->project_id.'_'.date('m-j-Y', strtotime($this->startDate)).'_to_'.date('m-j-Y', strtotime($this->endDate)).'_hours.csv';
			sfConfig::set('sf_web_debug', false);
			
			$response = $this->getResponse();
			$response->setContentType('text/csv');
		    $response->setHttpHeader('Content-Disposition', "attachment; filename=".$filename);
		    $response->addCacheControlHttpHeader('no-cache');

			return 'CSV';
		}
	}
	
	public function preExecute()
	{
		$this->project_id = $this->getRequestParameter('project_id');
		$this->project = Doctrine::getTable('SdProject')->find($this->project_id);
		$exemptActions = array();
		if(!$this->project && !in_array($this->getRequestParameter('action'), $exemptActions))
		{
			$this->getContext()->getRequest()->setParameter('nav_scope', 'main');
			$this->forward('error', 'index');
		}
		if(!$this->getUser()->isProjectMember($this->project_id) && !in_array($this->getRequestParameter('action'), $exemptActions))
		{
			
	      $this->getUser()->setFlash('error', 'You are not a member of this project.');
	      $this->redirect('@project_requestjoin?project_id='.$this->project_id);
	    }
	}
}
