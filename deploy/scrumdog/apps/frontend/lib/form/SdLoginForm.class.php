<?php
/**
 * SdLoginForm form.
 */
class SdLoginForm extends sfForm
{
  public function configure()
  {
	$this->setWidgets(array(
      'username' => new sfWidgetFormInput(array(), array('class' => 'text')),
      'password' => new sfWidgetFormInputPassword(array(), array('class' => 'text')),
		'remember' => new sfWidgetFormInputCheckbox(),
    ));

    //labels
    $this->widgetSchema->setLabels(array(
      'username'    => 'Username',
      'password'    => 'Passwords',
		'remember'    => 'Remember me',
    ));

    // helps

    $this->widgetSchema->setNameFormat('user[%s]');

    $this->setValidators(array(
      'username' => new sfValidatorString(array('required' => true), array('required' => 'Username is required.')),
      'password' => new sfValidatorString(array('required' => true), array('required' => 'Password is required.')),
		'remember' => new sfValidatorString(array('required' => false)),
    ));

    $decorator = new ModernFormSchemaFormatter($this->widgetSchema);
    $this->widgetSchema->addFormFormatter('custom', $decorator);
    $this->widgetSchema->setFormFormatterName('custom');
  }
}
