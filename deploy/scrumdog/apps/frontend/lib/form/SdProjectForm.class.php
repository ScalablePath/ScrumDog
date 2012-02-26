<?php

/**
 * SdProject form.
 *
 * @package    form
 * @subpackage SdProject
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class SdProjectForm extends BaseSdProjectForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'name'        => new sfWidgetFormInput(array(), array('class' => 'text')),
    ));

    //labels
    $this->widgetSchema->setLabels(array(
      'name'    => 'Name <span class="required">*</span>',
    ));

    // helps

    $this->widgetSchema->setNameFormat('project[%s]');

    $this->setValidators(array(
      'name' => new sfValidatorString(array('required' => true, 'max_length' => 255), array('required' => 'Project name is required.')),
    ));

	$this->validatorSchema->setOption('allow_extra_fields', true);

    $decorator = new ModernFormSchemaFormatter($this->widgetSchema);
    $this->widgetSchema->addFormFormatter('custom', $decorator);
    $this->widgetSchema->setFormFormatterName('custom');
  }
}