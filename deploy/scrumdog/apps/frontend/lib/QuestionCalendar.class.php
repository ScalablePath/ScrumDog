<?php

class QuestionCalendar
{
	public $year;
	public $month;
	public $monthText;
	public $selectedDay = NULL;

	public $prevMonth;
	public $prevYear;

	public $nextMonth;
	public $nextYear;

	public $days = array();
	public $dayCount;

	private $startDate;
	private $numDaysInMonth, $numDaysInPrevMonth;
	private $monthArray = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	
	private $project_id;

	public function QuestionCalendar($user, $dateString, $selected_day = NULL)
	{
		$this->project_id = sfContext::getInstance()->getRequest()->getParameter('project_id');
		$this->user = $user;
		$monthArray = explode('-', $dateString);
		$this->year = $monthArray[0];
		$this->month = $monthArray[1];
		$this->monthText = $this->monthArray[(int)$this->month - 1];

		$this->selectedDay = $selected_day;

		//calculate previous month
		if($this->month=='1')
		{
			$this->prevYear = $this->year - 1;
			$this->prevMonth = 12;
		}
		else
		{
			$this->prevYear = $this->year;
			$this->prevMonth = $this->month - 1;
		}

		//calculate next month
		if($this->month=='12')
		{
			$this->nextYear = $this->year + 1;
			$this->nextMonth = 1;
		}
		else
		{
			$this->nextYear = $this->year;
			$this->nextMonth = $this->month + 1;
		}

		$this->numDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
		$this->numDaysInPrevMonth = cal_days_in_month(CAL_GREGORIAN, $this->prevMonth, $this->prevYear);

		$this->buildDayData();
	}


	private function buildDayData()
	{
		//determine the start date of the calendar
		$monthStartString = $this->year.'-'.$this->month.'-01';
		$monthStartTime = strtotime($monthStartString);

		$dayOfWeek = date('w', $monthStartTime);

		if($dayOfWeek==0)
		{
			$calendarStartYear = $this->year;
			$calendarStartMonth = $this->month;
			$calendarStartDay = '01';
		}
		else
		{
			$calendarStartYear = $this->prevYear;
			$calendarStartMonth = $this->prevMonth;
			$calendarStartDay = $this->numDaysInPrevMonth - $dayOfWeek + 1;
		}
		$calendarStartDay = $calendarStartYear.'-'.$calendarStartMonth.'-'.$calendarStartDay;
		$calendarStartTime = strtotime($calendarStartDay);
		
		//determine the end date of the calendar
		$monthEndTime = strtotime($this->year.'-'.$this->month.'-'.$this->numDaysInMonth);
		$dayOfWeek = date('w', $monthEndTime);

		if($dayOfWeek==6)
		{
			$calendarEndYear = $this->year;
			$calendarEndMonth = $this->month;
			$calendarEndDay = $this->numDaysInMonth;
		}
		else
		{
			$calendarEndYear = $this->nextYear;
			$calendarEndMonth = $this->nextMonth;
			$calendarEndDay = 6 - $dayOfWeek;
		}
		$calendarEndDay = $calendarEndYear.'-'.$calendarEndMonth.'-'.$calendarEndDay;
		$calendarEndTime = strtotime($calendarEndDay);
		
		//setup filters
		$filters = array();
		$filters['user_id'] = $this->user->getId();
		$filters['project_id'] = $this->project_id;
		$filters['startDate'] = $calendarStartDay;
		$filters['endDate'] = $calendarEndDay;

		//get the user's question data
		$questionCollection = Doctrine::getTable('SdQuestion')->getQuestions($filters);
		//prepare the question data for the calendar loop
		$questionsIndex = array();
		foreach($questionCollection as $question)
		{
			$questionsIndex[$question->getDate()] = $question;
		}

		//get the user's task hour data
		$taskHoursCollection = Doctrine::getTable('SdTaskHours')->getTaskHours($filters, array(), array('joinTasks' => true));
		//prepare the task hours data for the calendar loop
		$taskHoursIndex = array();
		foreach($taskHoursCollection as $taskHours)
		{
			$taskHoursIndex[$taskHours->getDate()][] = $taskHours;
		}

		//assemble the weeks
		$pointerTime = $calendarStartTime;
		while($pointerTime <= $calendarEndTime)
		{			
			//Daylight Muthafuckin' savings, biyatch!
			if(date('H', $pointerTime)=='23')
				$pointerTime = $pointerTime + 60*60;
			elseif(date('H', $pointerTime)=='01')
				$pointerTime = $pointerTime - 60*60;
				
			$day = new CalendarDay();
			$day->timestamp = $pointerTime;
			$dayString = date('Y-m-d',$pointerTime);

			
			if(date('m',$pointerTime) != $this->month)
				$day->outOfMonth = true;

			//look for questions
			if(isset($questionsIndex[$dayString]))
			{
				if(trim($questionsIndex[$dayString]->getWork())!='' || trim($questionsIndex[$dayString]->getObstacles())!='')
					$day->question = $questionsIndex[$dayString];
			}
				
			//look for task hours
			if(isset($taskHoursIndex[$dayString]))
			{
				$day->taskHours = $taskHoursIndex[$dayString];
			}

			//set the right class
			if($dayString == $this->selectedDay)
				$day->selected = true;
	
			$this->days[] = $day;
			$pointerTime = $pointerTime + 60*60*24;
		}		
		$this->dayCount = count($this->days);
	}
}
