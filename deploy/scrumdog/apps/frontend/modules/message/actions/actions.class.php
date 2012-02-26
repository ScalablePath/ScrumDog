<?php

/**
 * message actions.
 *
 * @package    scrumdog
 * @subpackage message
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class messageActions extends sfActions
{
	/**
	* Executes index action
	*
	* @param sfRequest $request A request object
	*/
	public function executeIndex(sfWebRequest $request)
	{
		$filters = array();
		$filters['project_id'] = $this->project_id;
		
		$sort = array();
		$sort['created_at'] = 'desc';
		
		$options = array();
		$options['joinComments'] = true;
		$options['joinFiles'] = true;
		
		$this->messages = SdMessageTable::get($filters, $sort, $options);
	}
	
	public function executeCreate(sfWebRequest $request)
	{   
		$this->form = new SdMessageForm();
		
		if ($request->getMethod() == sfRequest::POST)
		{
			$message = $request->getParameter('message', array());
			$this->form->bind($message);
			
			if ($this->getUser()->isProjectMember($this->project_id) && $this->form->isValid())
			{
				try
				{
					//create the message
					$newMessage = new SdMessage();
					$newMessage->setProjectId($this->project_id);
					$newMessage->setUserId($this->getUser()->getId());
					$newMessage->setTitle($message['title']);
					$newMessage->setContent($message['content']);
								
					$newMessage->save();
					
					$this->getUser()->setFlash('success', 'Your message has been posted.');
					
					//Add Files to message
					foreach ($_FILES as $file_to_upload){
				
						$fileName = $file_to_upload['name'];
						$file = new SdFile();
						$file->setFilename($fileName);
						$file->save();
						
						//save the physical file
						$directoryPath = $file->getFileDirectoryPath(true);
						if(!file_exists($directoryPath))
							mkdir($directoryPath, 0755, true);
				
						if(move_uploaded_file($file_to_upload['tmp_name'], $file->getFilePath())){
							
							$messageId = $newMessage->getId();
							//create the MessageFile
							$messageFile = new SdMessageFile();
							$messageFile->setFileId($file->getId());
							$messageFile->setMessageId($messageId);
							$messageFile->save();
				
						} else {
							$file->delete();
						}
					}
					
					//TODO: Send emails to appropriate users
					$filters = array();
					$filters['project_id'] = $this->project_id;
					$filters['send_email'] = 1;
					
					$sort = array();
					
					$options = array();
					$options['joinUsers'] = true;
					
					$projectUsers = SdProjectUserTable::getRecords($filters, $sort, $options);
					
					$env = sfConfig::get('sf_environment');
					$subDomain = $env!='prod' ? $env : 'www';
					
					$messageLink = 'http://'.$subDomain.'.scrumdog.com/message/'.$newMessage->getId();
					$teamLink = 'http://'.$subDomain.'.scrumdog.com/project/'.$this->project_id.'/members';
					
					$creatorName = $this->getUser()->getFullName();
					$projectName = $this->project->getName();
					ProjectConfiguration::registerZend();
					
					foreach($projectUsers as $projectUser)
					{
						$mail = new Zend_Mail();
						
						$mail->setBodyText(<<<EOF
{$projectUser->User->getFullName()},

{$creatorName} has created a new message in the {$projectName} project.

Title: {$newMessage->getTitle()}
Message:
{$newMessage->getContent()}

You can view the message by clicking the link below:

{$messageLink}

-The ScrumDog Team.

You are receiving this email because you a part of a project with an active sprint.  The project owner can manage who receives emails on the project team members page at {$teamLink}.

EOF
);
			
						$mail->setFrom('do-not-reply@scrumdog.com', 'ScrumDog Mail System');
						$mail->addTo($projectUser->User->getEmail());
						$mail->setSubject("New message in the {$projectName} project: {$newMessage->getTitle()}");
						$mailSent = EmailSender::send($mail);
					}
					
					$this->redirect('@project_message_view?message_id='.$newMessage->getId());
				}
				catch (sfStopException $e)
				{
					throw $e;
				}
				
				catch (Exception $e)
				{
					//$this->getUser()->setFlash('error', 'Unable to create your message');
					$this->getUser()->setFlash('error', $e);
					return sfView::SUCCESS;
				}
			}
			else
			{
			$this->getUser()->setFlash('error', 'There is a problem with the data you entered into the form.', false);
			}
		}	
	}
	
	public function executeView(sfWebRequest $request)
	{
		$this->message_id = $this->getRequestParameter('message_id');
		$this->message = Doctrine::getTable('SdMessage')->find($this->message_id);
		if(!$this->message)
		{
			$request->setParameter('nav_scope', 'main');
			$this->forward('error', 'index');
		}
		if(!$this->getUser()->isProjectMember($this->project_id))
		{
			
		  $this->getUser()->setFlash('error', 'You are not a member of this project.');
		  $this->redirect('@project_requestjoin?project_id='.$this->project_id);
		}
		$this->projectUsers = SdProjectTable::getProjectUserArray($this->project_id, NULL, array('objects' => true));
	}
	
	public function executeAjaxSave(sfWebRequest $request)
  	{
		$messageArray = $request->getParameter('message', array());
	    $this->message = Doctrine::getTable('SdMessage')->find($messageArray['id']);
		$project_id = $this->message->getProjectId();
		
		//fail by default
		$this->output = '{"status": "error", "id": "'.$messageArray['id'].'"}';
		
		if($this->getUser()->getId()==$this->message->User->getId() || $this->getUser()->getId()==1)
		{
			if ($request->getMethod() == sfRequest::POST  && $this->getUser()->isProjectMember($project_id))
		    {
				$recordHistory = true;
				
				if(isset($messageArray['title']))
					$this->message->setTitle($messageArray['title']);
					
				if(isset($messageArray['content']))
					$this->message->setContent($messageArray['content']);
	
				if(isset($messageArray['is_archived']))
				{
					$this->message->setIsArchived($messageArray['is_archived']);
				}
					
				$this->message->save(null, $recordHistory);
			}
		}
	
		$this->getResponse()->setHttpHeader('Content-type', 'application/json');
	}


	public function executeFileUpload(sfWebRequest $request)
    {
		sfConfig::set('sf_web_debug', false);
		//Fluide_Util_Debug::shoutLog('test', 'test.txt');
		
		$end = true;
		
		$fileName = $_FILES ['Filedata'] ['name'];
		$file = new SdFile();
		$file->setFilename($fileName);
		$file->save();
		
		//save the physical file
		$directoryPath = $file->getFileDirectoryPath(true);
		if(!file_exists($directoryPath))
			mkdir($directoryPath, 0755, true);

		if(move_uploaded_file( $_FILES ['Filedata'] ['tmp_name'], $file->getFilePath() )){
			
			$messageId = $this->getRequestParameter('message_id');
			//create the MessageFile
			$messageFile = new SdMessageFile();
			$messageFile->setFileId($file->getId());
			$messageFile->setMessageId($messageId);
			$messageFile->save();

		} else {
			$file->delete();
			$end = false;				
		}
		
		if ($end) 
			return sfView::SUCCESS; 
		else 
			return sfView::ERROR; 
	}


	public function executeFileList(sfWebRequest $request)
    {
		
	}
	
	public function executeHistory(sfWebRequest $request)
    {
		
	}
	
	public function executeAjaxCommentSave(sfWebRequest $request)
    {
		$messageCommentArray = $request->getParameter('message', array());
		$messageObject = Doctrine::getTable('SdMessage')->find($request->getParameter('message_id'));
		$this->messageId = $request->getParameter('message_id');

		$project_id = $messageObject->getProjectId();
		$this->project = Doctrine::getTable('SdProject')->find($project_id);
		
		if ($request->getMethod() == sfRequest::POST  && $this->getUser()->isProjectMember($project_id))
		{
			$messageComment = new SdMessageComment();

			if(isset($messageCommentArray['comment']))
			{
				$messageComment->setMessageId($this->messageId); 
				$messageComment->setUserId($this->getUser()->getAttribute('id'));
				$messageComment->setComment($messageCommentArray['comment']); 
				$messageComment->save();
			}
			//save the comment in the message history
			$messageObject->saveMessageHistory('comment', '', $messageCommentArray['comment'], NULL, $messageComment->getId());

			//send an email
			if($messageObject->getUser()->getId()!=$this->getUser()->getId() && is_int($messageObject->getUser()->getId()))
			{
				ProjectConfiguration::registerZend();
				$mail = new Zend_Mail();
				sfLoader::loadHelpers(array('Url'));
				$emailLink = url_for('@project_message_view?message_id='.$this->messageId, true);
	
				$mail->setSubject("{$this->project->getName()} - New Comment on {$messageObject->getTitle()}");
				$bodyText = "{$messageObject->getUser()->getFullName()},\n\n".$this->getUser()->getFullName()." has commented on this message.\n\n";
				$bodyText .= "Comment:\n\n{$messageCommentArray['comment']}\n\n";
				$bodyText .= "You can view the message by clicking the link below:\n\n{$emailLink}\n\n-The ScrumDog Team.";
				
				$mail->setBodyText($bodyText);
				$mail->setFrom('do-not-reply@scrumdog.com', 'ScrumDog Mail System');
				$mail->addTo($messageObject->getUser()->getEmail());
//var_dump($mail); flush(); die();
				$mailSent = EmailSender::send($mail);
			}
			return sfView::SUCCESS;
		}
		else
		{
			$this->output = '{"status": "error", "html": ""}';
			return sfView::ERROR;
		}
	}
	
	public function preExecute()
	{
		$this->project_id = $this->getRequestParameter('project_id');
		$this->project = Doctrine::getTable('SdProject')->find($this->project_id);
		$exemptActions = array('fileUpload');
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
	
	public function executeAjaxFileDelete(sfWebRequest $request)
	{	
		$this->id = $request->getParameter('messagefile_id');
		try
		{
			$messageFile = Doctrine::getTable('SdMessageFile')->find($this->id);
			$messageFile->delete();
			$this->status = "success";
			
			$this->message = "MessageFile successfully deleted";
		}
		catch(Exception $e)
		{
			$this->status = "error";
			$this->message = $e->getMessage();
		}
		
	}
}
