<?php

/**
 * SdMessage form.
 *
 * @package    form
 * @subpackage SdMessage
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class SdMessageForm extends BaseSdMessageForm
{
  public function configure()
  {
	$project_id = sfContext::getInstance()->getRequest()->getParameter('project_id');
	//$sprint_id = sfContext::getInstance()->getRequest()->getParameter('sprint_id');
    $this->setWidgets(array(
    	'project_id'   => new sfWidgetFormInputHidden(),
		'title'        => new sfWidgetFormInputText(array(), array('class' => 'text large')),
		'content' => new sfWidgetFormTextarea(array(), array('class' => 'large')),	
    ));

    //labels
    $this->widgetSchema->setLabels(array(
      'title'    => 'Title <span class="required">*</span>',
      'content'    => 'Content',
    ));
    
    //defaults
    $this->setDefaults(array(
      'project_id'    => $project_id,
    ));

    // helps

    $this->widgetSchema->setNameFormat('message[%s]');

    $this->setValidators(array(
    	'project_id' => new sfValidatorString(array('required' => true)),
		'title' => new sfValidatorString(array('required' => true, 'max_length' => 255), array('required' => 'Message title is required.')),
		'content' => new sfValidatorString(array('required' => false)),
    ));
	
    $decorator = new ModernFormSchemaFormatter($this->widgetSchema);
    $this->widgetSchema->addFormFormatter('custom', $decorator);
    $this->widgetSchema->setFormFormatterName('custom');
  }
}