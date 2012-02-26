<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SdMessageCommentTable extends Doctrine_Table
{
	public static function getMessageComments($messageId)
	{
		$messageComments = Doctrine_Query::create() 
						  ->select('u.full_name, mc.comment, mc.created_at, f.id')
						  ->from('SdMessageComment mc')
						  ->innerJoin('mc.User u')
							->leftJoin('u.ProfileImage f')
						  ->where('mc.message_id = ?', $messageId)
						  ->execute();
		
		return $messageComments;
	}
}