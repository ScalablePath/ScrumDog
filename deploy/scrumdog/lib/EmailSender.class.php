<?php
/**
 * Class EmailSender
 * 
 * @author Damien Filiatrault <damien626@gmail.com>
 */
class EmailSender {
    
  	public static function send($emailObject)
	{
		$className = get_class($emailObject);
		$env = sfConfig::get('sf_environment');

		if($className=='Zend_Mail')
		{
			if($env!='prod')
			{				
				//Clean out the "to" field
				$recipientsArray = $emailObject->getRecipients();

				$cleanedRecipientsArray = array();
				foreach($recipientsArray as $recipientEmail)
				{	
					if(!self::isPermitted($recipientEmail))
						return true; //doing this so the app is testable outside of prod
				}

				/*
				$mail->setBodyText($emailObject->getBodyText());
				$mail->setFrom($emailObject->getFrom());
				$mail->setSubject($emailObject->getSubject());
				return $mail->send();
				*/
			}
	
			return $emailObject->send();
			return true;
		}
	}

	public static function isPermitted($emailAddress)
	{
		$permittedEmailsArray = sfConfig::get('app_permitted_emails');

		foreach($permittedEmailsArray as $permittedEmail)
		{
			if($emailAddress==$permittedEmail)
				return true;
		}

		$domainsArray = sfConfig::get('app_permitted_email_domains');
		$emailArray = explode('@', $emailAddress);
		$emailDomain = $emailArray[1];
		foreach($domainsArray as $permittedDomain)
		{
			if($emailDomain==$permittedDomain)
				return true;
		}

		return false;
	}
}
