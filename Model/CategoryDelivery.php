<?php
App::uses('AppModel', 'Model');

class CategoryDelivery extends AppModel
{
	public $name = 'CategoryDelivery';
	public $useTable = 'categories_delivery';
	public $actsAs = array('Containable');
	
	public $belongsTo = array(
		'Category' => array(
			'className' => 'Category',
			'foreignKey' => 'category_id'
		)
	);
}
