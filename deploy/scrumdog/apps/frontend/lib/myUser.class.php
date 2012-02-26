<?php

class myUser extends sfBasicSecurityUser
{
	private $key = 'A9B8C7D6E5F!';

	/**
	* Login user function
	* @param object $user A object from the User class
	*/
	
	public function login(SdUser $user, $remember=false)
	{
		if($user->getId() && $user->getIsActive())
		{
			$this->setAuthenticated(true);
			if ($remember)
			{
				// determine a random key
				if (!$user->getRememberKey())
				{
					$rememberKey = Fluide_Symfony_Util::generateRandomKey(10,10);
					
					// save the key to the User table
					$user->setRememberKey($rememberKey);
				}
				
				// save the key to the cookie
				$value = base64_encode(serialize(array($user->getRememberKey(), $user->getId())));
				sfContext::getInstance()->getResponse()->setCookie('remember', $value, time()+60*60*24*30, '/');
			}

			$this->setAttribute('id', $user->getId());
			$this->setAttribute('username', $user['username']);
			$this->setAttribute('fullname', $user['full_name']);
			$this->setAttribute('email', $user['email']);

			// Update last login
			$ssDate = date('Y-m-d h:i:s'); 
			$user->setLastLogin($ssDate);
			// Save user record
			$user->save();
		}
	}

	/*
	* user logout function
	*/
	public function logout()
	{
		// Additional logic may happen here such as token cleanup or forcing another account's login
		$this->setAuthenticated(false);
		sfContext::getInstance()->getResponse()->setCookie('remember', $value, time()-60*60*24*30, '/');
	}

	public function getSdUser()
	{
		return Doctrine::getTable('SdUser')->find($this->getAttribute('id'));
	}

	public function getId()
	{
		return $this->getAttribute('id');
	}

	public function getUsername()
	{
		return $this->getAttribute('username');
	}

	public function getFullname()
	{
		return $this->getAttribute('fullname');
	}

	public function getEmail()
	{
		return $this->getAttribute('email');
	}

   public function getProjectRole($projectId)
   {
	 return $this->getSdUser()->getProjectRole($projectId);
   }

   public function isProjectOwner($projectId)
   {
   	 return $this->getSdUser()->isProjectOwner($projectId);
   }

   public function isProjectMember($projectId)
   { 		
	 return $this->getSdUser()->isProjectMember($projectId);
   }

	//refactored functions
	public function getSessionData($name)
	{
		$result = $this->getAttribute($name);
		if(is_null($result))
		{
			$nameArray = explode('-', $name);
			switch($nameArray[0])
			{
				case 'archiveSort':
					$result = array('id' => 'desc');
					break;
				case 'backlogSort':
					$result = array('business_value' => 'desc');
					break;
				case 'dashboardSort':
				case 'searchSort':
				case 'sprintSort':
					$result = array('priority' => 'desc');
					break;
				case 'dashboardFilters':
					$result = array('status' => 'not-completed');
					break;
				case 'searchFilters':
					$result = array('user_id' => $nameArray[2], 'status' => 'not-completed');
					break;
				case 'sprintFilters':
					$result = array('status' => 'not-accepted');
					break;
				case 'projectRoleArray':
					$result = $this->getProjectRoleArray();
					break;
				default:
					$result = array();
			}
		}
		return $result;
	}
	
	/*
	* @author Damien Filiatrault
	* @return nothing
	*/

	public function getProjectRoleArray()
	{
		$filters = array();
		$filters['user_id'] = $this->getId();
		$projectUsers = Doctrine::getTable('SdProjectUser')->getRecords($filters);
		
		$projectRoleArray = array();
		foreach($projectUsers as $projectUser)
		{
			$projectRoleArray[$projectUser['project_id']] = $projectUser['role'];
		}
		$this->setAttribute('projectRoleArray', $projectRoleArray);
	}
}
