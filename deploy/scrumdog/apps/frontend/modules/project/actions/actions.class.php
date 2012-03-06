<?php

/**
 * project actions.
 *
 * @package    scrumdog
 * @subpackage project
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class projectActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  
  public function executeCreate(sfWebRequest $request)
  {
    $this->form = new SdProjectForm();

    if ($request->getMethod() == sfRequest::POST)
    {
      $project = $request->getParameter('project', array());
      $this->form->bind($project);

      if ($this->form->isValid())
      {
        try
        {
          $projectObject = $this->form->save();

          //setup the project_user join
          $project_user = new SdProjectUser();
          $project_user->setProjectId($projectObject->getId());
          $project_user->setUserId($this->getUser()->getId());
          $project_user->setRole(1);
          $project_user->save();
          
          //$this->getUser()->getProjectRoleArray();
          
		  $this->getContext()->getConfiguration()->loadHelpers(array('Url'));
		  
          $this->getUser()->setFlash('success', 'Your project has been created! Now you can <a href="'.url_for('@project_members?project_id='.$projectObject->getId()).'">add members</a>, <a href="'.url_for('@project_createsprint?project_id='.$projectObject->getId()).'">create a sprint</a>, or <a href="'.url_for('@project_createtask?project_id='.$projectObject->getId()).'">create a task</a>.');
          
          $this->redirect('@project_dashboard?project_id='.$projectObject->getId());
        }
        catch (sfStopException $e)
        {
          throw $e;
        }
        catch (Exception $e)
        {
          $this->getUser()->setFlash('error', 'Unable to save your project');
          return sfView::SUCCESS;
        }
      }
      else
      {
        $this->getUser()->setFlash('error', 'There is a problem with the data you entered into the form.');
      }
    }
  }

  public function executeDashboard(sfWebRequest $request)
  {
	$this->project_id = $request->getParameter('project_id');
	$this->project = Doctrine::getTable('SdProject')->findOneById($this->project_id);
	//$this->forward404Unless($this->project);	
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
	$this->filters = $this->getUser()->getSessionData('backlogFilters-'.$this->project_id);
	$this->sort = $this->getUser()->getSessionData('backlogSort-'.$this->project_id);
	$this->activeSprints = $this->project->getActiveSprints();
	$this->hasActiveSprints = count($this->activeSprints)>0;
	$request->setParameter('dialogmode', 'backlog');
  }	

  public function executeMembers(sfWebRequest $request)
  {
    $projectId = $request->getParameter('project_id');

    if(!$this->getUser()->isProjectMember($projectId))
	{
      $this->getUser()->setFlash('error', 'You are not a member of this project.');
      $this->redirect('@project_requestjoin?project_id='.$projectId);
    }

    $this->user = $this->getUser();
    $this->isProjectOwner = $this->getUser()->isProjectOwner($projectId);
    $this->project = Doctrine::getTable('SdProject')->find($projectId);
    $this->projectUsers = $this->project->getActiveProjectUsers();
    $this->projectUserCount = count($this->projectUsers);

    $this->pendingUsers = $this->project->getPendingProjectUsers();
    $this->pendingUserCount = count($this->pendingUsers);
    
    $this->pendingUsersNotRegistered = $this->project->getPendingProjectUsersNotRegistered();
    $this->pendingUsersNotRegisteredCount = count($this->pendingUsersNotRegistered);
    

		if ($request->getMethod() == sfRequest::POST)
    	{
			$projectUserArray = $request->getParameter('project_user');
//var_dump($projectUserArray); die();
			if(!is_null($projectUserArray))
			{	
				foreach($projectUserArray['id'] as $id)
				{
					$projectUser = Doctrine::getTable('SdProjectUser')->find($id);
					if(isset($projectUserArray['send_email']) && in_array($id, $projectUserArray['send_email']))
						$projectUser->setSendEmail(1);
					else
						$projectUser->setSendEmail(0);
					$projectUser->save();
				}
				
				$this->getUser()->setFlash('success', 'You have successfully updated the project members.', true);
				$this->redirect('@project_members?project_id='.$projectId);
			}
		}
  }

	public function executeAddmembers(sfWebRequest $request)
	{
		$projectId = $request->getParameter('project_id');
    	$this->project = Doctrine::getTable('SdProject')->find($projectId);
    	$addmembers = array('project_id' => $projectId);
    	$addmembers['current_route'] = sfContext::getInstance()->getRouting()->getCurrentInternalUri(true);
    	$this->form = new SdProjectAddMemberForm($addmembers);
	
    	// If comming by POST
    	if ($request->getMethod() == sfRequest::POST)
    	{
      		$addmembers = $request->getParameter('addmembers', array());
			$this->form->bind($addmembers);
	
			if ($this->form->isValid())
			{
        		//check if the current user is authorized to add members
        		if($this->getUser()->getSdUser()->isProjectUser($projectId))
				{ 
					// Parsing the usernames and puts this in a array
					$usernames = str_replace(';', ',', $addmembers['usernames']);
					$usernameArray = explode(',', $usernames);
					for ( $i=0; $i < count($usernameArray); $i++ ) $usernameArray[$i] = trim($usernameArray[$i]);
					$usernameArray = array_unique($usernameArray);
          
					// Checking if users already exist in the project
					$projectUsers = $this->project->getProjectUsers();
          			$addResults = array();
          			
					foreach ( $usernameArray as $username )
					{
            			if( $username != '' )
						{
							$user_already_in_project = false;
            				foreach ( $projectUsers as $existingProjectUser )
							{
								if ( $username == $existingProjectUser->User->getUsername() )
								{
									$user_already_in_project 	= true;
									$addResults[$username] 		= false;
									break;
								}
							}
							
							if ( !$user_already_in_project ) $addResults[$username] = $username;
						}
					}
	
					$successArray = array();
					$failArray = array();
					foreach ( $addResults as $username => $project_user )
					{
						if ( $project_user==false )
						{
              				$failArray[] = $username;
						}
						else
						{
							$successArray[] = $username;
              
							$user = SdUserTable::retrieveByUsername($username, true);
						
			  				// Search Invitation previously sended (by Id or Email) 
							$invitation = SdInvitationTable::isUserInvited ( $this->project->getId(), $user->getId() );
			  				if ( !$invitation ) $invitation = SdInvitationTable::isUserInvited ( $this->project->getId(), $user->getEmail() );
			  							  				
			  				// If not found yet create a new Invitation
							if ( !$invitation ){
								$invitation = new SdInvitation();
								$invitation->setProjectId ( $this->project->getId() );
								$invitation->setInviterUserId ( $this->getUser()->getId() );
								$invitation->setInviteeUserId ( $user->getId() );
								$invitation->setInviteeEmail ( NULL );
								$invitation->setStatus(1); // Set status to SEND
								$invitation->setHashAuto();
								$invitation->save();	
							}
			  
							// Send to user an activation email with the confirmation link
							$this->getContext()->getConfiguration()->loadHelpers(array('Url'));
							$confirmationLink = url_for('@project_confirmuser?project_id='.$this->project->getId().'&key='.$invitation->getHash(), true);
			  				
							ProjectConfiguration::registerZend();
			  				$mail = new Zend_Mail();

			  				$bodyText = "{$user->getFullName()},\n\n{$this->getUser()->getFullName()} has invited you to join the {$this->project->getName()} project.\n\nPlease confirm by clicking the link below:\n\n{$confirmationLink}\n\n-The ScrumDog Team.";
	
							$mail->setBodyText($bodyText);
							$mail->setFrom('do-not-reply@scrumdog.com', 'ScrumDog Mail System');
							$mail->addTo($user->getEmail());
							$mail->setSubject("You've been invited to join the ".$this->project->getName()." project team on ScrumDog");
							$mailSent = EmailSender::send($mail);
		
							// If mail sending failed,
							if (!$mailSent)
							{
								//$request->setError('errors', 'We were unable to send the confirmation email.');
								//$confirmation->delete();
							}
						}
					}
					
					// Set flash messages
					if(!empty($failArray))
						$this->getUser()->setFlash('error', 'There was a problem adding the following members to the project: '.implode(', ', $failArray));
						
					if(!empty($successArray))
						$this->getUser()->setFlash('success', 'The following members were successfully added to the project: '.implode(', ', $successArray));
						
					//$this->redirect('@project_dashboard?project_id='.$projectId);
					$this->redirect($addmembers['current_route']);
				}
			}
			else $this->getUser()->setFlash('error', 'There is a problem with the data you entered into the form.');
		}
		$this->redirect('@project_members?project_id='.$projectId);
	}


	/**
	* Confirms a user
	*
	* @param  sfWebRequest $request
	*/
	public function executeConfirmUser(sfWebRequest $request)
	{
    
		$key = $this->getRequestParameter('key');
		$this->confirmation = SdInvitationTable::getByHash($key);

		// Check if Invitation exist
		if ( !$this->confirmation )
		{
			$this->getUser()->setFlash('error', 'Unable to find this confirmation key: '.$key);
			return sfView::ERROR;
		}
		
		// Search project
		$this->project = Doctrine::getTable ( 'SdProject' )->find ( $this->confirmation->getProjectID() );
		if ( !$this->project )
		{
			$this->getUser()->setFlash('error', 'Unable to find project.');
			return sfView::ERROR;
		}
		
		// Search invited user
		$this->user = Doctrine::getTable('SdUser')->find( $this->confirmation->getInviteeUserId() );
		if ( !$this->user )
		{
			$this->getUser()->setFlash('error', 'Unable to find user to confirm');
			return sfView::ERROR;
		}
    
		// Check if the invitation belong to current user
		if ( $this->user->getId() != $this->getUser()->getId() )
		{
			$this->getUser()->setFlash('error', 'The invitation does not belong to the current user');
			return sfView::ERROR;
		}
        
		// Check if the Invitation hass already processed
		if ($this->confirmation->getStatus() != 1)
		{
			$this->getUser()->setFlash('error', 'This confirmation has already been processed');
			return sfView::ERROR;
		}
		
		// Add invited user to the project
		$this->project->addUsername ( $this->user->getUsername() );
    
		// Change the status of Invitation to ACCEPTED, login user and set the flash messages
		$this->confirmation->setStatus(2);
		$this->confirmation->save();
		
		$this->getUser()->login($this->user);
		$this->getUser()->setFlash('success', 'You have been added to the '.$this->project->getName().' project and you are now logged in.');
		$this->redirect('@project_dashboard?project_id='.$this->project->getId());
	}

	/**
	* Confirms a user
	*
	* @param  sfWebRequest $request
	*/
	public function executeConfirmSimpleUser(sfWebRequest $request)
	{
		// Get invitation
		$project_id = $request->getParameter('project_id');
		$invitation = SdInvitationTable::getInvitationByInviteeAndProject ( $this->getUser()->getId(), $project_id );
		$status = $invitation->getStatus();
	
		// If invitation exist and status is SEND
		if ( $status == SdInvitationTable::SEND ){
			
			// Adding user invited to the project
			$project_user = new SdProjectUser();
			$project_user->setProjectId ( $project_id );
			$project_user->setUserId ( $this->getUser()->getId() );
			$project_user->setRole ( SdProjectUserTable::MEMBER );
			$project_user->save();
			
			// Set invitation status to ACCEPTED
			$invitation->setStatus(SdInvitationTable::ACCEPTED);
			$invitation->save();
	    	
	    	$project = Doctrine::getTable('SdProject')->find($project_id);
			$owner = $project->getOwner();
	
			// Send registrant an email
			$this->getContext()->getConfiguration()->loadHelpers(array('Url'));
			$confirmationLink = url_for('@project_members?project_id='.$project_id, true);
			
			ProjectConfiguration::registerZend();
			$mail = new Zend_Mail();
			
			$bodyText = "{$owner->getFullName()},\n\n{$this->getUser()->getFullName()} has accepted your invitation to join the {$project->getName()} project.\n\nYou can reach the project members page by clicking the link below:\n\n{$confirmationLink}\n\n-The ScrumDog Team";
			
			$mail->setBodyText($bodyText);
			$mail->setFrom('do-not-reply@scrumdog.com', 'ScrumDog Mail System');
			$mail->addTo($owner->getEmail());
			$mail->setSubject("{$this->getUser()->getFullName()} has accepted your invitation to join the {$project->getName()} project.");
			$mailSent = EmailSender::send($mail);
			
			// Set flash message
			$this->getUser()->setFlash('success', "You have successfully joined the project.", true);
	    }
	    $this->redirect('@project_dashboard?project_id='.$project_id);
  }


  public function executeRemoveMember(sfWebRequest $request)
  {
    $project_id = $request->getParameter('project_id');
	$project_user_id = $request->getParameter('project_user_id');

    $project_user = Doctrine::getTable('SdProjectUser')->find($project_user_id);
	
    // Check if user to remove belongs to the project 
	if(!$project_user)
	{
		$this->getUser()->setFlash('error', "The project user you were attempting to delete no longer exists.");
		$this->redirect('@project_members?project_id='.$project_id);
	}
	
	$project = Doctrine::getTable('SdProject')->find($project_id);
	$removed_user = Doctrine::getTable('SdUser')->find($project_user->getUserId());
	
	// Check if current user have permits to remove users
    if($this->getUser()->isProjectOwner($project_id)  && !$removed_user->isProjectOwner($project_id))
    {
		// Remove assignments to project tasks.
    	SdTaskTable::assignProjectTasks($project_id, $removed_user->getId(), NULL);
	   	$project_user->delete();

      	// Send removed person an email		
		ProjectConfiguration::registerZend();
		$mail = new Zend_Mail();
		
		$bodyText = "The project owner has removed you from the {$project->getName()} project.\n\n-The ScrumDog Team.";
		
		$mail->setBodyText($bodyText);
		$mail->setFrom('do-not-reply@scrumdog.com', 'ScrumDog Mail System');
		$mail->addTo($removed_user->getEmail());
		$mail->setSubject("You have been removed from the {$project->getName()} project.");
		$mailSent = EmailSender::send($mail);

      	$this->getUser()->setFlash('success', "You have successfully removed {$removed_user->getUsername()} from this project.", true);
    }
	elseif($this->getUser()->getId() == $removed_user->getId())
	{	
		// If removed himself
		
		// Remove assignments to project tasks.
		SdTaskTable::assignProjectTasks($project_id, $removed_user->getId(), NULL);
	   	$project_user->delete();

      	// Send project owner an email		
		ProjectConfiguration::registerZend();
		$mail = new Zend_Mail();
		
		$bodyText = "{$removed_user->getFullName()} has left the {$project->getName()} project.\n\n-The ScrumDog Team";
		
		$mail->setBodyText($bodyText);
		$mail->setFrom('do-not-reply@scrumdog.com', 'ScrumDog Mail System');
		$mail->addTo($project->getOwner()->getEmail());
		$mail->setSubject("Hasta la vista, {$removed_user->getUsername()}!");
		$mailSent = EmailSender::send($mail);

      	$this->getUser()->setFlash('success', "You have successfully removed yourself from the {$project->getName()} project.");
		$this->redirect('@member_dashboard');
	}
	$this->redirect('@project_members?project_id='.$project_id);
  }

	public function executeRemoveInvitation(sfWebRequest $request)
	{
		// Get invitation
		$project_id = $request->getParameter('project_id');
		$invitation_id = $request->getParameter('invitation_id');
    	$invitation = Doctrine::getTable ('SdInvitation')->find( $invitation_id );
	
    	// Get user name or email of the ivitation (for flash message)
    	if ( $invitation->getInviteeUserId() )
    		$removed_user = Doctrine::getTable('SdUser')->find( $invitation->getInviteeUserId() )->getUsername();	
	    else
    		$removed_user = $invitation->getInviteeEmail(); 
	
		// Check if exist invitation and if exist then delete this 
		if ( !$invitation )
		{
			$this->getUser()->setFlash('error', "The invitation you were attempting to delete no longer exists.");
			$this->redirect('@project_members?project_id='.$project_id);
		}
		else
		{
			$invitation->delete();
			$this->getUser()->setFlash('success', "You have successfully removed {$removed_user} invitation from this project.", true);
    	}
	
		$this->redirect('@project_members?project_id='.$project_id);
  	}
  
	public function executeRequestJoin(sfWebRequest $request)
 	{

	    $projectId = $request->getParameter('project_id');
	    $this->project = Doctrine::getTable('SdProject')->find($projectId);
		
	    // Check if project exist 
	    if ( !$this->project ){
			$request->setParameter('nav_scope', 'main');
			$this->forward('error', 'index');
		}
		
		// Check if user autenticated
	    if ( !$this->getUser()->isAuthenticated() ) {
	       $this->redirect('@homepage');
	    }
	
	    // Check user is member
	    $role = $this->getUser()->getProjectRole($projectId);
		if($role>0) {
	       return 'Member';
	    }
	    
	    // Get Invitation
	    $invitation = SdInvitationTable::getInvitationByInviteeAndProject( $this->getUser()->getId(), $projectId );
	
	    // Check if invitation status is Requested then go to Already page
	    if ( $invitation && ( (int)$invitation->getStatus() === SdInvitationTable::REQUESTED ) ){
	       return 'Already';
	    }
	
	    // Check if invitation status is Send then go to Invited page
	    if( $invitation && (int)$invitation->getStatus() === SdInvitationTable::SEND ){
	       return 'Invited';
	    }
	
	    // If comming for POST
	    if ( $request->getMethod() == sfRequest::POST ){
			
	    	// Check invitation. If not exist, then create a new invitation.
	    	if ( !$invitation ){
				$invitation = new SdInvitation();
				$invitation->setProjectId ( $projectId );
				$invitation->setInviterUserId ( NULL );
				$invitation->setInviteeUserId ( $this->getUser()->getId() );
				$invitation->setInviteeEmail ( NULL );
				$invitation->setStatus(0); // Set status to Requested
				$invitation->setHashAuto();
				$invitation->save();
	
				$owner = $this->project->getOwner();
	
				// Send user an  email to the project's owner
				$this->getContext()->getConfiguration()->loadHelpers(array('Url'));
				$confirmationLink = url_for('@project_members?project_id='.$projectId, true);
			
				ProjectConfiguration::registerZend();
				$mail = new Zend_Mail();
				
				$bodyText = "{$owner->getFullName()},\n\n{$this->getUser()->getFullName()} has requested to join the {$this->project->getName()} project.\n\nYou can confirm or deny his or her request by following the link below:\n\n{$confirmationLink}\n\n-The ScrumDog Team.";
				
				$mail->setBodyText($bodyText);
				$mail->setFrom('do-not-reply@scrumdog.com', 'ScrumDog Mail System');
				$mail->addTo($owner->getEmail());
				$mail->setSubject("Someone has requested to join the {$this->project->getName()} project team.");
				$mailSent = EmailSender::send($mail);
		
				return sfView::SUCCESS;
				
	       } else return 'Already';
	    }
		return 'Do';
	}

	public function executeAcceptMember(sfWebRequest $request)
	{
		$project_id = $request->getParameter('project_id');
		
		// Check if current user is project's owner
		if($this->getUser()->isProjectOwner($project_id)){
			
			// Get invitation and set to Accept status.
	  		$invitation_id = $request->getParameter ( 'invitation_id' );
	  		$invitation = Doctrine::getTable ( 'SdInvitation')->find($invitation_id );
	  		$invitation->setStatus ( SdInvitationTable::ACCEPTED );
	  		$invitation->save();
	  		
	  		// Add new user to the project
	  		$project_user = new SdProjectUser();
			$project_user->setProjectId ( $project_id );
			$project_user->setUserId ( $invitation->getInviteeUserId() );
			$project_user->setRole ( SdProjectUserTable::MEMBER );
			$project_user->save();
	  		
			$this->project = Doctrine::getTable('SdProject')->find ( $project_id );
			$this->registrant = Doctrine::getTable('SdUser')->find ( $invitation->getInviteeUserId() );
	
			// Send registrant an email to confirmation.
			$this->getContext()->getConfiguration()->loadHelpers(array('Url'));
			$confirmationLink = url_for('@project_dashboard?project_id='.$project_id, true);
			
			ProjectConfiguration::registerZend();
			$mail = new Zend_Mail();
			
			$bodyText = "{$this->registrant->getFullName()},\n\n{$this->getUser()->getFullName()} has accepted your request to join the {$this->project->getName()} project.\n\nYou can reach the project dashboard by clicking the link below:\n\n{$confirmationLink}\n\n-The ScrumDog Team.";
			
			$mail->setBodyText($bodyText);
			$mail->setFrom('do-not-reply@scrumdog.com', 'ScrumDog Mail System');
			$mail->addTo($this->registrant->getEmail());
			$mail->setSubject("You've been accepted into the ".$this->project->getName()." project");
			$mailSent = EmailSender::send($mail);
	
			$this->getUser()->setFlash('success', "You have successfully accepted {$this->registrant->getFullName()} into the project.");
	    }
    	$this->redirect('@project_members?project_id='.$project_id);
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
		sfConfig::set('sf_web_debug', false);
	}

	public function executeManage(sfWebRequest $request)
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
		//Change Project Name
		$this->form = new SdProjectForm($this->project);

		if ($request->getMethod() == sfRequest::POST)
    	{
			$projectArray = $request->getParameter('project');
			if(!is_null($projectArray))
			{
				$this->form->bind($projectArray);
				if ($this->form->isValid())
				{
					$this->form->save();
					$this->getUser()->setFlash('success', 'You have successfully updated the project name.', true);
					$this->redirect('@project_manage?project_id='.$this->project_id);
				}
			}

			$sprintArray = $request->getParameter('sprint');
			if(!is_null($sprintArray))
			{	
				foreach($sprintArray['id'] as $id)
				{
					$sprint = Doctrine::getTable('SdSprint')->find($id);
					if(isset($sprintArray['active']) && in_array($id, $sprintArray['active']))
						$sprint->setActive(1);
					else
						$sprint->setActive(0);
					if(isset($sprintArray['current']) && in_array($id, $sprintArray['current']))
						$sprint->setCurrent(1);
					else
						$sprint->setCurrent(0);
					$sprint->save();
				}
				
				$this->getUser()->setFlash('success', 'You have successfully updated the project sprints.', true);
				$this->redirect('@project_manage?project_id='.$this->project_id);
			}
		}
	}

  public function executeArchive(sfWebRequest $request)
  {
	$this->project_id = $request->getParameter('project_id');
	$this->project = Doctrine::getTable('SdProject')->findOneById($this->project_id);

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
	$this->filters = $this->getUser()->getSessionData('archiveFilters-'.$this->project_id);
	$this->sort = $this->getUser()->getSessionData('archiveSort-'.$this->project_id);
	$this->activeSprints = $this->project->getActiveSprints();
	$this->hasActiveSprints = count($this->activeSprints)>0;
  }	

	public function executeArchiveBody(sfWebRequest $request)
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
		sfConfig::set('sf_web_debug', false);
	}
	
	public function executeAutocomplete(sfWebRequest $request)
	{
		$user_id = $this->getUser()->getId();
		
		if ($user_id > 0){
			
			$this->setLayout(false);
			$this->getResponse()->setContentType('application/json');
						
			$users_array = array();
			
	      	$users =  Doctrine::getTable('SdUser')->searchUsers($request->getParameter('q'));
	      	
	      	foreach ($users as $user){
	      		if ($user_id != $user->getId())
	      			array_push( $users_array, array( 'name' => $user->getFullName(), 'username' => $user->getUsername() ) );
	      	} 

	      	$this->renderText(json_encode($users_array));
		}
		
       	return sfView::NONE;
	}
	
}
