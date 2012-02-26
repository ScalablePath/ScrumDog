<?php

/**
 * terms actions.
 *
 * @package    scrumdog
 * @subpackage terms
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class termsActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->user = $this->getUser();
    $this->isAuthenticated = $this->user->isAuthenticated();
    if(!$this->isAuthenticated)
    {
      $this->form = new SdUserRegistrationForm();
    }
    else
    {
      $this->username = $this->user->getUsername();
    }
  }
}
