<?php
class authComponents extends sfComponents
{
	public function executeRegister()
	{
		$this->form = new SdUserRegistrationForm();
	}
}