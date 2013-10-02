<?php
App::uses('AppModel', 'Model');

class Friend extends AppModel
{
	public $name = 'Friend';
	public $useTable = 'friends';
	public $actsAs = array('Containable');
	
	public $belongsTo = array(
		'FacebookUser' => array(
			'className' => 'FacebookUser',
			'foreignKey' => 'facebook_user_id'
		),
		
		'FacebookUserFriend' => array(
			'className' => 'FacebookUser',
			'foreignKey' => 'friend_facebook_user_id'
		)
	);
}
