<?
class rememberFilter extends sfFilter
{
	public function execute ($filterChain)
	{
		// execute this filter only once
		if ($this->isFirstCall())
		{
			$currentUser = $this->getContext()->getUser();
			if(!$currentUser->isAuthenticated())
			{
				if ($cookie = $this->getContext()->getRequest()->getCookie('remember'))
				{
					$value = unserialize(base64_decode($cookie));
					$user = Doctrine_Query::create()
						->select('u.*')
						->from('SdUser u')
						->where("u.remember_key = '".$value[0]."'")
						->andWhere('u.id = '.$value[1])
						->fetchOne();	
					if ($user)
					{
						// sign in
						$currentUser->login($user);
					}
				}
			}
		}
		// execute next filter
		$filterChain->execute();
	}
}