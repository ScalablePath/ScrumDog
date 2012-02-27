<?php
/**
 * SdProjectInviteMemberForm form.
 */
class SdProjectInviteMemberForm extends BaseForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'emails'  => new sfWidgetFormInputText(array(), array('class' => 'text')),
      'project_id' => new sfWidgetFormInputHidden(),
      'current_route' => new sfWidgetFormInputHidden(),
    ));

    //labels
    $this->widgetSchema->setLabels(array(
      'emails'    => ' ',
    ));

    // helps
    $this->widgetSchema->setHelps(array(
      'emails'  => 'Separate emails with a comma',
    ));

    $this->widgetSchema->setNameFormat('invitemembers[%s]');

    $this->setValidators(array(
      'emails'    => new sfValidatorString(array('required' => true), array('required' => 'At least one email address is required.')),
      'project_id'    => new sfValidatorString(array('required' => true)),
      'current_route'    => new sfValidatorString(array('required' => true))
    ));

    // post validator

    $decorator = new ModernFormSchemaFormatter($this->widgetSchema);
    $this->widgetSchema->addFormFormatter('custom', $decorator);
    $this->widgetSchema->setFormFormatterName('custom');

  }
}
