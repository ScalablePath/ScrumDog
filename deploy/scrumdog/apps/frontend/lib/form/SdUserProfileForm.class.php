<?php
/**
 * SdUserProfile form.
 */
class SdUserProfileForm extends BaseSdUserForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'full_name' => new sfWidgetFormInputText(array(), array('class' => 'text')),
		'time_zone' => new sfWidgetFormSelect(array('choices' => SdUserTable::getTimeZoneArray())),
		'gender' => new sfWidgetFormSelect(array('choices' => array('' => '(select)', 'male' => 'male', 'female' => 'female'))),
      //'email'     => new sfWidgetFormInputText(array(), array('class' => 'text')),
      'phone'     => new sfWidgetFormInputText(array(), array('class' => 'text')),
      'city'     => new sfWidgetFormInputText(array(), array('class' => 'text')),
      'state'     => new sfWidgetFormInputText(array(), array('class' => 'text')),
      'country'     => new sfWidgetFormInputText(array(), array('class' => 'text')),
      'profile_image' => new sfWidgetFormInputFile(),
    ));

    //labels
    $this->widgetSchema->setLabels(array(
      'username'    => 'Username',
      'password'    => 'Password',
      'email'    => 'Email <span class="required">*</span>',
      'full_name'    => 'Full Name <span class="required">*</span>',
		'time_zone'    => 'Time Zone <span class="required">*</span>',
      'phone'    => 'Phone Number',
      'city'    => 'City',
      'state'    => 'State/Province',
      'country'    => 'Country',
      'gender'    => 'Gender <span class="required">*</span>',
	  'profile_image' => 'Profile Image',
    ));

    // helps
    $this->widgetSchema->setHelps(array(

    ));

    $this->widgetSchema->setNameFormat('profile[%s]');

    //validators
    $this->setValidators(array(
      'full_name' => new sfValidatorString(array('required' => true, 'max_length' => 255), array('required' => 'Full name is required.')),
	  'time_zone' => new sfValidatorString(array('required' => true), array('required' => 'Time zone is required.')),
      'gender' => new sfValidatorString(array('required' => true), array('required' => 'Gender is required.')),
      'phone' => new sfValidatorString(array('required' => false)),
      'city' => new sfValidatorString(array('required' => false)),
      'state' => new sfValidatorString(array('required' => false)),
      'country' => new sfValidatorString(array('required' => false)),
      'profile_image' => new sfValidatorFile(array(
		  'required'   => false,
		  'mime_types' => 'web_images',
		)),
    ));

    // post validator

    $decorator = new ModernFormSchemaFormatter($this->widgetSchema);
    $this->widgetSchema->addFormFormatter('custom', $decorator);
    $this->widgetSchema->setFormFormatterName('custom');

  }
}
