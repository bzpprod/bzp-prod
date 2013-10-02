<?php
App::uses('AppModel', 'Model');

class User extends AppModel
{
	public $name = 'User';
	public $useTable = 'users';
	public $whitelist = array('group_id', 'facebook_user_id', 'username', 'name', 'email', 'birthday', 'gender', 'location');
	public $actsAs = array('Acl' => array('type' => 'requester'), 'Containable');
	public $slugAttribute = ':id-:name';
	
	public $belongsTo = array(
		'Group' => array(
			'className' => 'Group',
			'foreignKey' => 'group_id'
		),
		
		'FacebookUser' => array(
			'className' => 'FacebookUser',
			'foreignKey' => 'facebook_user_id'
		)
	);
	
	public $hasOne = array(
		'Store' => array(
			'className' => 'Store',
			'foreignKey' => 'user_id',
			'conditions' => array('Store.is_deleted' => false),
			'dependent' => false
		)
	);
	
	public $hasMany = array(
		'Address' => array(
			'className' => 'UserAddress',
			'foreignKey' => 'foreign_key',
			'conditions' => array('Address.model' => 'User', 'Address.is_deleted' => false),
			'dependent' => false
		)
	);
	
	public $validate = array(
		'name' => array(
			'rule' => array('minLength', 3),
			'message' => 'User.validate.name',
			'allowEmpty' => false,
			'required' => true
		),
		
		'email' => array(
			'rule' => array('email', false),
			'message' => 'User.validate.email',
			'allowEmpty' => false,
			'required' => true
		),
		
		'birthday' => array(
			'rule' => array('date', 'ymd'),
			'message' => 'User.validate.birthday',
			'allowEmpty' => true,
			'required' => true,
			'on' => 'create'
		),
		
		'gender' => array(
			'rule' => array('inList', array('male', 'female')),
			'message' => 'User.validate.gender',
			'allowEmpty' => true,
			'required' => true,
			'on' => 'create'
		),
		
		'location' => array(
			'rule' => array('minLength', 3),
			'message' => 'User.validate.location',
			'allowEmpty' => true,
			'required' => true,
			'on' => 'create'
		)
	);
	
	
	public function beforeSave($options = array())
	{
		if (empty($this->id) && empty($this->data[$this->alias]['id']))
		{
			array_push($this->whitelist, 'referral_uid');
		
			while(empty($this->data[$this->alias]['referral_uid']))
			{
				$referralUid = $this->__randomString(mt_rand(6, 10));
				
				if (!$this->find('count', array('conditions' => array($this->alias . '.referral_uid' => $referralUid))))
				{
					$this->data[$this->alias]['referral_uid'] = $referralUid;
				}
			}
		}
		
		return parent::beforeSave($options);
	}
	
	public function parentNode()
	{
		if (!$this->id && empty($this->data))
		{
			return null;
		}
		
		if (!empty($this->data[$this->alias]['group_id']) && is_array($this->data[$this->alias]))
		{
			return array('Group' => array('id' => $this->data[$this->alias]['group_id']));
		}
		else		
		{
			$groupId = $this->field('group_id');
			
			if ($groupId)
			{
				return array('Group' => array('id' => $groupId));
			}
		}
		
		return null;
	}
	
	public function bindNode($user)
	{
		if (!empty($user[$this->alias]['group_id']) && is_array($user[$this->alias]))
		{
			return array('model' => 'Group', 'foreign_key' => $user[$this->alias]['group_id']);
		}
		else
		{
			if (!empty($user[$this->alias]['id']) && is_array($user[$this->alias]))
			{
				$this->id = $user[$this->alias]['id'];
			}
			else if (!empty($user[$this->alias]) && is_numeric($user[$this->alias]) && is_array($user))
			{
				$this->id = $user[$this->alias];
			}
			else if (!empty($user) && is_numeric($user))
			{
				$this->id = $user;
			}
			
			if ($this->id)
			{
				return array('model' => 'Group', 'foreign_key' => $this->field('group_id'));
			}
		}
		
		return array('model' => 'Group', 'foreign_key' => 1);	
	}
	
	
	public function generateReferralUid()
	{
		$generated = false;
		$referralUid = '';
		
		if (!empty($this->id))
		{
			$referralUid = $this->field('referral_uid');
			
			if (empty($referralUid))
			{
				while (!$generated)
				{
					$referralUid = $this->__randomString(mt_rand(6, 10));
					
					if (!$this->find('count', array('conditions' => array($this->alias . '.referral_uid' => $referralUid))))
					{
						$this->saveField('referral_uid', $referralUid);
						$generated = true;
					}
				}	
			}	
		}
		
		return $referralUid;
	}
}
