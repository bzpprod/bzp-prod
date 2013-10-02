<?php
App::uses('AppModel', 'Model');

class Qualification extends AppModel
{
	public $name = 'Qualification';
	public $useTable = 'qualifications';
	public $whitelist = array('user_id', 'transaction_id', 'delivered', 'delivered_description_id', 'qualification', 'qualification_description_id', 'testimony', 'method');
	public $actsAs = array('Containable');
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		),
		
		'Transaction' => array(
			'className' => 'Transaction',
			'foreignKey' => 'transaction_id'
		),
		
		'DeliveredDescription' => array(
			'className' => 'SystemMessage',
			'foreignKey' => 'delivered_description_id'
		)
	);
	
	public $validate = array(
		'delivered' => array(
			'rule' => array('inList', array('yes', 'no')),
			'message' => 'Qualification.validate.delivered',
			'allowEmpty' => false,
			'required' => true
		),
		
		'delivered_description_id' => array(
			'rule' => array('deliveredDescriptionExist'),
			'message' => 'Qualification.validate.delivered_description',
			'allowEmpty' => false,
			'required' => false
		),
		
		'qualification' => array(
			'rule' => array('inList', array('negative', 'neutral', 'positive')),
			'message' => 'Qualification.validate.qualification',
			'allowEmpty' => false,
			'required' => true
		),
		
		'testimony' => array(
			'rule' => array('minLength', 3),
			'message' => 'Qualification.validate.testimony',
			'allowEmpty' => true,
			'required' => true
		),
		
		'method' => array(
			'rule' => array('inList', array('purchase', 'sale')),
			'message' => 'Qualification.validate.method',
			'allowEmpty' => false,
			'required' => true
		)
	);
	
	
	protected function deliveredDescriptionExist($check)
	{
		if (is_numeric($check['delivered_description_id']))
		{
			if ($this->DeliveredDescription->find('count', array('conditions' => array('DeliveredDescription.id' => $check['delivered_description_id']))))
			{
				return true;
			}	
		}
	
		return false;
	}
}
