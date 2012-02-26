<?php
require_once('ModernFormSchemaFormatter.class.php');

/**
 * SdLoginForm form.
 */
class SdForgotPasswordForm extends BaseSdUserForm
{
  public function configure()
  {
	$this->setWidgets(array(
      'username' => new sfWidgetFormInput(),
    ));

    //labels
    $this->widgetSchema->setLabels(array(
      'username'    => 'Username or Email <span class="required">*</span>',
    ));

    // helps

    $this->widgetSchema->setNameFormat('user[%s]');

    $this->setValidators(array(
      'username' => new sfValidatorString(array('required' => true), array('required' => 'Username or Email is required.')),
    ));

    $decorator = new ModernFormSchemaFormatter($this->widgetSchema);
    $this->widgetSchema->addFormFormatter('custom', $decorator);
    $this->widgetSchema->setFormFormatterName('custom');
  }
}
