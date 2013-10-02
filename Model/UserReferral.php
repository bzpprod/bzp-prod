<?php
App::uses('AppModel', 'Model');

class UserReferral extends AppModel
{
	public $name = 'UserReferral';
	public $useTable = 'users_referral';
	public $actsAs = array('Containable');
	
	public $belongsTo = array(
		'User' => array('className' => 'User', 'foreignKey' => 'invited_id')
	);
}
