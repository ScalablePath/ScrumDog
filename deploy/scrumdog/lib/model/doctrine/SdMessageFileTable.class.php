<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SdMessageFileTable extends Doctrine_Table
{
	public static function getMessageFiles($messageId)
	{
		$files = Doctrine_Query::create() 
						  ->select('f.*, mf.message_id')
						  ->from('SdMessageFile mf')
						  ->innerJoin('mf.File f')
						  ->where('mf.message_id = ?', $messageId)
						  ->execute();
		
		return $files;
	}
}