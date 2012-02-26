<?php

class MySessionStorage extends sfSessionStorage
{
  public function initialize($options = null)
  {
	$context = sfContext::getInstance();
    //Shitty work-around for swfuploader
    if( $context->getActionName() == "fileUpload")
    { 
      $sessionName = $options["session_name"];

      if($value = $context->getRequest()->getParameter($sessionName))
      {
        session_name($sessionName);
        session_id($value);
      } 
    }
    
    parent::initialize($options);
  }
}
