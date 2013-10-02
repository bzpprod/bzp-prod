<?php
App::uses('AppModel', 'Model');

class Category extends AppModel
{
	public $name = 'Category';
	public $useTable = 'categories';
	public $whitelist = array('title', 'description');
	public $actsAs = array('Containable', 'Tree');
	public $slugAttribute = ':id-:title';
	
	public $belongsTo = array(
		'ParentCategory' => array(
			'className'	=> 'Category',
			'foreignKey'=> 'parent_id'
		)
	);
	
	public $hasMany = array(
		'ChildCategory' => array(
			'className' => 'Category',
			'foreignKey' => 'parent_id',
			'dependent' => false
		)
	);
	
	public $hasOne = array(
		'Delivery' => array(
			'className' => 'CategoryDelivery',
			'foreignKey' => 'category_id',
			'dependent' => false
		)
	);
	
	public $validate = array(
		'parent_id' => array(			
			'rule' => array('parentCategoryExist'),
			'message' => 'Category.validate.parent_id',
			'allowEmpty' => false,
			'required' => false
		),
		
		'title' => array(			
			'rule' => array('notempty'),
			'message' => 'Category.validate.title',
			'allowEmpty' => false,
			'required' => true
		)
	);
	
	
	protected function parentCategoryExist($check)
	{
		if (is_numeric($check['parent_id']))
		{
			if ($this->find('count', array('conditions' => array('Category.id' => $check['parent_id'], 'Category.is_deleted' => false))))
			{
				return true;
			}	
		}
	
		return false;
	}
}
