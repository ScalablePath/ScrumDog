<?php

/**
 * task actions.
 *
 * @package    scrumdog
 * @subpackage task
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class taskActions extends sfActions {
	/**
	 * Executes index action
	 *
	 * @param sfRequest $request A request object
	 */
	public function executeCreate(sfWebRequest $request) {
		$this->project_id = $this->getRequestParameter ( 'project_id' );
		$this->project = Doctrine::getTable ( 'SdProject' )->find ( $this->project_id );
		if (! $this->project) {
			$request->setParameter ( 'nav_scope', 'main' );
			$this->forward ( 'error', 'index' );
		}
		if (! $this->getUser ()->isProjectMember ( $this->project_id )) {
			
			$this->getUser ()->setFlash ( 'error', 'You are not a member of this project.' );
			$this->redirect ( '@project_requestjoin?project_id=' . $this->project_id );
		}
		$this->form = new SdTaskForm ( );
		$sprint_id = $request->getParameter ( 'sprint_id' );
		$currentRouteScope = Fluide_Symfony_Util::getCurrentRouteScope ();
		if ($currentRouteScope == 'sprint' && ! is_null ( $sprint_id ))
			$this->createRoute = '@sprint_createtask?sprint_id=' . $sprint_id;
		else
			$this->createRoute = '@project_createtask?project_id=' . $this->project_id;
		
		if ($request->getMethod () == sfRequest::POST) {
			$task = $request->getParameter ( 'task', array () );
			$this->form->bind ( $task );
			
			if ($this->getUser ()->isProjectMember ( $this->project_id ) && $this->form->isValid ()) {
				try {
					//create the task
					$newTask = new SdTask ( );
					$newTask->setProjectId ( $this->project_id );
					$newTask->setCreatorUserId ( $this->getUser ()->getId () );
					$newTask->setName ( $task ['name'] );
					$newTask->setDescription ( $task ['description'] );
					if (is_numeric ( $task ['estimated_hours'] ))
						$newTask->setEstimatedHours ( $task ['estimated_hours'] );
					$newTask->setBusinessValue ( $task ['business_value'] );
					$newTask->setPriority ( $task ['priority'] );
					$newTask->setStatus ( $task ['status'] );
					
					if ($task ['parent_id'] == '')
						$task ['parent_id'] = NULL;
					$newTask->setParentId ( $task ['parent_id'] );
					if ($task ['user_id'] == '')
						$task ['user_id'] = NULL;
					$newTask->setUserId ( $task ['user_id'] );
					
					if ($task ['sprint_id'] == '')
						$task ['sprint_id'] = NULL;
					$newTask->setSprintId ( $task ['sprint_id'] );
					
					$newTask->save ();
					
					$this->getUser ()->setFlash ( 'success', 'Your task has been created.' );
					
					// Add files to task
					foreach ( $_FILES as $file_to_upload ) {
						
						$fileName = $file_to_upload ['name'];
						$file = new SdFile ( );
						$file->setFilename ( $fileName );
						$file->save ();
						
						//save the physical file
						$directoryPath = $file->getFileDirectoryPath ( true );
						if (! file_exists ( $directoryPath ))
							mkdir ( $directoryPath, 0755, true );
						
						if (move_uploaded_file ( $file_to_upload ['tmp_name'], $file->getFilePath () )) {
							
							$taskId = $newTask->getId();
							//create the TaskFile
							$taskFile = new SdTaskFile ( );
							$taskFile->setFileId ( $file->getId () );
							$taskFile->setTaskId ( $taskId );
							$taskFile->save ();
						
						} else {
							$file->delete ();
						}
					}
					
					//Send an email to the assigned user if there is one
					if (! is_null ( $newTask->getUserId () )) {
						$newTask->sendAssignmentEmail ( true );
					}
					
					if (! is_null ( $task ['sprint_id'] ))
						$this->redirect ( '@sprint_dashboard?sprint_id=' . $task ['sprint_id'] );
					else
						$this->redirect ( '@project_dashboard?project_id=' . $this->project_id );
				} catch ( sfStopException $e ) {
					throw $e;
				} catch ( Exception $e ) {
					//$this->getUser()->setFlash('error', 'Unable to create your task');
					$this->getUser ()->setFlash ( 'error', $e );
					return sfView::SUCCESS;
				}
			} else {
				$this->getUser ()->setFlash ( 'error', 'There is a problem with the data you entered into the form.', false );
			}
		}
	}
	
	public function executeAjaxSave(sfWebRequest $request) {
		$taskArray = $request->getParameter ( 'task', array () );
		//var_dump($taskArray['id']);
		$this->task = Doctrine::getTable ( 'SdTask' )->find ( $taskArray ['id'] );
		$project_id = $this->task->getProjectId ();
		
		//fail by default
		//$this->output = '{"status": "error", "id": "'.$taskArray['id'].'"}';
		$this->status = "error";
		$this->id = $taskArray ['id'];
		$this->message = "default failure";
		
		if ($request->getMethod () == sfRequest::POST && $this->getUser ()->isProjectMember ( $project_id )) {
			
			if (isset ( $taskArray ['name'] ))
				$this->task->setName ( $taskArray ['name'] );
			if (isset ( $taskArray ['business_value'] ))
				$this->task->setBusinessValue ( $taskArray ['business_value'] );
			if (isset ( $taskArray ['priority'] ))
				$this->task->setPriority ( $taskArray ['priority'] );
			if (isset ( $taskArray ['status'] ))
				$this->task->setStatus ( $taskArray ['status'] );
			if (isset ( $taskArray ['estimated_hours'] )) {
				if (trim ( $taskArray ['estimated_hours'] ) == '')
					$taskArray ['estimated_hours'] = NULL;
				$this->task->setEstimatedHours ( $taskArray ['estimated_hours'] );
			}
			if (isset ( $taskArray ['parent_id'] )) {
				if (trim ( $taskArray ['parent_id'] ) == '')
					$taskArray ['parent_id'] = NULL;
				$this->task->setParentId ( $taskArray ['parent_id'] );
			}
			if (isset ( $taskArray ['description'] ))
				$this->task->setDescription ( $taskArray ['description'] );
			if (isset ( $taskArray ['sprint_id'] )) {
				if (trim ( $taskArray ['sprint_id'] ) == '')
					$taskArray ['sprint_id'] = NULL;
				$this->task->setSprintId ( $taskArray ['sprint_id'] );
			}
			if (isset ( $taskArray ['is_archived'] )) {
				$this->task->setIsArchived ( $taskArray ['is_archived'] );
			}
			if (isset ( $taskArray ['user_id'] )) {
				if (trim ( $taskArray ['user_id'] ) == '')
					$taskArray ['user_id'] = NULL;
				$this->task->setUserId ( $taskArray ['user_id'] );
			}
			
			try {
				$this->task->save ();
				$this->status = "success";
				$this->id = $taskArray ['id'];
				$this->message = "Task successfully saved";
			} catch ( Exception $e ) {
				$this->status = "error";
				$this->id = $taskArray ['id'];
				$this->message = $e->getMessage ();
			}
		}
		
		$this->getResponse ()->setHttpHeader ( 'Content-type', 'application/json' );
	}
	
	public function executeAjaxCreate(sfWebRequest $request) {
		$taskArray = $request->getParameter ( 'task', array () );
		$this->task = new SdTask ( );
		
		if (isset ( $taskArray ['sprint_id'] )) {
			$sprint = Doctrine::getTable ( 'SdSprint' )->find ( $taskArray ['sprint_id'] );
			$project_id = $sprint->getProjectId ();
		} else {
			$project_id = $taskArray ['project_id'];
		}
		
		//fail by default
		$this->output = '{"status": "error", "id": "new"}';
		
		if ($request->getMethod () == sfRequest::POST && $this->getUser ()->isProjectMember ( $project_id )) {
			$this->task->setCreatorUserId ( $this->getUser ()->getId () );
			if (isset ( $taskArray ['name'] ))
				$this->task->setName ( $taskArray ['name'] );
			if (isset ( $taskArray ['estimated_hours'] )) {
				if (trim ( $taskArray ['estimated_hours'] == '' ))
					$taskArray ['estimated_hours'] = NULL;
				$this->task->setEstimatedHours ( $taskArray ['estimated_hours'] );
			}
			if (isset ( $taskArray ['sprint_id'] )) {
				if (trim ( $taskArray ['sprint_id'] == '' ))
					$taskArray ['sprint_id'] = NULL;
				$this->task->setSprintId ( $taskArray ['sprint_id'] );
			}
			if (isset ( $taskArray ['parent_id'] )) {
				if (trim ( $taskArray ['parent_id'] ) == '')
					$taskArray ['parent_id'] = NULL;
				$this->task->setParentId ( $taskArray ['parent_id'] );
			}
			if (isset ( $taskArray ['user_id'] )) {
				if (trim ( $taskArray ['user_id'] ) == '')
					$taskArray ['user_id'] = NULL;
				$this->task->setUserId ( $taskArray ['user_id'] );
			}
			if (isset ( $taskArray ['status'] ))
				$this->task->setStatus ( $taskArray ['status'] );
			if (isset ( $taskArray ['business_value'] ))
				$this->task->setBusinessValue ( $taskArray ['business_value'] );
			if (isset ( $taskArray ['priority'] ))
				$this->task->setPriority ( $taskArray ['priority'] );
			
			$this->task->setProjectId ( $project_id );
			
			try {
				$this->task->save ();
				$this->status = "success";
				$this->message = "Task successfully saved";
			} catch ( Exception $e ) {
				$this->status = "error";
				$this->message = $e->getMessage ();
			}
		}
		
		$this->getResponse ()->setHttpHeader ( 'Content-type', 'application/json' );
		return sfView::SUCCESS;
	}
	
	public function executeView(sfWebRequest $request) {
		$this->project_id = $this->getRequestParameter ( 'project_id' );
		$this->project = Doctrine::getTable ( 'SdProject' )->find ( $this->project_id );
		$this->task_id = $this->getRequestParameter ( 'task_id' );
		$this->task = Doctrine::getTable ( 'SdTask' )->find ( $this->task_id );
		
		if ($this->task->getParentId ())
			$this->parentTask = Doctrine::getTable ( 'SdTask' )->find ( $this->task->getParentId () );
		
		if (! $this->task) {
			$request->setParameter ( 'nav_scope', 'main' );
			$this->forward ( 'error', 'index' );
		}
		if (! $this->getUser ()->isProjectMember ( $this->project_id )) {
			$this->getUser ()->setFlash ( 'error', 'You are not a member of this project.' );
			$this->redirect ( '@project_requestjoin?project_id=' . $this->project_id );
		}
		$this->projectUsers = SdProjectTable::getProjectUserArray ( $this->project_id, NULL, array ('objects' => true ) );
		//get possible parent tasks
		$filters = array ();
		$filters ['project_id'] = $this->project_id;
		$filters ['status'] = 'not-completed';
		
		//make sure none of the child IDs are in the list
		$initialArray = array ($this->task->getId () );
		$idArray = $this->task->getChildIdsRecursive ( $initialArray );
		$filters ['id'] = 'NOT IN (' . implode ( ',', $idArray ) . ')';
		
		if (! is_null ( $this->task->getSprintId () ))
			$filters ['sprint_id'] = $this->task->getSprintId ();
		else
			$filters ['sprint_id'] = 'null';
		$filters ['is_archived'] = $this->task->getIsArchived ();
		
		$sort = array ('task_id' => 'ASC' );
		
		$this->parentTasks = SdTaskTable::getTasks ( $filters, $sort );
		
		/*Sub Tasks */
		//prepare the filters
		$filters = array ('parent_id' => $this->task->getId () );
		//$filters['is_archived'] = 0;
		

		//prepare the sort
		$sort = array ('task_id' => 'ASC' );
		
		$this->subTasks = SdTaskTable::getTasks ( $filters, $sort );
		
		$this->activeSprints = $this->project->getActiveSprints ();
		$this->hasActiveSprints = count ( $this->activeSprints ) > 0;
		$request->setParameter ( 'dialogmode', 'task' );
	}
	
	public function executeAjaxCommentSave(sfWebRequest $request) {
		$taskCommentArray = $request->getParameter ( 'task', array () );
		$taskObject = Doctrine::getTable ( 'SdTask' )->find ( $request->getParameter ( 'task_id' ) );
		$this->taskId = $request->getParameter ( 'task_id' );
		
		$project_id = $taskObject->getProjectId ();
		$this->project = Doctrine::getTable ( 'SdProject' )->find ( $project_id );
		
		if ($request->getMethod () == sfRequest::POST && $this->getUser ()->isProjectMember ( $project_id )) {
			$taskComment = new SdTaskComment ( );
			
			if (isset ( $taskCommentArray ['comment'] )) {
				$taskComment->setTaskId ( $this->taskId );
				$taskComment->setUserId ( $this->getUser ()->getAttribute ( 'id' ) );
				$taskComment->setComment ( $taskCommentArray ['comment'] );
				$taskComment->save ();
			}
			//save the comment in the task history
			$taskObject->saveTaskHistory ( 'comment', '', $taskCommentArray ['comment'], NULL, $taskComment->getId () );
			
			//send an email to the person who the task is currently assigned to
			if ($taskObject->getUser ()->getId () != $this->getUser ()->getId () && is_int ( $taskObject->getUser ()->getId () )) {
				ProjectConfiguration::registerZend ();
				$mail = new Zend_Mail ( );
				sfLoader::loadHelpers ( array ('Url' ) );
				$emailLink = url_for ( '@project_task?task_id=' . $this->taskId, true );
				
				$mail->setSubject ( "{$this->project->getName()} - New Comment on {$taskObject->getName()}" );
				$bodyText = "{$taskObject->getUser()->getFullName()},\n\n" . $this->getUser ()->getFullName () . " has commented on this task that is currently assigned to you.\n\n";
				$bodyText .= "Comment:\n\n{$taskCommentArray['comment']}\n\n";
				$bodyText .= "You can view the task by clicking the link below:\n\n{$emailLink}\n\n-The ScrumDog Team.";
				
				$mail->setBodyText ( $bodyText );
				$mail->setFrom ( 'do-not-reply@scrumdog.com', 'ScrumDog Mail System' );
				$mail->addTo ( $taskObject->getUser ()->getEmail () );
				//var_dump($mail); flush(); die();
				$mailSent = EmailSender::send ( $mail );
			}
			return sfView::SUCCESS;
		} else {
			$this->output = '{"status": "error", "html": ""}';
			return sfView::ERROR;
		}
	}
	
	public function executeFileUpload(sfWebRequest $request) {
		sfConfig::set ( 'sf_web_debug', false );
		
		$end = true;
		
		$fileName = $_FILES ['Filedata'] ['name'];
		$file = new SdFile ( );
		$file->setFilename ( $fileName );
		$file->save ();
		
		//save the physical file
		$directoryPath = $file->getFileDirectoryPath ( true );
		if (! file_exists ( $directoryPath ))
			mkdir ( $directoryPath, 0755, true );
		
		if (move_uploaded_file ( $_FILES ['Filedata'] ['tmp_name'], $file->getFilePath () )) {
			
			$taskId = $this->getRequestParameter ( 'task_id' );
			//create the TaskFile
			$taskFile = new SdTaskFile ( );
			$taskFile->setFileId ( $file->getId () );
			$taskFile->setTaskId ( $taskId );
			$taskFile->save ();
		
		} else {
			$file->delete ();
			$end = false;
		}
		
		if ($end)
			return sfView::SUCCESS;
		else
			return sfView::ERROR;
	}
	
	public function executeFileList(sfWebRequest $request) {
	
	}
	
	public function executeAjaxFileDelete(sfWebRequest $request) {
		$this->project_id = $request->getParameter ( 'project_id' );
		$this->project = Doctrine::getTable ( 'SdProject' )->find ( $this->project_id );
		if (! $this->project) {
			$request->setParameter ( 'nav_scope', 'main' );
			$this->forward ( 'error', 'index' );
		}
		if (! $this->getUser ()->isProjectMember ( $this->project_id )) {
			
			$this->getUser ()->setFlash ( 'error', 'You are not a member of this project.' );
			$this->redirect ( '@project_requestjoin?project_id=' . $this->project_id );
		}
		
		$this->id = $request->getParameter ( 'taskfile_id' );
		try {
			$taskFile = Doctrine::getTable ( 'SdTaskFile' )->find ( $this->id );
			$taskFile->delete ();
			$this->status = "success";
			
			$this->message = "TaskFile successfully deleted";
		} catch ( Exception $e ) {
			$this->status = "error";
			$this->message = $e->getMessage ();
		}
	
	}
	
	public function executeHistory(sfWebRequest $request) {
	
	}
	
	public function executeSubtaskBody(sfWebRequest $request) {
		$this->project_id = $request->getParameter ( 'project_id' );
		$this->project = Doctrine::getTable ( 'SdProject' )->find ( $this->project_id );
		if (! $this->project) {
			$request->setParameter ( 'nav_scope', 'main' );
			$this->forward ( 'error', 'index' );
		}
		if (! $this->getUser ()->isProjectMember ( $this->project_id )) {
			
			$this->getUser ()->setFlash ( 'error', 'You are not a member of this project.' );
			$this->redirect ( '@project_requestjoin?project_id=' . $this->project_id );
		}
	}
	
	public function executeCreateDialog(sfWebRequest $request) {
	
	}
}
