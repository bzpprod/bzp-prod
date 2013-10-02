<?php
App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');

class Log extends AppModel
{
	public $name = 'Log';
	public $useTable = 'logs';
	public $actsAs = array('Containable');
	public $logEnabled = false;
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		)
	);
	
	public $validate = array(
		'ip' => array(
			'rule' => array('ip', 'IPv4'),
			'message' => 'Log.validate.ip',
			'allowEmpty' => false
		),
		
		'action' => array(
			'rule' => array('notempty'),
			'message' => 'Log.validate.action',
			'allowEmpty' => false
		)
	);
	
	
	public function beforeValidate($options = array())
	{
		$ip = CakeRequest::clientIp(false);
		
		if (strlen($ip) >= 7)
		{
			$this->data['Log']['ip'] = $ip;
		}
		
		
		if ($user = AuthComponent::user())
		{
			if (!empty($user['User']['id']))
			{
				$this->data['Log']['user_id'] = $user['User']['id'];
			}
			else if (is_numeric($user))
			{
				$this->data['Log']['user_id'] = $user;
			}
		}
		
		
		return parent::beforeValidate($options);
	}
}
