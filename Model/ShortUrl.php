<?php
App::uses('AppModel', 'Model');

class ShortUrl extends AppModel
{
	public $name = 'ShortUrl';
	public $useTable = 'shorturls';
	public $actsAs = array('Containable');
	
	public $validate = array(
		'original_url' => array(
			'rule' => 'isUnique',
			'message' => 'ShortUrl.validate.original_url',
			'allowEmpty' => false,
			'required' => true
		)
	);
	
	
	public function beforeSave($options = array())
	{
		if (empty($this->id) && empty($this->data[$this->alias]['id']))
		{
			while(empty($this->data[$this->alias]['short_url_id']))
			{
				$shorUrlId = $this->__randomString(mt_rand(6, 10));
				
				if (!$this->find('count', array('conditions' => array($this->alias . '.short_url_id' => $shorUrlId))))
				{
					$this->data[$this->alias]['short_url_id'] = $shorUrlId;
				}
			}
		}
		
		return parent::beforeSave($options);
	}
}
