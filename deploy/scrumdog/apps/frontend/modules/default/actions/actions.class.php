<?php

/**
 * default actions.
 *
 * @package    scrumdog
 * @subpackage default
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class defaultActions extends sfActions {
	/**
	 * Executes index action
	 *
	 * @param sfRequest $request A request object
	 */
	public function executeIndex(sfWebRequest $request) {
		$this->user = $this->getUser ();
		$this->isAuthenticated = $this->user->isAuthenticated ();
		if ($this->isAuthenticated) {
			$this->username = $this->user->getUsername ();
		}
	}
	
	public function executeInviteMembers(sfWebRequest $request) {
		
		$projectId = $request->getParameter ( 'project_id' );
		$invitemembers ['current_route'] = sfContext::getInstance ()->getRouting ()->getCurrentInternalUri ( true );
		$this->form = new SdInviteMemberForm ( $invitemembers );
		
		// If comming by POST
		if ($request->getMethod () == sfRequest::POST) {
			
			$invitemembers = $request->getParameter ( 'invitemembers', array () );
			$this->form->bind ( $invitemembers );
			if ($this->form->isValid ()) {
				
				//check if the current user is authorized to add members
				if ($this->getUser ()->isAuthenticated ()) {
					
					// Parsing the field to emails
					$emails 	= str_replace ( ';', ',', $invitemembers ['emails'] );
					$emailArray = explode ( ',', $emails );
					for ($i = 0; $i < count ( $emailArray ); $i ++) 
						$emailArray [$i] = trim ( $emailArray [$i] );
					$emailArray = array_unique ( $emailArray );
					
					$addResults = array ();
					foreach ( $emailArray as $email ) {
						
						$invitation = NULL;
						
						// Check if email belong to current user 
						if ( $email != '' && $email != $this->getUser()->getEmail() ) {
							
							// If invite to a current project 
							if ($request->hasParameter ( 'invite-to-project' )) {
								$projectId = $request->getParameter ( 'invite-to-project' );
								
								// Check if the mail belongs to a user  
								$user =  SdUserTable::isUserEmail($email);
								if (!$user) {
									$user_exist = false;
									//echo $user->getEmail();
								} else {
									$user_exist = true;
								}
								
								// Check if the invitation has already been sent
								$invitation = SdInvitationTable::getInvitationByEmailAndProject ( $email, $projectId );
								if (!$invitation && $user_exist){
									$invitation = SdInvitationTable::getInvitationByInviteeAndProject ( $user->getId(), $projectId );
								}
								
								// Else generate a new invitation
								if (!$invitation){
									
									if ($user_exist && $user->isProjectMember( $request->getParameter ( 'invite-to-project' ) ) ){
										$this->getUser ()->setFlash ( 'notice', $user->getUsername().' ('.$user->getEmail().') already a member of this project' );
										continue;
									}									
									
									$invitation = new SdInvitation ( );
									$invitation->setProjectId ( $request->getParameter ( 'invite-to-project' ) );
									$invitation->setInviterUserId ( $this->getUser()->getId() );
									
									// Invitation to generate depending on whether the user exists 
									if (!$user_exist){
										$invitation->setInviteeUserId ( NULL );
										$invitation->setInviteeEmail ( $email );	
									}else{
										$invitation->setInviteeUserId ( $user->getId() );
										$invitation->setInviteeEmail ( NULL );
									}
									
									$invitation->setStatus ( 1 ); // Set status to SEND
									$invitation->setHashAuto ();
									$invitation->save ();
								}
							}
							
							$addResults [$email] = $this->sendInviteEmail ( $email, $invitation, $user );
						
						} else {
							$this->getUser ()->setFlash ( 'notice', 'You can\'t invite yourself' );
						}
					}

					
					$successArray = array ();
					$failArray = array ();
					foreach ( $addResults as $email => $value ) {
						if ($value == false) {
							$failArray [] = $email;
						} else {
							$successArray [] = $email;
						}
					}
					
					if (! empty ( $failArray )) {
						$this->getUser ()->setFlash ( 'error', 'There was a problem sending the following email addresses: ' . implode ( ', ', $failArray ) );
					}
					
					if (! empty ( $successArray )) {
						$this->getUser ()->setFlash ( 'success', 'The following email addresses were invited: ' . implode ( ', ', $successArray ) );
					}
					
					$this->redirect ( $invitemembers ['current_route'] );
				}
			} else {
				$this->getUser ()->setFlash ( 'error', 'There is a problem with the data you entered into the form.' );
			}
		
		}
	}
	
	private function sendInviteEmail($emailAddress, $invitation, $user) {
		$this->getContext()->getConfiguration()->loadHelpers( array ('Url' ) );
		
		ProjectConfiguration::registerZend ();
		$mail = new Zend_Mail ( );
		$mail->setFrom ( 'do-not-reply@scrumdog.com', 'ScrumDog Mail System' );
		$mail->addTo ( $emailAddress );
		
		if ( !$invitation ) {
			$mail->setSubject ( "You've been invited to join ScrumDog." );
			
			$confirmationLink = url_for ( '@homepage', true );
			
			$bodyText  = "{$emailAddress},\n\n";
			$bodyText .= "{$this->getUser()->getFullName()} has invited you to join ScrumDog.\n\n";
			$bodyText .= "Please check out the site and sign up by clicking the link below:\n\n";
			$bodyText .= "{$confirmationLink}\n\n";
			$bodyText .= "Thanks!\n\n";
			$bodyText .= "-The ScrumDog Team.\n\n";
			
			$mail->setBodyText ($bodyText);
		
		} elseif (!$user) {
			
			$project = Doctrine::getTable('SdProject')->find( $invitation->getProjectId() );
			$mail->setSubject("You've been invited to join the ".$project->getName()." project on ScrumDog");
			$confirmationLink = url_for ( '@user_register_key?key=' . $invitation->getHash(), true );
			
			$bodyText  = "{$emailAddress},\n\n";
			$bodyText .= "{$this->getUser()->getFullName()} has invited you to join the {$project->getName()} on ScrumDog.\n\n";
			$bodyText .= "Please check out the site and sign up by clicking the link below:\n\n";
			$bodyText .= "{$confirmationLink}\n\n";
			$bodyText .= "Thanks!\n\n";
			$bodyText .= "-The ScrumDog Team.";
			
			$mail->setBodyText ( $bodyText );
			
		} else {
			
			$project = Doctrine::getTable('SdProject')->find( $invitation->getProjectId() );
			$mail->setSubject("You've been invited to join the ".$project->getName()." project team on ScrumDog");
			$confirmationLink = url_for('@project_confirmuser?project_id='.$invitation->getProjectId().'&key='.$invitation->getHash(), true);
			
			$bodyText  = "{$user->getFullName()},\n\n";
			$bodyText .= "{$this->getUser()->getFullName()} has invited you to join the {$project->getName()} project.\n\n";
			$bodyText .= "Please confirm by clicking the link below:\n\n";
			$bodyText .= "{$confirmationLink}\n\n";
			$bodyText .= "-The ScrumDog Team.";
			
			$mail->setBodyText( $bodyText );
		}
		
		return EmailSender::send ( $mail );
	}

}
