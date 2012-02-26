<?
/**
 * userActions
 *
 * @package    symfony
 * @subpackage plugin
 */
class userActions extends sfActions
{
   /**
   * Activates a user
   *
   * @param  sfWebRequest $request
   */
  public function executeActivate(sfWebRequest $request)
  {
    //this should be in the validateActivate() function but that wasn't working
	$key = $this->getRequestParameter('key');
	$this->confirmation = Doctrine::getTable('SdConfirmation')->findOneByHash($key);
    if (!$this->confirmation)
    {
      $this->getUser()->setFlash('error', 'Unable to find this confirmation key: '.$key, false);
      return sfView::ERROR;
    }

    //$confirmationAttributes = unserialize($this->confirmation->getAttributes());
	$this->user = Doctrine::getTable('SdUser')->find($this->confirmation->getForeignId());
    if (!$this->user)
    {
      $this->getUser()->setFlash('error', 'Unable to find user to activate', false);
      return sfView::ERROR;;
    }
    if ($this->user->getIsActive())
    {
      $this->getUser()->setFlash('error', 'This account has already been activated', false);
      return sfView::ERROR;
    }

	$this->user['is_active'] = 1;
    $this->user->save();
    $this->confirmation->delete();
    $this->getUser()->login($this->user);
    $this->getUser()->setFlash('success', 'Your account has been activated and you are now logged in. Please fill out the rest of your profile.');
    $this->redirect('@member_editprofile');
  }

  public function executeEditProfile(sfWebRequest $request)
  {
	$this->user = $this->getUser()->getSdUser();
    $this->form = new SdUserProfileForm($this->user);
    if($request->getMethod() != sfRequest::POST)
    {
		$imageFile = $this->user->getProfileImage();
		if(!is_null($imageFile->getId()))
		{
			$this->profileImageFile = $imageFile;
		}
		return sfView::SUCCESS;
    }

    $profile = $request->getParameter('profile', array());
    $this->form->bind($profile, $request->getFiles('profile'));
	
	if ($this->form->isValid())
    {
      try
      {  
		$validatedFileObject = $this->form->getValue('profile_image');
		if($validatedFileObject)
		{
			//delete the old file
			$oldFile = $this->user->getProfileImage();
			if(!is_null($oldFile->getId()))
			{
				$oldFile->delete();
			}
			$newFile = new SdFile();		
			$newFile->setFilename($validatedFileObject->getOriginalName());
			$saveResult = $newFile->save(); 
			$validatedFileObject->save($newFile->getFilePath());
			$this->user->setProfileImage($newFile);
		}
		$timeZone = $this->form->getValue('time_zone');
		$dateTimeZone = new DateTimeZone($timeZone);
		$dateTimeZoneUTC = new DateTimeZone('UTC');
		$dateTimeUTC = new DateTime("now", $dateTimeZoneUTC);
		$timeZoneOffset = timezone_offset_get($dateTimeZone, $dateTimeUTC)/(60*60);

		$timeZoneOffset = $timeZoneOffset==12 ? -12 : $timeZoneOffset;

		$this->user->setFullName($this->form->getValue('full_name'));
		$this->user->setPhone($this->form->getValue('phone'));
		$this->user->setCity($this->form->getValue('city'));
		$this->user->setState($this->form->getValue('state'));
		$this->user->setCountry($this->form->getValue('country'));
		$this->user->setGender($this->form->getValue('gender'));
		$this->user->setTimeZone($timeZone);
		$this->user->setTimeZoneOffset($timeZoneOffset);
		$this->user->save();		
		
        $this->getUser()->setFlash('success', 'Your profile has been updated');
        $this->redirect('@member_profile?username='.$this->user->getUsername());
      }
      catch (sfStopException $e)
      {
        throw $e;
      }
      catch (Exception $e)
      {
		//$this->getUser()->setFlash('error', 'Unable to save your profile');
		$this->getUser()->setFlash('error', $e);
        return sfView::SUCCESS;
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'There is a problem with the data you entered into the form.');
    }
  }

  public function executeProfile($request)
  {
    $this->user = SdUserTable::retrieveByUsername($this->getRequestParameter('username'));
	if(!$this->user)
	{
		$request->setParameter('nav_scope', 'main');
		$this->forward('error', 'index');
	}	
	$imageFile = $this->user->getProfileImage();
	if(!is_null($imageFile->getId()))
	{
		$this->profileImageSrc = $imageFile->getThumbnailSrc(200, 200, 'scale');
	}
	else
	{
		if($this->user->getGender()=='female')
			$this->profileImageSrc = "/images/avatar/female_200x200.gif";
		else
			$this->profileImageSrc = "/images/avatar/male_200x200.gif";
	}

    $this->is_current_user = $this->getUser()->getId() == $this->user->getId();
  }

