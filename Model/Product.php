<?php
App::uses('AppModel', 'Model');

class Product extends AppModel
{
	public $name = 'Product';
	public $useTable = 'products';
	public $whitelist = array('store_id', 'category_id', 'title', 'description', 'price', 'quantity', 'condition', 'exchangeable', 'is_deleted');
	public $actsAs = array('Containable');
	public $slugAttribute = ':id-:title';
	
	public $virtualFields = array(
		'quantity_available' => '(Product.quantity - Product.quantity_sold)'
	);
	
	public $belongsTo = array(
		'Store' => array(
			'className' => 'Store',
			'foreignKey' => 'store_id'
		),
		
		'Category' => array(
			'className' => 'Category',
			'foreignKey' => 'category_id'
		)
	);
	
	public $hasMany = array(
		'Photo' => array(
			'className' => 'ProductPhoto',
			'foreignKey' => 'foreign_key',
			'conditions' => array('Photo.model' => 'ProductPhoto', 'Photo.is_deleted' => false),
			'order' => array('Photo.is_default DESC', 'Photo.created ASC'),
			'dependent' => false
		)
	);
	
	public $validate = array(
		'category_id' => array(
			'rule' => array('categoryExist'),
			'message' => 'Product.validate.category_id',
			'allowEmpty' => false,
			'required' => true
		),
		
		'title' => array(
			'minLength' => array(
				'rule' => array('minLength', 5),
				'message' => 'Product.validate.title.minLength',
				'allowEmpty' => false,
				'required' => true
			),
			
			'maxLength' => array(
				'rule' => array('maxLength', 200),
				'message' => 'Product.validate.title.maxLength',
				'allowEmpty' => false,
				'required' => true
			)
		),
		
		'description' => array(
			'rule' => array('minLength', 10),
			'message' => 'Product.validate.description',
			'allowEmpty' => true,
			'required' => true
		),
		
		'price' => array(
			'rule' => array('decimal', 2),
			'message' => 'Product.validate.price',
			'allowEmpty' => false,
			'required' => true
		),
		
		'quantity' => array(
			'rule' => array('comparison', '>', 0),
			'message' => 'Product.validate.quantity',
			'allowEmpty' => false,
			'required' => true
		),
		
		'condition' => array(
			'rule' => array('inList', array('new', 'used')),
			'message' => 'Product.validate.condition',
			'allowEmpty' => false,
			'required' => true
		)
	);
	
	
	public function beforeValidate($options = array())
	{
		if (!empty($this->data['Product']['price']))
		{
			$this->data['Product']['price'] = str_replace(',', '.', $this->data['Product']['price']);
		}
		
		return parent::beforeValidate($options);
	}
	
	public function beforeSave($options = array())
	{
		if ((!empty($this->id) || !empty($this->data[$this->alias]['id'])) && isset($this->data[$this->alias]['quantity']))
		{
			$product_id = !empty($this->id) ? $this->id : $this->data[$this->alias]['id'];
			$product = $this->find('first', array('fields' => array('Product.quantity_sold'), 'conditions' => array($this->alias . '.id' => $product_id)));
			
			if (!empty($product))
			{
				$this->data[$this->alias]['quantity'] = $product[$this->alias]['quantity_sold'] + $this->data[$this->alias]['quantity'];
			}
		}
		
		return parent::beforeSave($options);
	}
	
	
	protected function categoryExist($check)
	{
		if (is_numeric($check['category_id']))
		{
			if ($this->Category->find('count', array('conditions' => array('Category.id' => $check['category_id'], 'Category.is_deleted' => false))))
			{
				return true;
			}	
		}
	
		return false;
	}
}
