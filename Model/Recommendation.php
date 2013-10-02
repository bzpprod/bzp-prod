<?php
App::uses('AppModel', 'Model');

class Recommendation extends AppModel
{
	public $name = 'Recommendation';
	public $useTable = 'recommendations';
	public $actsAs = array('Containable');
	
	public $belongsTo = array(
		'FacebookUser' => array('className' => 'FacebookUser', 'foreignKey' => 'facebook_user_id')
	);
}
