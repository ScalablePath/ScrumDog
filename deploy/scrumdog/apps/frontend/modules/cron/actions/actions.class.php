<?php

/**
 * about actions.
 *
 * @package    scrumdog
 * @subpackage cron
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class cronActions extends sfActions
{
	/**
	* Executes an application defined process prior to execution of this sfAction object.
	*
	* By default, this method is empty.
	*/
	public function preExecute()
	{
		if ($_SERVER['argc'] == 0)
		{
  			// Redirect to the homepage
  			header('Location: http://'.$_SERVER['SERVER_NAME']);
  			exit();
		}
		
		$this->setLayout(false);
		error_reporting(E_ALL ^ E_WARNING);
		sfConfig::set('sf_web_debug', false);
	}
	
	public function executeSendReminderEmails(sfWebRequest $request)
	{
		$conn = Doctrine_Manager::getInstance()->getCurrentConnection();
		$pdo = $conn->getDbh();

		//Figure out the target time zone we'd like to email
		$GMT_dtz = new DateTimeZone('GMT');
		$GMT_dt = new DateTime("now", $GMT_dtz);
		$hour_in_GMT = (int) $GMT_dt->format('H');
		$reminder_hour = sfConfig::get('app_reminder_hour');
		$temp_target_offset = $reminder_hour - $hour_in_GMT;

		if(abs($temp_target_offset)>11)
		{
			$target_offset = (12-(abs($temp_target_offset)%12)) * Fluide_Symfony_Util::sign($temp_target_offset) * -1;
		}
		else
		{
			$target_offset = $temp_target_offset;
		}

		//Figure out what date it is for the people we are sending out to
		$timeString_in_GMT = $GMT_dt->format('Y-m-d H:i:s');
		$timeStamp_in_GMT = strtotime($timeString_in_GMT);
		$timeStamp_in_Target = $timeStamp_in_GMT + ($target_offset * 60 * 60);

		//var_dump($target_offset); flush(); die();

		$stmt = $pdo->prepare("SELECT DISTINCT u.username, u.email, u.full_name, p.id AS project_id, p.name FROM `sd_sprint` s LEFT JOIN sd_project_user pu ON s.project_id=pu.project_id INNER JOIN sd_user u ON pu.user_id=u.id INNER JOIN sd_project p ON p.id=s.project_id WHERE s.active=1 and s.scrum_days LIKE '%".date('w', $timeStamp_in_Target)."%' AND pu.send_email=1 AND pu.role>0 AND u.time_zone_offset >= ".$target_offset." AND u.time_zone_offset < ".($target_offset + 1));
		//var_dump($stmt); flush(); die();
		$stmt->execute();
	
		$env = sfConfig::get('sf_environment');
		$subDomain = $env!='prod' ? $env : 'www';

		ProjectConfiguration::registerZend();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			sfLoader::loadHelpers(array('Url'));
			$dateString = date('Y-m-d', $timeStamp_in_Target);
			//$questionLink = url_for('@project_questions?project_id='.$row['project_id'].'&username='.$row['username'].'&date='.$dateString, true);

			$questionLink = 'http://'.$subDomain.'.scrumdog.com/project/'.$row['project_id'].'/questions/'.$row['username'].'/'.$dateString;
			$teamLink = 'http://'.$subDomain.'.scrumdog.com/project/'.$row['project_id'].'/members';

			$mail = new Zend_Mail();
			
			$mail->setBodyText(<<<EOF
{$row['full_name']},

Please answer your daily scrum questions for the {$row['name']} project by clicking the following link:

{$questionLink}

-The ScrumDog Team.

You are receiving this email because you a part of a project with an active sprint.  Who receives daily emails can be controlled on the project members page at {$teamLink}.

EOF
);

			$mail->setFrom('do-not-reply@scrumdog.com', 'ScrumDog Mail System');
			$mail->addTo($row['email']);
			$mail->setSubject("Answer your daily scrum questions for the {$row['name']} project.");
			$mailSent = EmailSender::send($mail);
		}

		echo("sent reminder emails\n");
		return true;
	}
}
