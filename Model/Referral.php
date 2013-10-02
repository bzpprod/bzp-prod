<?php
App::uses('AppModel', 'Model');

class Referral extends AppModel
{
	public $name = 'Referral';
	public $useTable = 'users_referral';
	public $actsAs = array('Containable');
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'invited_id'
		)
	);
}
