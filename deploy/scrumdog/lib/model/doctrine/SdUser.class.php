<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SdUser extends BaseSdUser
{

  public function getProjectRole($projectId)
  {
	if($this->getId()==1)
		return SdProjectUserTable::OWNER;
		
	//Check if this is the current user and has the info in a session
	/* THIS DIDN'T WORK BECAUSE IT WAS TOO HARD TO KEEP SYNCHED
	$sessionUser = sfContext::getInstance()->getUser();
	if($this->getId()==$sessionUser->getId())
	{
		$projectRoleArray = $sessionUser->getSessionData('projectRoleArray');
		return (int) $projectRoleArray[$projectId];
	}
	*/

  	$project_user = Doctrine_Query::create()
				   ->select('pu.*')
				   ->from('SdProjectUser pu')
				   ->where('pu.user_id = ?', $this->getId())
				   ->andWhere('pu.project_id = ?', $projectId)
				   ->fetchOne();	

	if($project_user)
		return $project_user->getRole();
    else
      return false;
  }

  public function isProjectOwner($projectId)
  {
    $role = $this->getProjectRole($projectId);

    if($role==SdProjectUserTable::OWNER)
      return true;
    else
      return false;
  }

  public function isProjectMember($projectId)
  { 
	$role = $this->getProjectRole($projectId);

    if($role>SdProjectUserTable::INVITED)
      return true;
    else
      return false;
  }

  public function isProjectUser($projectId)
  {	
    $role = $this->getProjectRole($projectId);

    if($role)
      return true;
    else
      return false;
	 
  }
  
}