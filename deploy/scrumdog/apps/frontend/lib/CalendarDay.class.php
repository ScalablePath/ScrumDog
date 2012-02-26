<?php

class CalendarDay
{
	public $class = NULL; //selected, data, out
	public $taskHours = array();
	public $question = NULL;
	public $timestamp;
	public $text;
	public $outOfMonth = false;
	public $selected = false;

	public function CalendarDay()
	{

	}

	public function getClass()
	{
		$classArray = array();
		if($this->outOfMonth)
			$classArray[] = "out";
		if(!is_null($this->question) || count($this->taskHours))
			$classArray[] = "data";
		if($this->selected)
			$classArray[] = "selected";

		return implode(' ', $classArray);
	}

	public function getInfo()
	{
		$ret = '';
		if(!is_null($this->question) || count($this->taskHours))
		{
			$ret .= '<p>';
			if(count($this->taskHours))
			{
				$ret .= '<strong>Task Hours:</strong>';
				$ret .= '<ul>';
				foreach($this->taskHours as $taskHours)
				{
					$ret .= '<li>'.$taskHours->getHours().' hours : '.$taskHours->Task->getName().'</li>';
				}
				$ret .= '</ul>';
			}
			if(!is_null($this->question))
			{
				$ret .= '<strong>Work Description:</strong><br />'.$this->question->getWork().'<br />';
				$ret .= '<strong>Obstacles:</strong><br />'.$this->question->getObstacles().'<br />';
			}
			$ret .= '</p>';
		}
		return $ret;
	}
}
