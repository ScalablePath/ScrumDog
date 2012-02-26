<?
/**
 * authActions
 *
 * @package    symfony
 * @subpackage plugin
 */
class authActions extends sfActions
{
	public function executePreSignin($request)
	{
		sfLoader::loadHelpers(array('Url'));
		$this->redirect(url_for('@user_signin?redirect='.urlencode($_SERVER['REQUEST_URI'])));
	}

	public function executeSignin($request)
	{
		if($this->getUser()->isAuthenticated())
		{
			$this->username = $this->getUser()->getUsername();
			return 'LoggedIn';
		}
		
		$this->renderRedirectInput = false;
		if($request->getParameter('redirect'))
		{
			$this->renderRedirectInput = true;
			$this->redirectUrl = $request->getParameter('redirect');
		}
		$this->form = new SdLoginForm();
		//$this->regForm = new SdUserRegistrationForm();
		
		if ($request->isMethod('post'))
		{
			$user = $request->getParameter('user');
			$this->form->bind($user);
			if ($this->form->isValid())
			{
				//check the ability to log in
				$sdUser = Doctrine::getTable('SdUser')->findOneByUsername($user['username']);
				//var_dump($sdUser); exit;
				
				if(isset($sdUser) && $sdUser == "")
				{
					$this->getUser()->setFlash('error', 'The username you entered does not exist.', false);
					return;
				}
				elseif($sdUser->getIsActive()!=1)
				{
					$this->username = $user['username'];
					return 'NotActive';
				}
				elseif($sdUser->getPassword() == $user['password'])
				{
					$remember = isset($user['remember']);
					$this->getUser()->login($sdUser, $remember);
					$this->redirect($this->getLoginRedirect($request));
				}
				else
				{
					$this->getUser()->setFlash('error', 'The password your entered is incorrect.', false);
				}
			}
		}
	}

	public function executeSignout(sfWebRequest $request)
	{
		$this->getUser()->logout();
	}

	public function executeForgotPassword(sfWebRequest $request)
	{
		if ($request->isMethod('post'))
		{
			$user = $request->getParameter('user');
			$userString = $user['username'];
			
			//if its a valid email, we know it's not a username
			$isEmail = false;
			if(Fluide_Symfony_Util::isValidEmail($userString))
			{
				$sdUser = Doctrine::getTable('SdUser')->findOneByEmail($userString);
				if(is_null($sdUser))
				{
					$this->getUser()->setFlash('error', 'The email address you entered was not found in our system.', false);
				}
				else
				{
					$this->getUser()->setFlash('success', 'Your login information has been emailed to '.$userString.'.', false);
					$this->sendLoginInfo($sdUser);
					return 'Found';
				}
			}
			else
			{
				$sdUser = Doctrine::getTable('SdUser')->findOneByUsername($userString);
				if(is_null($sdUser))
				{
					$this->getUser()->setFlash('error', 'The username you entered was not found in our system.', false);
				}
				else
				{
					$this->getUser()->setFlash('success', 'Your login information has been sent to the email address on file for '.$userString.'.', false);
					$this->sendLoginInfo($sdUser);
					return 'Found';
				}
			}

			
		}

		if($this->getUser()->isAuthenticated())
		{
			$this->form = new SdForgotPasswordForm($this->getUser()->getSdUser());
		}
		else
		{
			$this->form = new SdForgotPasswordForm();
		}
	}

	private function getLoginRedirect($request)
	{
		sfLoader::loadHelpers(array('Url'));

		$redirect = $request->getParameter('redirect');

		if(is_null($redirect) && isset($_SERVER['HTTP_REFERER']))
		{
			$redirect = $_SERVER['HTTP_REFERER'];
		}

		$badLoginRedirects = array();
		$badLoginRedirects[] = url_for('@user_signout', true);
		$badLoginRedirects[] = url_for('@user_signin', true);
		$badLoginRedirects[] = url_for('@user_register', true);
		$badLoginRedirects[] = url_for('@user_register_thankyou', true);

		if(in_array($redirect, $badLoginRedirects))
			$redirect = url_for('@member_dashboard');

		return $redirect;
	}

	private function sendLoginInfo($sdUser)
	{
		// Send user an email with their login info
		sfLoader::loadHelpers('Url');
        $activationLink = url_for('@user_signin', true);

        ProjectConfiguration::registerZend();
        $mail = new Zend_Mail();
        $mail->setBodyText(<<<EOF
{$sdUser->getFullName()},

You (or someone else) requested that we send you your ScrumDog login information.  So here it is.

Username: {$sdUser->getUsername()}
Password: {$sdUser->getPassword()}

You can log in by clicking the link below:
 
{$activationLink}
 
-The ScrumDog Team.
EOF
);

		$mail->setFrom('do-not-reply@scrumdog.com', 'ScrumDog Mail System');
        $mail->addTo($sdUser->getEmail());
        $mail->setSubject('ScrumDog Account Information for '.$sdUser->getFullName());
        $mailSent = EmailSender::send($mail);
	}
}
