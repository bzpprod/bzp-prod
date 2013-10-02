<?php
App::uses('StoreProduct', 'Model');

class ViewStoreProductCategory extends StoreProduct
{
	public $name = 'ViewStoreProductCategory';
	public $useTable = 'view_stores_products_categories';
	public $actsAs = array('Containable');
}
