<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SdMessageFile extends BaseSdMessageFile
{
	public function postInsert($event)
	{
		$newValue = '<a href="'.$this->File->getSrc().'">'.$this->File->getFilename().'</a>';
		$this->Message->saveMessageHistory('file add', '', $newValue, NULL, $this->File->getId());
	}
	
	public function postDelete($event)
	{
		$oldValue = '<a href="'.$this->File->getSrc().'">'.$this->File->getFilename().'</a>';
		$this->Message->saveMessageHistory('file delete', $oldValue, '', NULL, $this->File->getId());
	}
}