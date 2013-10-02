<?php
App::uses('AppModel', 'Model');

class FacebookUser extends AppModel
{
	public $name = 'FacebookUser';
	public $useTable = 'facebook_users';
	public $actsAs = array('Containable');
	
	public $hasOne = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'facebook_user_id',
			'conditions' => array('User.is_deleted' => false),
			'dependent' => false
		)
	);
}
