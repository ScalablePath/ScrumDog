<?php decorate_with(false);
$result = 'Date,Project,Hours,Summary'."\n";
$i=0;
$questionRaw = $sf_data->getRaw('questions');
foreach($questionRaw as $question)
{
	$result .=  '"'.date('m/d/Y', strtotime($question->getDate())).'",';
	$value = str_replace('"', '""', $question->Project->getName());
	$result .=  '"'.$value.'",';
	$result .=  '"'.$question->getHours().'",';

	$taskHours = $question->getTaskHours();
	$taskHourCount = count($taskHours);
	$i=0;
	
	$summary = '';
	if($taskHourCount>0)
	{
		$summary .= 'Tasks: ';
		foreach($taskHours as $taskHour)
		{
			$i++;
			$value = str_replace('"', '""', $taskHour->Task->getName());
			$summary .= $value;
			$summary .= ' #'.$taskHour->Task->getId();
			$summary .= ' ('.$taskHour->getHours();
			if($taskHour->getHours()-1!=0)
				$summary .= 'hrs)';
			else
				$summary .= 'hr)';
			if($i!=$taskHourCount)
				$summary .= ', ';		
		}
	}
		
	if(trim($question->getWork())!='')
	{
		$value = str_replace(array("\r", "\n"), ' ', $question->getWork());
		$value = str_replace('"', '""', $value);
		$summary .= ' Other Work: '.$value;
	}
	
	if(trim($question->getObstacles())!='')
	{
		$value = str_replace(array("\r", "\n"), ' ', $question->getObstacles());
		$value = str_replace('"', '""', $value);
		$summary .= ' Obstacles: '.$value;
	}
	
	$result .= '"'.$summary.'"'."\n";
}
echo($result);