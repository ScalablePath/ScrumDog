<?php

/**
 * SdTask form.
 *
 * @package    form
 * @subpackage SdTask
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class SdTaskForm extends BaseSdTaskForm
{
    public function configure()
    {
        $project_id = sfContext::getInstance()->getRequest()->getParameter('project_id');
        $sprint_id = sfContext::getInstance()->getRequest()->getParameter('sprint_id');
        $parentTasks = $this->getParentTasks($project_id, $sprint_id);
        $this->setWidgets(array(
            'name'        => new sfWidgetFormInputText(array(), array('class' => 'text large')),
            'description' => new sfWidgetFormTextarea(array(), array('class' => 'large')),
            'business_value' => new sfWidgetFormSelect(array('choices' => SdTaskTable::$businessValueArr)),
            'priority' => new sfWidgetFormSelect(array('choices' => SdTaskTable::$priorityArr)),
            'estimated_hours' => new sfWidgetFormInputText(array(), array('class' => 'text numeric')),
            'parent_id' => new sfWidgetFormSelect(array('choices' => $parentTasks)),
            'user_id' => new sfWidgetFormSelect(array('choices' => SdProjectTable::getProjectUserArray($project_id, NULL, array('unassigned' => true)))),
            'sprint_id' => new sfWidgetFormSelect(array('choices' => SdProjectTable::getSprintArray($project_id))),
            'status' => new sfWidgetFormSelect(array('choices' => SdTaskTable::$statusArr)),
        ));

        //labels
        $this->widgetSchema->setLabels(array(
            'name'    => 'Name <span class="required">*</span>',
            'description'    => 'Description',
            'business_value'    => 'Business Value',
            'priority'    => 'Priority',
            'estimated_hours'    => 'Estimated Hours',
            'parent_id'    => 'Parent Task',
            'user_id'    => 'Assigned to',
            'sprint_id'    => 'Sprint',
            'status'    => 'Status',
        ));

        //defaults
        $this->setDefaults(array(
            'business_value'    => 1,
            'priority'    => 1,
            'status'    => 0,
        ));

        if(!is_null($sprint_id))
        {
            $this->widgetSchema->setLabels(array('estimated_hours' => 'Estimated Hours <span class="required">*</span>'));
            $this->setDefaults(array('sprint_id' => $sprint_id));
        }

        // helps

        $this->widgetSchema->setNameFormat('task[%s]');

        $this->setValidators(array(
            'name' => new sfValidatorString(array('required' => true, 'max_length' => 255), array('required' => 'Task name is required.')),
            'description' => new sfValidatorString(array('required' => false)),
            'business_value' => new sfValidatorString(),
            'priority' => new sfValidatorString(),
            'estimated_hours' => new sfValidatorNumber(array('required' => false)),
            'parent_id' => new sfValidatorString(array('required' => false)),
            'user_id' => new sfValidatorString(array('required' => false)),
            'sprint_id' => new sfValidatorString(array('required' => false)),
            'status' => new sfValidatorString(array('required' => false)),
        ));

        $this->validatorSchema->setPostValidator(
            new sfValidatorCallback(array('callback' => array($this, 'checkSprintFields')))
        );

        $decorator = new ModernFormSchemaFormatter($this->widgetSchema);
        $this->widgetSchema->addFormFormatter('custom', $decorator);
        $this->widgetSchema->setFormFormatterName('custom');
    }

    public function checkSprintFields($validator, $values)
    {
        if ($values['sprint_id']!='' && $values['estimated_hours']==''){
            throw new sfValidatorError($validator, 'If you assign a sprint, you must estimate the hours.');
        }
        return $values;
    }

    protected function getParentTasks($project_id, $sprint_id)
    {
        $filters = array();
        $parentTasks = array();
        $parentTasks[null] = '-none-';
        $filters['project_id'] = $project_id;
        $filters['status'] = 'not-completed';
        $filters['is_archived'] = 0;

        if(!is_null($sprint_id))
            $filters['sprint_id'] = $sprint_id;
        else
            $filters['sprint_id'] = 'null';

        $sort = array('task_id' => 'ASC');

        $result = SdTaskTable::getTasks($filters, $sort);
        foreach($result as $item){
            $parentTasks[$item->getId()] = $item;
        }

        return $parentTasks;
    }
}