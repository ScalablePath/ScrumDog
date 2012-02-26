<?php

class errorActions extends sfActions
{  
  
  /**
   * Executes index action
   *
   */ 
  public function executeIndex($request){
    return sfView::SUCCESS;
  } 
}

?>