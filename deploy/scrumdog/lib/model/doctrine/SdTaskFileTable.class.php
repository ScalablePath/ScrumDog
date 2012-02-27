<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SdTaskFileTable extends Doctrine_Table
{
	public static function getTaskFiles($taskId)
	{
		$taskFiles = Doctrine_Query::create() 
						  ->select('f.*, tf.task_id')
						  ->from('SdTaskFile tf')
						  ->innerJoin('tf.File f')
						  ->where('tf.task_id = ?', $taskId)
						  ->execute();
		
		return $taskFiles;
	}
}