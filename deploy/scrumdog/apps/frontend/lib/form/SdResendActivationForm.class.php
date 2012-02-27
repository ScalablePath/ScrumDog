<?php
/**
 * ResendActivation form.
 */
class SdResendActivationForm extends BaseSdUserForm
{
  public function configure()
  {
	$this->setWidgets(array(
      'username' => new sfWidgetFormInputText(array(), array('class' => 'text')),
    ));

    //labels
    $this->widgetSchema->setLabels(array(
      'username'    => 'Username <span class="required">*</span>',
    ));

    // helps

    $this->widgetSchema->setNameFormat('resend[%s]');

    $this->setValidators(array(
      'username' => new sfValidatorString(array('required' => true), array('required' => 'Username is required.')),
    ));

    $decorator = new ModernFormSchemaFormatter($this->widgetSchema);
    $this->widgetSchema->addFormFormatter('custom', $decorator);
    $this->widgetSchema->setFormFormatterName('custom');
  }
}
