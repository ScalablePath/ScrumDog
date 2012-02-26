<?php
class messageComponents extends sfComponents
{
  public function executeComment($request)
  {
		$this->messageId = $this->getRequestParameter('message_id');
		$this->comments = SdMessageCommentTable::getMessageComments($this->messageId);
  }
  
  public function executeHistory()
  {
		$messageId = $this->getRequestParameter('message_id');
			
		$query = Doctrine_Query::create()
							->select('u.full_name, mh.created_at, mh.change_type, mh.previous_value, mh.new_value')
							->from('SdMessageHistory mh')
							->innerJoin('mh.User u')
							->where('mh.message_id ='.$messageId)
							->orderby('mh.created_at DESC');
		
		$this->histories = $query->execute();
		$this->historyTotal = count($this->histories->getData());
  }
}