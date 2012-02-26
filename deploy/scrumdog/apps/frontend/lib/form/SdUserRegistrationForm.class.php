<?php
/**
 * SdUserRegistration form.
 */
class SdUserRegistrationForm extends BaseSdUserForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'username'  => new sfWidgetFormInput(array(), array('class' => 'text')),
      'password'  => new sfWidgetFormInputPassword(array(), array('class' => 'text')),
      'email'     => new sfWidgetFormInput(array(), array('class' => 'text')),
      'full_name' => new sfWidgetFormInput(array(), array('class' => 'text')),
	  'terms' => new sfWidgetFormInputCheckbox(),
    ));

    //labels
    $this->widgetSchema->setLabels(array(
      'username'    => 'Username',
      'password'    => 'Password',
      'email'    => 'Email',
      'full_name'    => 'Full Name',
	  'terms'    => '<span></span>'
    ));

    // helps
    $this->widgetSchema->setHelps(array(
      'username'  => 'Between 6 and 15 characters.',
      'password'  => 'At least 6 characters.',
	  'terms'    => 'I agree to the <a href="/terms" id="terms-open-popup" >terms</a>.'
    ));

    $this->widgetSchema->setNameFormat('user[%s]');

    $this->setValidators(array(
      'username'    => new sfValidatorAnd(array(
        new sfValidatorString(array('required' => true, 'min_length' => 6, 'max_length' => 15), array('required' => 'Username is required.', 'min_length' => 'The username "%value%" is too short. It must be at least %min_length% characters.', 'max_length' => 'The username "%value%" is too long. It must be at most %max_length% characters.')),
        new validatorRegexAll(array('pattern' => '/[a-zA-Z0-9_\-]/'), array('invalid' => 'Only alphanumeric characters, underscores and dashes are allowed.')),
      )),
      'email'   => new sfValidatorEmail(array('required' => true, 'max_length' => 255), array('required' => 'Email is required.')),
      'password' => new sfValidatorString(array('required' => true, 'min_length' => 6, 'max_length' => 128), array('required' => 'Password is required.', 'min_length' => 'The password "%value%" is too short. It must be at least %min_length% characters.', 'max_length' => 'The password "%value%" is too long. It must be at most %max_length% characters.')),
      'full_name' => new sfValidatorString(array('required' => true, 'max_length' => 255), array('required' => 'Full name is required.')),
	  'terms' => new sfValidatorString(array('required' => true), array('required' => 'You must agree to the terms.')),
    ));

    // post validator
    $this->validatorSchema->setPostValidator(new sfValidatorAnd(array(
      new sfValidatorDoctrineUnique(array('model'  => 'SdUser', 'column' => 'username'), array('invalid' => 'This username is already being used.')),
      new sfValidatorDoctrineUnique(array('model'  => 'SdUser', 'column' => 'email'), array('invalid' => 'A user with this email already exists.'))
    )));

    $decorator = new ModernFormSchemaFormatter($this->widgetSchema);
    $this->widgetSchema->addFormFormatter('custom', $decorator);
    $this->widgetSchema->setFormFormatterName('custom');

  }
}
