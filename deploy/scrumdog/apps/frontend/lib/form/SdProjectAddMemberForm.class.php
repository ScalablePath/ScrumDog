<?php
/**
 * SdProjectAddMemberForm form.
 */
class SdProjectAddMemberForm extends BaseForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'usernames'  => new sfWidgetFormInputText(array(), array('class' => 'text')),
      'project_id' => new sfWidgetFormInputHidden(),
      'current_route' => new sfWidgetFormInputHidden(),
    ));

    //labels
    $this->widgetSchema->setLabels(array(
      'usernames'    => ' ',
    ));

    // helps
    $this->widgetSchema->setHelps(array(
      'usernames'  => 'Separate usernames with a comma',
    ));

    $this->widgetSchema->setNameFormat('addmembers[%s]');

    $this->setValidators(array(
      'usernames'    => new sfValidatorString(array('required' => true), array('required' => 'At least one username is required.')),
      'project_id'    => new sfValidatorString(array('required' => true)),
      'current_route'    => new sfValidatorString(array('required' => true))
    ));

    // post validator

    $decorator = new ModernFormSchemaFormatter($this->widgetSchema);
    $this->widgetSchema->addFormFormatter('custom', $decorator);
    $this->widgetSchema->setFormFormatterName('custom');

  }
}