	public function executeRegister($request)
	{
		if ($this->getUser()->isAuthenticated()){
			$this->forward('user', 'registerLoggedIn');
		}

		if ($request->hasParameter('key')){
    	
	    	$key = $request->getParameter('key');
	    	$this->invitation = SdInvitationTable::getByHash($key);
	    	
	    	if ($this->invitation && $this->invitation->getStatus() == SdInvitationTable::SEND)
	    		$this->email_to_registered = $this->invitation->getInviteeEmail();
	    	else 
	    		$this->forward('user', 'registerBadInvitationKey');
		}
		
		$this->form = new SdUserRegistrationForm();
	
		if ($request->isMethod('post'))
		{
			$user = $request->getParameter('user');

			$this->form->bind($user);
			if ($this->form->isValid())
			{
				sfLoader::loadHelpers('Url');

				$sdUser = new SdUser();
				$sdUser->setUsername($user['username']);
				$sdUser->setPassword($user['password']);
				$sdUser->setFullName($user['full_name']);
		        $sdUser->setEmail($user['email']);
				$ssDate = date('Y-m-d h:i:s'); 
				$sdUser->setLastLogin($ssDate);
		        $sdUser->save();
        
		        // Adding to new user at the project (if coming by invitation)
		        //$key = $request->getParameter('key');
		        //$this->invitation = SdInvitationTable::getByHash($key);
		        
		        if($this->invitation){
		        	$this->project = Doctrine::getTable ( 'SdProject' )->find ( $this->invitation->getProjectID() );
		        	$project_user = $this->project->addUsername ( $sdUser->getUsername() );
		        	$this->invitation->setStatus(2);
		        	$this->invitation->save();
		        }

		        // Create Email Confirmation entry
		        $confirmation = new SdConfirmation();
		        $confirmation->setType('user_activation');
				$confirmation->setForeignId($sdUser->getId());
		        //$attributes = array('user_id' => $sdUser->getId());
		        //$confirmation->setAttributes(serialize($attributes));
		        $confirmation->setHashAuto();
		        $confirmation->save();
		
		        // Send user an activation email
		        $mailSent = $this->sendActivationEmail($sdUser, $confirmation);
		
		        // If mail sending failed,
		        if (!$mailSent)
		        {
					$request->setError('errors', 'We were unable to send you an activation email. Registration process failed.');
					$activation->delete();
					$user->delete();
					return sfView::SUCCESS;
		        }
				$this->getUser()->setFlash('success', 'An activation email has been sent to '.$sdUser->getEmail());
				$this->redirect(url_for('@user_register_thankyou').'?'.http_build_query($this->form->getValues()));
			}
			else
			{
				/*
		        //this didn't work, unfortunately
		        if(!is_null($request->getParameter('home_form')))
		        {
		          $this->forward('default', 'index');
		        }
		        */
			}
		}
	}

	public function executeRegisterBadInvitationKey()
	{
	}
  
	public function executeRegisterLoggedIn()
	{
	}

	public function executeRegisterThankYou()
	{
	}

	public function executeDashboard(sfWebRequest $request)
	{
		$this->user = $this->getUser()->getSdUser();
		//backlog stuff
		$this->userProjects = SdProjectTable::getProjectsByUser($this->user);
		$this->filters = $this->getUser()->getSessionData('dashboardFilters');
		$this->sort = $this->getUser()->getSessionData('dashboardSort');
	}

	public function executeResendActivation(sfWebRequest $request)
	{
		
		$resend = $request->getParameter('resend');
	  
		if($request->getMethod() == sfRequest::POST)
		{
			$this->form = new SdResendActivationForm();
			$this->form->bind($resend);
			if($this->form->isValid())
			{
				$username = $resend['username'];
				$this->user = SdUserTable::retrieveByUsername($username);
//echo($this->user->getId()); die();
		
				if(isset($this->user) && $this->user == "")
				{
					$this->getUser()->setFlash('error', 'The username you entered does not exist.', false);
					return sfView::ERROR;
				}
				elseif($this->user->getIsActive())
				{
					$this->getUser()->setFlash('notice', 'The username you entered has already been activated.', false);
					return sfView::ERROR;
				}
				else
				{
					$this->confirmation = SdConfirmationTable::retrieveActivationByUserId($this->user->getId());
//echo($this->confirmation->getId()); die();
					if(is_null($this->confirmation))
					{
						$this->getUser()->setFlash('notice', 'The username you entered has already been activated.', false);
						return sfView::ERROR;
					}
					else
					{
						$this->sendActivationEmail($this->user, $this->confirmation);
						$this->getUser()->setFlash('success', 'The activation email for '.$username.' has been resent.', false);
						return sfView::SUCCESS;
					}
				}
			}
			else
			{
				$this->getUser()->setFlash('error', 'There is a problem with the data you entered into the form.');
				return sfView::SUCCESS;
			}
		}
		else
		{
			$tempUser = new SdUser();
			$tempUser->setUsername($resend['username']);
			$this->form = new SdResendActivationForm($tempUser);
			return sfView::SUCCESS;
		}		
	}

