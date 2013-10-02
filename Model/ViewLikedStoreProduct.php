<?php
App::uses('StoreProduct', 'Model');

class ViewLikedStoreProduct extends StoreProduct
{
	public $name = 'ViewLikedStoreProduct';
	public $useTable = 'view_liked_stores_products';
}
