<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SdMessageTable extends Doctrine_Table
{
	//This function should be usable in many situations
	public static function get($filters, $sort = array(), $options = array())
	{
//var_dump($sort);
//var_dump($filters); die();
		//setup the base query
		$q = Doctrine_Query::create()
		->from('SdMessage t');

		$selectString = 't.*';
		if(isset($options['joinComments']))
		{
			$selectString .= ', mc.*';
			$q->leftJoin('t.Comments SdMessageComment mc');
		}
		if(isset($options['joinFiles']))
		{
			$selectString .= ', mf.*';
			$q->leftJoin('t.Files SdMessageFile mf');
		}
		
		$q->select($selectString);

		//add filters
		if(is_array($filters))
		{
			$filterCount=0;
			foreach($filters as $fieldName => $fieldValue)
			{
				if(trim($fieldValue)!='')
				{
					if($filterCount==0)
					{
						$whereFunc = 'where';
						$whereInFunc = 'whereIn';
					}
					else
					{
						$whereFunc = 'andWhere';
						$whereInFunc = 'andWhereIn';
					}
					switch($fieldName)
					{
						default:
							$q->{$whereFunc}("t.".$fieldName." = ?", $fieldValue);
					}
					$filterCount++;
				}
			}
		}

		//add sorts
		if(is_array($sort))
		{
			$orderString = '';
			$i=0;
			foreach($sort as $fieldName => $fieldValue)
			{
				if($fieldValue=='asc' || $fieldValue=='desc')
				{
					if($i>0)
						$orderString .= ', ';
					switch($fieldName)
					{
						default:
							$orderString .= 't.'.$fieldName.' '.$fieldValue;
					}
					$i++;
				}
			}
			if(trim($orderString)!='')
				$q->orderby($orderString);
		}

		//echo $q->getSql(); echo('<br>'); //die();
		
		$records = $q->execute();

		return $records;
	}
}