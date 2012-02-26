<?php

/**
 * SdSprint form.
 *
 * @package    form
 * @subpackage SdSprint
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class SdSprintForm extends BaseSdSprintForm
{
  public function configure()
  {
    $this->setWidgets(array(
	  'name'        => new sfWidgetFormInput(array(), array('class' => 'text')),
      //'description' => new sfWidgetFormTextarea(array(), array('class' => 'large')),
		'start_date' => new sfWidgetFormInput(array(), array('class' => 'text date')),
		'end_date' => new sfWidgetFormInput(array(), array('class' => 'text date')),
		//'scrum_start_time' => new sfWidgetFormInput(array(), array('class' => 'text time')),
		//'scrum_time_zone_name' => new sfWidgetFormSelect(array('choices' => SdUserTable::getTimeZoneArray())),
		'scrum_days' => new sfWidgetFormSelectCheckbox(array('choices' => SdSprintTable::$scrumDaysArr)),
    ));

    //labels
    $this->widgetSchema->setLabels(array(
      'name'    => 'Name <span class="required">*</span>',
      //'description'    => 'Description',
		'start_date'    => 'Start Date <span class="required">*</span>',
		'end_date'    => 'End Date <span class="required">*</span>',
		//'scrum_start_time'    => 'Scrum Meeting Start Time <span class="required">*</span>',
		//'scrum_time_zone_name'    => 'Scrum Time Zone <span class="required">*</span>',
		'scrum_days'    => 'Sprint Work Days',
    ));

    // helps

    $this->widgetSchema->setNameFormat('sprint[%s]');

    $this->setValidators(array(
      'name' => new sfValidatorString(array('required' => true, 'max_length' => 255), array('required' => 'Sprint name is required.')),
      //'description' => new sfValidatorString(array('required' => false)),
		'start_date' => new sfValidatorDate(array('required' => true, 'date_format' => '/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/'), array('required' => 'Start Date is required.', 'bad_format' => 'Invalid date format')),
		'end_date' => new sfValidatorDate(array('required' => true, 'date_format' => '/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/'), array('required' => 'End Date is required.', 'bad_format' => 'Invalid date format')),
		//'scrum_start_time' => new sfValidatorString(array('required' => true), array('required' => 'You must choose a scrum meeting time.')),
		//'scrum_time_zone_name' => new sfValidatorString(array('required' => true)),
		'scrum_days' => new sfValidatorString(array('required' => true)),
    ));
	
	/*$this->validatorSchema->setPostValidator(
      new sfValidatorSchemaCompare($this->getObject()->getStartDate(), sfValidatorSchemaCompare::LESS_THAN, $this->getObject()->getEndDate(),
        array(),
        array('invalid' => 'The start date cannot be after the end date.')
      )
    ); */

	/*$this->validatorSchema->setPostValidator(
      new sfValidatorCallback(array('callback' => array($this, 'checkSprintFields')))
    );*/

	$this->setDefaults(array('start_date' => date('Y-m-d'), 'scrum_time_zone_name' => 'America/Los_Angeles', 'scrum_days' => array(1,2,3,4,5)));
	
    $decorator = new ModernFormSchemaFormatter($this->widgetSchema);
    $this->widgetSchema->addFormFormatter('custom', $decorator);
    $this->widgetSchema->setFormFormatterName('custom');
  }

  /*public function checkSprintFields($validator, $values)
  {
	//var_dump($values); die();

    if (strtotime($values['start_date']) > strtotime($values['end_date']))
	{
		throw new sfValidatorError($validator, 'The start date cannot be after the end date.');
	}
    return $values;
  }*/

}