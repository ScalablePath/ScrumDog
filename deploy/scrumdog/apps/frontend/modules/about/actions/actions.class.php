<?php

/**
 * about actions.
 *
 * @package    scrumdog
 * @subpackage about
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class aboutActions extends sfActions
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
    if($this->isAuthenticated)
    {
      $this->username = $this->user->getUsername();
    }
  }

 /**
  * Executes howItWorks action
  *
  * @param sfRequest $request A request object
  */
  public function executeHowItWorks(sfWebRequest $request)
  {
    $this->user = $this->getUser();
    $this->isAuthenticated = $this->user->isAuthenticated();
    if($this->isAuthenticated)
    {
      $this->username = $this->user->getUsername();
    }
  }
}