	private function sendActivationEmail($sdUser, $confirmation)
	{
		sfLoader::loadHelpers('Url');
        $activationLink = url_for('@user_activate?key='.$confirmation->getHash(), true);

        ProjectConfiguration::registerZend();
        $mail = new Zend_Mail();

//$betaBodyText = "{$sdUser->getFullName()},\n\nThanks for joining ScrumDog!\n\nCurrently, new registrations must be manually confirmed by a ScrumDog administrator.  If you would like your account to be activated, please send an email to support@scrumdog.com.\n\nWe appreciate your interest in ScrumDog.\n\n-The ScrumDog Team.";
$bodyText = "{$sdUser->getFullName()},\n\nThanks for joining ScrumDog!\n\nPlease activate your ScrumDog account by clicking the link below:\n\n{$activationLink}\n\n-The ScrumDog Team.";

		//This is temporary while in beta period
        $mail->setBodyText($bodyText);

        $mail->setFrom('do-not-reply@scrumdog.com', 'ScrumDog Mail System');
        $mail->addTo($sdUser->getEmail());
        $mail->setSubject('ScrumDog Account Activation');
        $mailSent = EmailSender::send($mail);

//temporary
$mail = new Zend_Mail();
$adminBodyText = "{$sdUser->getFullName()} just joined ScrumDog!\n
username = {$sdUser->getUsername()}
email = {$sdUser->getEmail()}\n
If you want to activate their ScrumDog account, click the link below:\n\n{$activationLink}\n\n-The ScrumDog Team.";
$supportEmail = sfConfig::get('app_support_email');
$mail->setBodyText($adminBodyText);
$mail->setFrom('do-not-reply@scrumdog.com', 'ScrumDog Mail System');
$mail->addTo($supportEmail);
$mail->setSubject('New ScrumDog Account Registration');
$mailSent = EmailSender::send($mail);

		return $mailSent;
	}
  
  //this should probably be moved to part of the edit profile form
  public function executeDeleteProfileImage(sfWebRequest $request)
  {
  	$snFileId = $request->getParameter('id');
	$oUser = Doctrine::getTable('SdUser')->find($this->getUser()->getId());
	$ssFilename = $oUser->getProfileImage()->getFilename();
	$snRows = $oUser->ProfileImage->delete();
	if($snRows)
		$oUser->getProfileImage()->DeleteProfileImage($snFileId, $ssFilename);
	$oUser->setProfileImage(NULL);
	$oUser->save();
	
	$this->redirect('@member_editprofile');
  }

	public function executeBacklogBody(sfWebRequest $request)
	{
		$this->user = $this->getUser()->getSdUser();
		sfConfig::set('sf_web_debug', false);
	}
	
	public function executeHours(sfWebRequest $request)
	{
		$this->startDate = $this->getRequestParameter('start_date');
		$this->endDate = $this->getRequestParameter('end_date');
		$this->projectsArray = $this->getRequestParameter('projects');
		$this->projects = SdProjectTable::getProjectsByUser($this->getUser());
	
		if(is_null($this->startDate))
			$this->csvLink = $_SERVER["REQUEST_URI"].'?csv=true';
		else
			$this->csvLink = $_SERVER["REQUEST_URI"].'&csv=true';
		
		if(is_null($this->projectsArray))
		{
			$this->projectsArray = array();
			//if this is the first time the user is visiting the page then select all users
			if(is_null($this->startDate))
			{	
				foreach($this->projects as $project)
				{
					$this->projectsArray[] = $project->getId();
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
		$filters['user_id'] = $this->getUser()->getId();
		$filters['hours'] = '>0';
		$filters['projects'] = $this->projectsArray;
		
		$sort = array();
		$sort['date'] = 'asc';
		$sort['user_id'] = 'asc';
		
		$options = array();
		$options['joinProjects'] = true;

		$this->questions = Doctrine::getTable('SdQuestion')->getQuestions($filters, $sort, $options);
		
		$this->rowCount = count($this->questions);
		
		$csv = $this->getRequestParameter('csv');
		
		if($csv=='true')
		{
			$filename = 'user_'.$this->getUser()->getId().'_'.date('m-j-Y', strtotime($this->startDate)).'_to_'.date('m-j-Y', strtotime($this->endDate)).'_hours.csv';
			sfConfig::set('sf_web_debug', false);
			
			$response = $this->getResponse();
			$response->setContentType('text/csv');
		    $response->setHttpHeader('Content-Disposition', "attachment; filename=".$filename);
		    $response->addCacheControlHttpHeader('no-cache');
		    
			return 'CSV';
		}
	}
}
